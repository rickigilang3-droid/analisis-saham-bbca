<?php

namespace App\Services;

use App\Models\Prediction;
use App\Models\StockData;
use App\Notifications\StrongBuySignal;
use GuzzleHttp\Client;
use Illuminate\Notifications\Notification;
use Illuminate\Support\Facades\Log;

class MLPredictionService
{
    private Client $client;

    public function __construct()
    {
        $this->client = new Client([
            'base_uri' => config('services.ml_service.url', 'http://127.0.0.1:5001'),
            'timeout'  => 20,
        ]);
    }

    /**
     * Kirim data ke Python ML service dan dapatkan prediksi.
     * Jika service tidak tersedia, gunakan fallback lokal agar fitur tetap bisa berfungsi.
     */
    public function analyze(string $symbol = 'BBCA.JK'): array
    {
        $symbol = $this->normalizeSymbol($symbol);

        $stocks = StockData::where('symbol', $symbol)
            ->recent(60)
            ->with('indicator')
            ->get();

        if ($stocks->count() < 30) {
            return ['signal' => 'HOLD', 'confidence' => 0, 'reasoning' => 'Data kurang dari 30 hari, ML tidak bisa berjalan'];
        }

        $payload = $stocks->map(fn($s) => [
            'date'             => $s->trading_date->format('Y-m-d'),
            'open'             => (float) $s->open_price,
            'high'             => (float) $s->high_price,
            'low'              => (float) $s->low_price,
            'close'            => (float) $s->close_price,
            'volume'           => (int) $s->volume,
            'ma5'              => (float) ($s->indicator->ma5 ?? 0),
            'ma20'             => (float) ($s->indicator->ma20 ?? 0),
            'rsi'              => (float) ($s->indicator->rsi ?? 50),
            'macd'             => (float) ($s->indicator->macd ?? 0),
            'bollinger_upper'  => (float) ($s->indicator->bollinger_upper ?? 0),
            'bollinger_lower'  => (float) ($s->indicator->bollinger_lower ?? 0),
        ])->values()->toArray();

        try {
            if (!config('services.ml_service.enabled', false)) {
                return $this->buildFallbackPrediction($symbol, $stocks, $payload, 'ML Service tidak aktif, fallback lokal digunakan.');
            }

            $response = $this->client->post('/predict', [
                'json' => ['data' => $payload],
            ]);

            $result = json_decode($response->getBody()->getContents(), true);
            $signal = strtoupper($result['signal'] ?? 'HOLD');
            $confidence = (float) ($result['confidence'] ?? 50);
            $reasoning = $result['reasoning'] ?? 'Prediksi dari ML model (Random Forest + LSTM)';
            $predicted = $result['predicted_price'] ?? null;

            if (!in_array($signal, ['BUY', 'SELL', 'HOLD'])) {
                return $this->buildFallbackPrediction($symbol, $stocks, $payload, 'Respons ML tidak valid, fallback lokal digunakan.');
            }

            $result = [
                'signal' => $signal,
                'confidence' => $confidence,
                'predicted_price' => $predicted,
                'reasoning' => $reasoning,
            ];

            $this->persistPrediction($symbol, $result);
            return $result;
        } catch (\Exception $e) {
            Log::warning('ML Service tidak bisa dihubungi: ' . $e->getMessage());
            return $this->buildFallbackPrediction($symbol, $stocks, $payload, 'ML Service offline. Fallback lokal digunakan.');
        }
    }

    private function buildFallbackPrediction(string $symbol, $stocks, array $payload, string $fallbackReason): array
    {
        $latest = $stocks->last();
        $prev = $stocks->count() > 1 ? $stocks[$stocks->count() - 2] : $latest;
        $price = (float) ($latest->close_price ?? 0);
        $prevClose = (float) ($prev->close_price ?? $price);
        $changePct = $prevClose > 0 ? (($price - $prevClose) / $prevClose) * 100 : 0;

        $ind = $latest->indicator;
        $ma5 = (float) ($ind->ma5 ?? 0);
        $ma20 = (float) ($ind->ma20 ?? 0);
        $ma50 = (float) ($ind->ma50 ?? 0);
        $rsi = (float) ($ind->rsi ?? 50);
        $macdHist = (float) ($ind->macd_histogram ?? 0);

        $score = 0;
        if ($ma5 > $ma20) $score += 1.2;
        if ($price > $ma50) $score += 1.1;
        if ($macdHist > 0) $score += 0.8;
        if ($rsi >= 50 && $rsi <= 70) $score += 0.6;
        if ($rsi > 70) $score -= 0.3;
        if ($changePct >= 0.25) $score += 0.7;
        if ($changePct <= -0.25) $score -= 0.7;

        $signal = $score >= 1.8 ? 'BUY' : ($score <= -1.8 ? 'SELL' : 'HOLD');
        $confidence = min(90, max(55, round(abs($score) * 18 + 55, 1)));
        $delta = $score >= 1.8 ? 0.35 : ($score <= -1.8 ? -0.35 : 0);
        $predicted = round($price * (1 + $delta), 2);

        $result = [
            'signal' => $signal,
            'confidence' => $confidence,
            'predicted_price' => $predicted,
            'reasoning' => "Fallback lokal ML aktif. {$fallbackReason} Momentum: {$changePct}% | MA5/MA20: {$ma5}/{$ma20} | RSI: {$rsi} | MACD Histogram: {$macdHist}.",
        ];

        $this->persistPrediction($symbol, $result);
        return $result;
    }

    private function persistPrediction(string $symbol, array $result): void
    {
        Prediction::create([
            'symbol' => $symbol,
            'prediction_date' => now()->toDateString(),
            'target_date' => now()->addDays(5)->toDateString(),
            'prediction_type' => 'ml',
            'signal' => in_array($result['signal'] ?? 'HOLD', ['BUY', 'SELL', 'HOLD']) ? strtoupper($result['signal']) : 'HOLD',
            'confidence' => (float) ($result['confidence'] ?? 0),
            'predicted_price' => $result['predicted_price'] ?? null,
            'reasoning' => $result['reasoning'] ?? 'Prediksi ML',
            'raw_data' => $result,
        ]);
    }

    /**
     * Gabungkan semua prediksi jadi satu rekomendasi final
     */
    public function combinedAnalysis(string $symbol, array $ruleBased, array $ml, array $ai): array
    {
        $scores = ['BUY' => 0, 'SELL' => 0, 'HOLD' => 0];
        $weights = [
            'rule_based' => 0.3,
            'ml'         => 0.35,
            'ai'         => 0.35,
        ];

        $allPredictions = [
    'rule_based' => $ruleBased,
    'ml'         => $ml,
    'ai'         => $ai,
];

foreach ($allPredictions as $type => $pred) {
    $signal     = $pred['signal'] ?? 'HOLD';
    $confidence = ($pred['confidence'] ?? 50) / 100;
    $weight     = $weights[$type];
    $scores[$signal] += $confidence * $weight;
}
        arsort($scores);
        $finalSignal     = array_key_first($scores);
        $finalConfidence = round($scores[$finalSignal] * 100, 1);

        $reasoning = "=== ANALISIS GABUNGAN ===\n\n"
            . "🔵 Rule-Based: {$ruleBased['signal']} ({$ruleBased['confidence']}% keyakinan)\n"
            . "🟠 ML Model: {$ml['signal']} ({$ml['confidence']}% keyakinan)\n"
            . "🟣 AI Claude: {$ai['signal']} ({$ai['confidence']}% keyakinan)\n\n"
            . "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━\n"
            . "✅ REKOMENDASI FINAL: {$finalSignal}\n"
            . "📊 Keyakinan: {$finalConfidence}%\n";

        Prediction::create([
            'symbol'          => $symbol,
            'prediction_date' => now()->toDateString(),
            'target_date'     => now()->addDays(5)->toDateString(),
            'prediction_type' => 'combined',
            'signal'          => $finalSignal,
            'confidence'      => $finalConfidence,
            'reasoning'       => $reasoning,
            'raw_data'        => compact('ruleBased', 'ml', 'ai', 'scores'),
        ]);

        if ($finalSignal === 'BUY' && $finalConfidence >= 75.0) {
            Notification::route('telegram', env('TELEGRAM_CHAT_ID'))
                ->notify(new StrongBuySignal($symbol, $finalSignal, $finalConfidence, $reasoning));
        }

        return ['signal' => $finalSignal, 'confidence' => $finalConfidence, 'reasoning' => $reasoning];
    }

    private function normalizeSymbol(string $symbol): string
    {
        $symbol = strtoupper(trim($symbol));

        return str_ends_with($symbol, '.JK') ? $symbol : $symbol . '.JK';
    }
}