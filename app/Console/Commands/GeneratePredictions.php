<?php

namespace App\Console\Commands;

use App\Services\AIPredictionService;
use App\Services\MLPredictionService;
use App\Services\RuleBasedPredictionService;
use Illuminate\Console\Command;

class GeneratePredictions extends Command
{
    protected $signature = 'predict:generate {symbol=BBCA.JK}';
    protected $description = 'Jalankan Rule-Based, ML, dan AI prediction, lalu simpan hasil gabungannya';

    public function handle(
        RuleBasedPredictionService $ruleBasedService,
        MLPredictionService $mlService,
        AIPredictionService $aiService
    ): int {
        $symbol = strtoupper($this->argument('symbol'));

        $this->info("Menjalankan prediksi untuk {$symbol}...");

        $this->line('→ Rule-Based...');
        $ruleBased = $ruleBasedService->analyze($symbol);
        $this->comment("  Signal: {$ruleBased['signal']} | Confidence: {$ruleBased['confidence']}%");

        $this->line('→ ML...');
        $ml = $mlService->analyze($symbol);
        $this->comment("  Signal: {$ml['signal']} | Confidence: {$ml['confidence']}%");

        $this->line('→ AI (Claude)...');
        $ai = $aiService->analyze($symbol);
        $this->comment("  Signal: {$ai['signal']} | Confidence: {$ai['confidence']}%");
        if (str_contains($ai['reasoning'] ?? '', 'Gagal mendapatkan analisis AI')) {
            $this->warn('  Catatan: AI prediction gagal (kemungkinan API key belum diset di .env: ANTHROPIC_API_KEY / config services.anthropic.key)');
        }

        $this->line('→ Menggabungkan hasil...');
        $combined = $mlService->combinedAnalysis($symbol, $ruleBased, $ml, $ai);
        $this->info("  Sinyal Final: {$combined['signal']} | Confidence: {$combined['confidence']}%");

        $this->info('Selesai! Prediksi tersimpan di tabel predictions.');
        $this->comment('Catatan: kolom "Akurasi Model" di dashboard baru akan terisi setelah target_date prediksi ini terlewati dan php artisan backtest:run dijalankan.');

        return self::SUCCESS;
    }
}