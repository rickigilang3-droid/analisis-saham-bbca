<?php

namespace App\Services;

use App\Models\Prediction;
use App\Models\StockData;
use Illuminate\Support\Collection;

class BacktestingService
{
    /**
     * Jalankan proses backtesting: isi harga aktual untuk prediksi yang sudah jatuh tempo.
     *
     * @return array Statistik proses yang dijalankan.
     */
    public function run(): array
    {
        $unverifiedPredictions = Prediction::whereNull('actual_price')
            ->where('target_date', '<=', now()->toDateString())
            ->get();

        if ($unverifiedPredictions->isEmpty()) {
            return ['processed' => 0, 'message' => 'Tidak ada prediksi baru untuk diverifikasi.'];
        }

        $processedCount = 0;
        foreach ($unverifiedPredictions as $prediction) {
            // Cari harga penutupan aktual pada tanggal target prediksi
            $actualPrice = StockData::where('symbol', $prediction->symbol)
                ->where('trading_date', $prediction->target_date)
                ->value('close_price');

            if ($actualPrice !== null) {
                $prediction->actual_price = $actualPrice;
                $prediction->save();
                $processedCount++;
            }
        }

        return [
            'processed' => $processedCount,
            'message' => "Berhasil memverifikasi {$processedCount} dari {$unverifiedPredictions->count()} prediksi.",
        ];
    }

    /**
     * Hitung dan kembalikan metrik akurasi dari semua prediksi yang sudah terverifikasi.
     *
     * @param string $symbol Simbol saham yang akan dianalisis.
     * @return array Metrik akurasi per model dan keseluruhan.
     */
    public function getMetrics(string $symbol = 'BBCA.JK'): array
    {
        // Ambil semua prediksi yang sudah punya harga aktual
        $verifiedPredictions = Prediction::where('symbol', $symbol)
            ->whereNotNull('actual_price')
            ->get();

        if ($verifiedPredictions->isEmpty()) {
            return ['overall' => $this->getEmptyMetrics(), 'by_type' => []];
        }

        // Kelompokkan berdasarkan tipe prediksi
        $grouped = $verifiedPredictions->groupBy('prediction_type');

        $metricsByType = [];
        foreach ($grouped as $type => $predictions) {
            $metricsByType[$type] = $this->calculateMetricsForGroup($predictions, $symbol);
        }

        // Hitung metrik keseluruhan
        $overallMetrics = $this->calculateMetricsForGroup($verifiedPredictions, $symbol);

        return [
            'overall' => $overallMetrics,
            'by_type' => $metricsByType,
        ];
    }

    /**
     * Helper untuk menghitung metrik untuk sekelompok prediksi.
     */
    private function calculateMetricsForGroup(Collection $predictions, string $symbol): array
    {
        $total = $predictions->count();
        $correctSignals = 0;
        $totalPriceError = 0;
        $pricePredCount = 0;

        foreach ($predictions as $p) {
            // Ambil harga penutupan pada hari prediksi dibuat
            $startPrice = (float) StockData::where('symbol', $symbol)
                ->where('trading_date', $p->prediction_date)
                ->value('close_price');

            if ($startPrice === 0.0) continue;

            $actualChange = ((float) $p->actual_price - $startPrice) / $startPrice * 100;

            // Cek akurasi sinyal
            $isCorrect = match ($p->signal) {
                'BUY'  => $actualChange > 0.25, // Dianggap benar jika harga naik > 0.25%
                'SELL' => $actualChange < -0.25, // Dianggap benar jika harga turun > 0.25%
                'HOLD' => abs($actualChange) <= 0.5, // Dianggap benar jika perubahan kecil
                default => false,
            };

            if ($isCorrect) {
                $correctSignals++;
            }

            // Hitung error harga jika ada prediksi harga
            if ($p->predicted_price > 0) {
                $totalPriceError += abs(((float) $p->actual_price - (float) $p->predicted_price) / (float) $p->actual_price);
                $pricePredCount++;
            }
        }

        return [
            'count' => $total,
            'signal_accuracy' => $total > 0 ? round(($correctSignals / $total) * 100, 1) : 0,
            'avg_price_error_pct' => $pricePredCount > 0 ? round(($totalPriceError / $pricePredCount) * 100, 2) : null,
        ];
    }

    private function getEmptyMetrics(): array
    {
        return [
            'count' => 0,
            'signal_accuracy' => 0,
            'avg_price_error_pct' => null,
        ];
    }
}