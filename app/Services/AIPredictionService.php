<?php

namespace App\Services;

use App\Models\Prediction;
use App\Models\StockData;
use GuzzleHttp\Client;
use Illuminate\Support\Facades\Log;

class AIPredictionService
{
    private Client $client;

    public function __construct()
    {
        $this->client = new Client(['timeout' => 30]);
    }

    /**
     * Analisis AI menggunakan Claude API
     */
    public function analyze(string $symbol = 'BBCA.JK'): array
    {
        try {
            // Ambil data 30 hari terakhir
            $stocks = StockData::where('symbol', $symbol)
                ->recent(30)
                ->with('indicator')
                ->get();

            if ($stocks->isEmpty()) {
                return ['signal' => 'HOLD', 'confidence' => 0, 'reasoning' => 'Tidak ada data untuk dianalisis'];
            }

            $latest = $stocks->last();
            $prev   = $stocks->count() > 1 ? $stocks[$stocks->count() - 2] : $latest;

            $priceChange = round((($latest->close_price - $prev->close_price) / $prev->close_price) * 100, 2);

            // Buat ringkasan data untuk AI
            $dataSummary = $this->buildDataSummary($symbol, $stocks, $latest, $priceChange);

            // Kirim ke Claude API
            $response = $this->client->post('https://api.anthropic.com/v1/messages', [
                'headers' => [
                    'x-api-key'         => config('services.anthropic.key'),
                    'anthropic-version' => '2023-06-01',
                    'content-type'      => 'application/json',
                ],
                'json' => [
                    'model'      => config('services.anthropic.model', 'claude-sonnet-4-20250514'),
                    'max_tokens' => 1000,
                    'system'     => 'Kamu adalah analis saham profesional Indonesia yang ahli dalam analisis teknikal dan fundamental saham BBCA (Bank Central Asia). Berikan analisis yang akurat, ringkas, dan actionable dalam Bahasa Indonesia. Selalu akhiri dengan rekomendasi: BUY, SELL, atau HOLD.',
                    'messages'   => [
                        [
                            'role'    => 'user',
                            'content' => $dataSummary,
                        ],
                    ],
                ],
            ]);

            $body    = json_decode($response->getBody()->getContents(), true);
            $aiText  = $body['content'][0]['text'] ?? 'Analisis tidak tersedia';

            // Parse signal dari respons AI
            $signal     = $this->parseSignal($aiText);
            $confidence = $this->estimateConfidence($aiText);

            $result = [
                'signal'     => $signal,
                'confidence' => $confidence,
                'reasoning'  => $aiText,
            ];

            Prediction::create([
                'symbol'          => $symbol,
                'prediction_date' => now()->toDateString(),
                'target_date'     => now()->addDays(7)->toDateString(),
                'prediction_type' => 'ai',
                'signal'          => $signal,
                'confidence'      => $confidence,
                'reasoning'       => $aiText,
                'raw_data'        => $result,
            ]);

            return $result;

        } catch (\Exception $e) {
            Log::error('AI Prediction error: ' . $e->getMessage());
            return [
                'signal'     => 'HOLD',
                'confidence' => 0,
                'reasoning'  => 'Gagal mendapatkan analisis AI: ' . $e->getMessage(),
            ];
        }
    }

    private function buildDataSummary(string $symbol, $stocks, $latest, $priceChange): string
    {
        $ind = $latest->indicator;

        // Ambil 5 data terakhir
        $recentPrices = $stocks->take(-5)->map(fn($s) => [
            'tanggal' => $s->trading_date->format('d/m'),
            'close'   => number_format($s->close_price, 0, ',', '.'),
            'volume'  => number_format($s->volume, 0, ',', '.'),
        ])->values()->toArray();

        $priceTable = "Tanggal | Close | Volume\n";
        foreach ($recentPrices as $r) {
            $priceTable .= "{$r['tanggal']} | Rp{$r['close']} | {$r['volume']}\n";
        }

        return "Analisis Teknikal Saham {$symbol} - " . now()->format('d M Y') . "\n\n"
            . "=== DATA TERKINI ===\n"
            . "Harga Terakhir: Rp " . number_format($latest->close_price, 0, ',', '.') . "\n"
            . "Perubahan: {$priceChange}%\n"
            . "Volume: " . number_format($latest->volume, 0, ',', '.') . " lembar\n\n"
            . "=== MOVING AVERAGES ===\n"
            . "MA5: Rp " . number_format($ind->ma5 ?? 0, 0, ',', '.') . "\n"
            . "MA10: Rp " . number_format($ind->ma10 ?? 0, 0, ',', '.') . "\n"
            . "MA20: Rp " . number_format($ind->ma20 ?? 0, 0, ',', '.') . "\n"
            . "MA50: Rp " . number_format($ind->ma50 ?? 0, 0, ',', '.') . "\n\n"
            . "=== OSCILLATOR ===\n"
            . "RSI (14): " . ($ind->rsi ?? 'N/A') . "\n"
            . "MACD: " . ($ind->macd ?? 'N/A') . "\n"
            . "MACD Signal: " . ($ind->macd_signal ?? 'N/A') . "\n"
            . "MACD Histogram: " . ($ind->macd_histogram ?? 'N/A') . "\n\n"
            . "=== BOLLINGER BANDS ===\n"
            . "Upper: Rp " . number_format($ind->bollinger_upper ?? 0, 0, ',', '.') . "\n"
            . "Middle: Rp " . number_format($ind->bollinger_middle ?? 0, 0, ',', '.') . "\n"
            . "Lower: Rp " . number_format($ind->bollinger_lower ?? 0, 0, ',', '.') . "\n\n"
            . "=== RIWAYAT 5 HARI ===\n"
            . $priceTable . "\n"
            . "Berdasarkan data di atas, berikan:\n"
            . "1. Analisis kondisi teknikal saat ini\n"
            . "2. Potensi pergerakan 3-7 hari ke depan\n"
            . "3. Level support dan resistance\n"
            . "4. Rekomendasi: BUY / SELL / HOLD dengan alasan singkat\n"
            . "5. Target harga dan stop loss jika BUY\n";
    }

    private function parseSignal(string $text): string
    {
        $text = strtoupper($text);
        if (preg_match('/\b(REKOMENDASI|SINYAL|SIGNAL)\s*:\s*(BUY|BELI)\b/', $text)) return 'BUY';
        if (preg_match('/\b(REKOMENDASI|SINYAL|SIGNAL)\s*:\s*(SELL|JUAL)\b/', $text)) return 'SELL';
        if (preg_match('/\bREKOMENDASI\s*:\s*HOLD\b/', $text)) return 'HOLD';
        if (substr_count($text, 'BUY') > substr_count($text, 'SELL')) return 'BUY';
        if (substr_count($text, 'SELL') > substr_count($text, 'BUY')) return 'SELL';
        return 'HOLD';
    }

    private function estimateConfidence(string $text): float
    {
        $keywords = ['sangat', 'kuat', 'jelas', 'definitif', 'pasti', 'strong'];
        $weakWords = ['mungkin', 'kemungkinan', 'bisa', 'potensi', 'risiko'];
        $score = 60;
        foreach ($keywords as $k) { if (stripos($text, $k) !== false) $score += 5; }
        foreach ($weakWords as $k) { if (stripos($text, $k) !== false) $score -= 3; }
        return min(90, max(40, $score));
    }
}