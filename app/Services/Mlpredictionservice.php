<?php

namespace App\Services;

use App\Models\Prediction;
use App\Models\StockData;
use GuzzleHttp\Client;
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
     * Kirim data ke Python ML service dan dapatkan prediksi
     */
    public function analyze(): array
    {
        try {
            if (!config('services.ml_service.enabled', false)) {
                return ['signal' => 'HOLD', 'confidence' => 0, 'reasoning' => 'ML Service tidak aktif'];
            }

            // Ambil 60 hari data terakhir
            $stocks = StockData::where('symbol', 'BBCA.JK')
                ->recent(60)
                ->with('indicator')
                ->get();

            if ($stocks->count() < 30) {
                return ['signal' => 'HOLD', 'confidence' => 0, 'reasoning' => 'Data kurang dari 30 hari, ML tidak bisa berjalan'];
            }

            // Format data untuk Python
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

            $response = $this->client->post('/predict', [
                'json' => ['data' => $payload],
            ]);

            $result = json_decode($response->getBody()->getContents(), true);

            $signal     = strtoupper($result['signal'] ?? 'HOLD');
            $confidence = (float) ($result['confidence'] ?? 50);
            $reasoning  = $result['reasoning'] ?? 'Prediksi dari ML model (Random Forest + LSTM)';
            $predicted  = $result['predicted_price'] ?? null;

            Prediction::create([
                'symbol'          => 'BBCA.JK',
                'prediction_date' => now()->toDateString(),
                'target_date'     => now()->addDays(5)->toDateString(),
                'prediction_type' => 'ml',
                'signal'          => in_array($signal, ['BUY', 'SELL', 'HOLD']) ? $signal : 'HOLD',
                'confidence'      => $confidence,
                'predicted_price' => $predicted,
                'reasoning'       => $reasoning,
                'raw_data'        => $result,
            ]);

            return [
                'signal'          => $signal,
                'confidence'      => $confidence,
                'predicted_price' => $predicted,
                'reasoning'       => $reasoning,
            ];

        } catch (\Exception $e) {
            Log::warning('ML Service tidak bisa dihubungi: ' . $e->getMessage());
            return [
                'signal'     => 'HOLD',
                'confidence' => 0,
                'reasoning'  => 'ML Service offline. Pastikan python/ml_service.py sudah dijalankan.',
            ];
        }
    }

    /**
     * Gabungkan semua prediksi jadi satu rekomendasi final
     */
    public function combinedAnalysis(array $ruleBased, array $ml, array $ai): array
    {
        $scores = ['BUY' => 0, 'SELL' => 0, 'HOLD' => 0];
        $weights = [
            'rule_based' => 0.3,
            'ml'         => 0.35,
            'ai'         => 0.35,
        ];

        foreach ([$ruleBased => 'rule_based', $ml => 'ml', $ai => 'ai'] as $pred => $type) {
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
            'symbol'          => 'BBCA.JK',
            'prediction_date' => now()->toDateString(),
            'target_date'     => now()->addDays(5)->toDateString(),
            'prediction_type' => 'combined',
            'signal'          => $finalSignal,
            'confidence'      => $finalConfidence,
            'reasoning'       => $reasoning,
            'raw_data'        => compact('ruleBased', 'ml', 'ai', 'scores'),
        ]);

        return ['signal' => $finalSignal, 'confidence' => $finalConfidence, 'reasoning' => $reasoning];
    }
}