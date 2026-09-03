<?php

namespace App\Services;

use App\Models\StockData;
use App\Models\StockIndicator;
use GuzzleHttp\Client;
use Illuminate\Support\Facades\Log;

class StockDataService
{
    private Client $client;
    private string $symbol = 'BBCA.JK';

    public function __construct()
    {
        $this->client = new Client(['timeout' => 15]);
    }

    /**
     * Fetch data saham dari Yahoo Finance (gratis, tanpa API key)
     */
    public function fetchFromYahoo(int $days = 90, string $symbol = 'BBCA.JK'): array
    {
        $this->symbol = $this->normalizeSymbol($symbol);

        try {
            $period2 = time();
            $period1 = $period2 - ($days * 86400);

            $url = "https://query1.finance.yahoo.com/v8/finance/chart/{$this->symbol}";
            $response = $this->client->get($url, [
                'query' => [
                    'period1'  => $period1,
                    'period2'  => $period2,
                    'interval' => '1d',
                    'events'   => 'history',
                ],
                'headers' => [
                    'User-Agent' => 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36',
                ],
            ]);

            $data = json_decode($response->getBody()->getContents(), true);
            $result = $data['chart']['result'][0] ?? null;

            if (!$result) {
                throw new \Exception('Data tidak ditemukan dari Yahoo Finance');
            }

            $timestamps = $result['timestamp'];
            $ohlcv       = $result['indicators']['quote'][0];
            $adjClose    = $result['indicators']['adjclose'][0]['adjclose'] ?? [];

            $saved = 0;
            foreach ($timestamps as $i => $ts) {
                $date = date('Y-m-d', $ts);
                $close = $ohlcv['close'][$i] ?? null;
                if (!$close) continue;

                StockData::updateOrCreate(
                    ['symbol' => $this->symbol, 'trading_date' => $date],
                    [
                        'open_price'  => round($ohlcv['open'][$i] ?? $close, 2),
                        'high_price'  => round($ohlcv['high'][$i] ?? $close, 2),
                        'low_price'   => round($ohlcv['low'][$i] ?? $close, 2),
                        'close_price' => round($close, 2),
                        'volume'      => $ohlcv['volume'][$i] ?? 0,
                        'adj_close'   => round($adjClose[$i] ?? $close, 2),
                    ]
                );
                $saved++;
            }

            $this->calculateIndicators();
            return ['success' => true, 'saved' => $saved, 'message' => "Berhasil simpan $saved data"];

        } catch (\Exception $e) {
            Log::error('Yahoo Finance fetch error: ' . $e->getMessage());
            return ['success' => false, 'message' => $e->getMessage()];
        }
    }

    /**
     * Hitung technical indicators (MA, RSI, MACD, Bollinger Bands)
     */
    public function calculateIndicators(): void
    {
        $stocks = StockData::where('symbol', $this->symbol)
            ->orderBy('trading_date')
            ->get();

        if ($stocks->count() < 20) return;

        $closes = $stocks->pluck('close_price')->map(fn($v) => (float)$v)->toArray();
        $n      = count($closes);

        foreach ($stocks as $i => $stock) {
            $indicators = [];

            // Moving Averages
            foreach ([5, 10, 20, 50] as $period) {
                $key = "ma{$period}";
                if ($i >= $period - 1) {
                    $slice = array_slice($closes, $i - $period + 1, $period);
                    $indicators[$key] = round(array_sum($slice) / $period, 2);
                }
            }

            // RSI (14 period)
            if ($i >= 14) {
                $changes = [];
                for ($j = $i - 13; $j <= $i; $j++) {
                    $changes[] = $closes[$j] - $closes[$j - 1];
                }
                $gains  = array_filter($changes, fn($c) => $c > 0);
                $losses = array_filter($changes, fn($c) => $c < 0);
                $avgGain = count($gains) > 0 ? array_sum($gains) / 14 : 0;
                $avgLoss = count($losses) > 0 ? abs(array_sum($losses)) / 14 : 0;
                $rs = $avgLoss > 0 ? $avgGain / $avgLoss : 100;
                $indicators['rsi'] = round(100 - (100 / (1 + $rs)), 4);
            }

            // MACD (12, 26, 9)
            if ($i >= 25) {
                $ema12 = $this->ema(array_slice($closes, 0, $i + 1), 12);
                $ema26 = $this->ema(array_slice($closes, 0, $i + 1), 26);
                $macd  = $ema12 - $ema26;
                $indicators['macd'] = round($macd, 4);

                if ($i >= 33) {
                    $macdValues = [];
                    for ($j = max(0, $i - 8); $j <= $i; $j++) {
                        $e12 = $this->ema(array_slice($closes, 0, $j + 1), 12);
                        $e26 = $this->ema(array_slice($closes, 0, $j + 1), 26);
                        $macdValues[] = $e12 - $e26;
                    }
                    $signal = $this->ema($macdValues, min(9, count($macdValues)));
                    $indicators['macd_signal']    = round($signal, 4);
                    $indicators['macd_histogram'] = round($macd - $signal, 4);
                }
            }

            // Bollinger Bands (20, 2 std)
            if ($i >= 19) {
                $slice = array_slice($closes, $i - 19, 20);
                $mean  = array_sum($slice) / 20;
                $variance = array_sum(array_map(fn($v) => pow($v - $mean, 2), $slice)) / 20;
                $std  = sqrt($variance);
                $indicators['bollinger_middle'] = round($mean, 2);
                $indicators['bollinger_upper']  = round($mean + 2 * $std, 2);
                $indicators['bollinger_lower']  = round($mean - 2 * $std, 2);
            }

            if (!empty($indicators)) {
                StockIndicator::updateOrCreate(
                    ['stock_data_id' => $stock->id],
                    $indicators
                );
            }
        }
    }

    private function ema(array $data, int $period): float
    {
        if (empty($data) || $period <= 0) return 0;
        $k     = 2 / ($period + 1);
        $ema   = $data[0];
        foreach (array_slice($data, 1) as $v) {
            $ema = $v * $k + $ema * (1 - $k);
        }
        return $ema;
    }

    public function getLatestPrice(string $symbol = 'BBCA.JK'): ?array
    {
        $this->symbol = $this->normalizeSymbol($symbol);

        $latest = StockData::where('symbol', $this->symbol)
            ->orderBy('trading_date', 'desc')
            ->with('indicator')
            ->first();

        return $latest ? $latest->toArray() : null;
    }

    public function getHistoryForChart(int $days = 60, string $symbol = 'BBCA.JK'): array
    {
        $this->symbol = $this->normalizeSymbol($symbol);

        return StockData::where('symbol', $this->symbol)
            ->recent($days)
            ->with('indicator')
            ->get()
            ->toArray();
    }

    private function normalizeSymbol(string $symbol): string
    {
        $symbol = strtoupper(trim($symbol));

        if ($symbol === '' || !str_contains($symbol, 'BBCA')) {
            return 'BBCA.JK';
        }

        return str_ends_with($symbol, '.JK') ? $symbol : $symbol . '.JK';
    }
}