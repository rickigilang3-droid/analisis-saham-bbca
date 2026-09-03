<?php

namespace App\Console\Commands;

use App\Models\StockData;
use App\Models\StockIndicator;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class FetchStockData extends Command
{
    protected $signature = 'stock:fetch {symbol=BBCA.JK} {--range=2y} {--interval=1d}';
    protected $description = 'Ambil data historis harga saham dari Yahoo Finance dan hitung indikator teknikal';

    public function handle(): int
    {
        $symbol   = strtoupper($this->argument('symbol'));
        $range    = $this->option('range');
        $interval = $this->option('interval');

        $this->info("Mengambil data historis untuk {$symbol} (range={$range}, interval={$interval})...");

        $url = "https://query1.finance.yahoo.com/v8/finance/chart/{$symbol}";

        try {
            $response = Http::withHeaders([
                'User-Agent' => 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36',
            ])->timeout(20)->get($url, [
                'range'    => $range,
                'interval' => $interval,
            ]);

            if (! $response->successful()) {
                $this->error('Gagal mengambil data dari Yahoo Finance. Status: ' . $response->status());
                return self::FAILURE;
            }

            $data = $response->json();
            $result = $data['chart']['result'][0] ?? null;

            if (! $result) {
                $this->error('Response Yahoo Finance kosong / format tidak dikenali.');
                return self::FAILURE;
            }

            $timestamps = $result['timestamp'] ?? [];
            $quote      = $result['indicators']['quote'][0] ?? [];
            $adjclose   = $result['indicators']['adjclose'][0]['adjclose'] ?? [];

            $opens   = $quote['open'] ?? [];
            $highs   = $quote['high'] ?? [];
            $lows    = $quote['low'] ?? [];
            $closes  = $quote['close'] ?? [];
            $volumes = $quote['volume'] ?? [];

            if (empty($timestamps) || empty($closes)) {
                $this->error('Tidak ada data candle yang ditemukan.');
                return self::FAILURE;
            }

            $this->info('Ditemukan ' . count($timestamps) . ' hari data. Menyimpan ke database...');

            $rows = [];
            for ($i = 0; $i < count($timestamps); $i++) {
                // Skip hari dengan data null (hari libur/bolong dari Yahoo)
                if (! isset($closes[$i]) || $closes[$i] === null) {
                    continue;
                }

                $rows[] = [
                    'date'   => date('Y-m-d', $timestamps[$i]),
                    'open'   => $opens[$i] ?? $closes[$i],
                    'high'   => $highs[$i] ?? $closes[$i],
                    'low'    => $lows[$i] ?? $closes[$i],
                    'close'  => $closes[$i],
                    'volume' => $volumes[$i] ?? 0,
                    'adjclose' => $adjclose[$i] ?? $closes[$i],
                ];
            }

            if (empty($rows)) {
                $this->error('Semua data candle kosong (null) setelah filtering.');
                return self::FAILURE;
            }

            // Urutkan berdasarkan tanggal ascending, biar perhitungan indikator berurutan
            usort($rows, fn($a, $b) => strcmp($a['date'], $b['date']));

            $stockDataIds = []; // index => stock_data id, sejajar dengan $rows
            $bar = $this->output->createProgressBar(count($rows));
            $bar->start();

            foreach ($rows as $idx => $row) {
                $stock = StockData::updateOrCreate(
                    ['symbol' => $symbol, 'trading_date' => $row['date']],
                    [
                        'open_price'  => $row['open'],
                        'high_price'  => $row['high'],
                        'low_price'   => $row['low'],
                        'close_price' => $row['close'],
                        'volume'      => $row['volume'],
                        'adj_close'   => $row['adjclose'],
                    ]
                );
                $stockDataIds[$idx] = $stock->id;
                $bar->advance();
            }
            $bar->finish();
            $this->newLine();

            $this->info('Menghitung indikator teknikal (MA, RSI, MACD, Bollinger Bands)...');
            $closesOnly = array_column($rows, 'close');

            // --- Hitung EMA 12 & 26 penuh (dibutuhkan buat MACD) ---
            $ema12 = $this->emaSeries($closesOnly, 12);
            $ema26 = $this->emaSeries($closesOnly, 26);

            $macdLine = [];
            foreach ($closesOnly as $i => $c) {
                $macdLine[$i] = ($ema12[$i] !== null && $ema26[$i] !== null)
                    ? $ema12[$i] - $ema26[$i]
                    : null;
            }
            $macdSignal = $this->emaSeries($macdLine, 9);

            $bar2 = $this->output->createProgressBar(count($rows));
            $bar2->start();

            foreach ($rows as $i => $row) {
                $ma5  = $this->sma($closesOnly, $i, 5);
                $ma10 = $this->sma($closesOnly, $i, 10);
                $ma20 = $this->sma($closesOnly, $i, 20);
                $ma50 = $this->sma($closesOnly, $i, 50);
                $rsi  = $this->rsi($closesOnly, $i, 14);

                $macd = $macdLine[$i] ?? null;
                $signal = $macdSignal[$i] ?? null;
                $hist = ($macd !== null && $signal !== null) ? $macd - $signal : null;

                $bbMiddle = $ma20;
                $bbStd = $this->stdDev($closesOnly, $i, 20);
                $bbUpper = ($bbMiddle !== null && $bbStd !== null) ? $bbMiddle + (2 * $bbStd) : null;
                $bbLower = ($bbMiddle !== null && $bbStd !== null) ? $bbMiddle - (2 * $bbStd) : null;

                StockIndicator::updateOrCreate(
                    ['stock_data_id' => $stockDataIds[$i]],
                    [
                        'ma5' => $ma5, 'ma10' => $ma10, 'ma20' => $ma20, 'ma50' => $ma50,
                        'rsi' => $rsi,
                        'macd' => $macd, 'macd_signal' => $signal, 'macd_histogram' => $hist,
                        'bollinger_upper' => $bbUpper, 'bollinger_middle' => $bbMiddle, 'bollinger_lower' => $bbLower,
                    ]
                );
                $bar2->advance();
            }
            $bar2->finish();
            $this->newLine();

            $this->info("Selesai! {$symbol}: " . count($rows) . " hari data tersimpan dengan indikator lengkap.");
            return self::SUCCESS;

        } catch (\Exception $e) {
            Log::error('FetchStockData error: ' . $e->getMessage());
            $this->error('Terjadi error: ' . $e->getMessage());
            return self::FAILURE;
        }
    }

    private function sma(array $closes, int $i, int $period): ?float
    {
        if ($i + 1 < $period) return null;
        $slice = array_slice($closes, $i - $period + 1, $period);
        return round(array_sum($slice) / $period, 2);
    }

    private function stdDev(array $closes, int $i, int $period): ?float
    {
        if ($i + 1 < $period) return null;
        $slice = array_slice($closes, $i - $period + 1, $period);
        $mean = array_sum($slice) / $period;
        $variance = array_sum(array_map(fn($x) => ($x - $mean) ** 2, $slice)) / $period;
        return round(sqrt($variance), 2);
    }

    private function emaSeries(array $values, int $period): array
    {
        $ema = array_fill(0, count($values), null);
        $k = 2 / ($period + 1);
        $seed = null;
        $seedIndex = null;

        foreach ($values as $i => $v) {
            if ($v === null) continue;
            if ($seed === null) {
                // Cari titik awal: rata-rata sederhana dari `period` nilai pertama yang valid
                if ($i + 1 >= $period) {
                    $window = array_slice($values, $i - $period + 1, $period);
                    if (! in_array(null, $window, true)) {
                        $seed = array_sum($window) / $period;
                        $seedIndex = $i;
                        $ema[$i] = round($seed, 4);
                    }
                }
                continue;
            }
            $ema[$i] = round(($v - $ema[$i - 1]) * $k + $ema[$i - 1], 4);
        }

        return $ema;
    }

    private function rsi(array $closes, int $i, int $period = 14): ?float
    {
        if ($i + 1 < $period + 1) return null;

        $gains = 0;
        $losses = 0;
        for ($j = $i - $period + 1; $j <= $i; $j++) {
            $diff = $closes[$j] - $closes[$j - 1];
            if ($diff >= 0) {
                $gains += $diff;
            } else {
                $losses += abs($diff);
            }
        }
        $avgGain = $gains / $period;
        $avgLoss = $losses / $period;

        if ($avgLoss == 0) return 100.0;
        $rs = $avgGain / $avgLoss;
        return round(100 - (100 / (1 + $rs)), 4);
    }
}