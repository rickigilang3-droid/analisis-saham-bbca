<?php

namespace App\Console\Commands;

use App\Services\BacktestingService;
use Illuminate\Console\Command;

class BacktestRunCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'backtest:run';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Menjalankan proses backtesting untuk memverifikasi prediksi lama dengan harga aktual.';

    /**
     * Execute the console command.
     */
    public function handle(BacktestingService $backtestingService)
    {
        $this->info('Memulai proses backtesting...');

        $result = $backtestingService->run();

        $this->info($result['message']);

        $this->info('Menghitung metrik akurasi terbaru...');
        $metrics = $backtestingService->getMetrics();

        $this->comment('Akurasi Keseluruhan: ' . ($metrics['overall']['signal_accuracy'] ?? 0) . '%');

        foreach ($metrics['by_type'] as $type => $metric) {
            $this->line("- {$type}: {$metric['signal_accuracy']}% ({$metric['count']} prediksi)");
        }

        $this->info('Proses backtesting selesai.');
    }
}