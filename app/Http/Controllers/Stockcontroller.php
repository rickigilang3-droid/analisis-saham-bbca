<?php

namespace App\Http\Controllers;

use App\Models\Prediction;
use App\Models\StockData;
use App\Services\AIPredictionService;
use App\Services\MLPredictionService;
use App\Services\RuleBasedPredictionService;
use App\Services\StockDataService;
use Illuminate\Http\Request;

class StockController extends Controller
{
    public function __construct(
        private StockDataService $stockService,
        private RuleBasedPredictionService $ruleService,
        private MLPredictionService $mlService,
        private AIPredictionService $aiService,
    ) {}

    public function dashboard()
    {
        $latest      = $this->stockService->getLatestPrice();
        $chartData   = $this->stockService->getHistoryForChart(60);
        $predictions = Prediction::where('symbol', 'BBCA.JK')
            ->orderBy('created_at', 'desc')
            ->take(5)
            ->get();

        $latestPrediction = Prediction::where('symbol', 'BBCA.JK')
            ->where('prediction_type', 'combined')
            ->orderBy('created_at', 'desc')
            ->first();

        return view('user.dashboard', compact('latest', 'chartData', 'predictions', 'latestPrediction'));
    }

    public function predict(Request $request)
    {
        // Pastikan data terbaru sudah ada
        $this->stockService->fetchFromYahoo(90);

        // Jalankan semua metode prediksi
        $rulePrediction = $this->ruleService->analyze();
        $mlPrediction   = $this->mlService->analyze();
        $aiPrediction   = $this->aiService->analyze();

        // Gabungkan
        $combined = $this->mlService->combinedAnalysis($rulePrediction, $mlPrediction, $aiPrediction);

        return response()->json([
            'success'     => true,
            'rule_based'  => $rulePrediction,
            'ml'          => $mlPrediction,
            'ai'          => $aiPrediction,
            'combined'    => $combined,
        ]);
    }

    public function refreshData()
    {
        $result = $this->stockService->fetchFromYahoo(90);
        return response()->json($result);
    }

    public function chartData(Request $request)
    {
        $days = (int) $request->get('days', 60);
        $data = $this->stockService->getHistoryForChart($days);

        return response()->json([
            'labels'  => array_column($data, 'trading_date'),
            'closes'  => array_column($data, 'close_price'),
            'volumes' => array_column($data, 'volume'),
            'highs'   => array_column($data, 'high_price'),
            'lows'    => array_column($data, 'low_price'),
            'opens'   => array_column($data, 'open_price'),
        ]);
    }

    public function history()
    {
        $predictions = Prediction::where('symbol', 'BBCA.JK')
            ->orderBy('prediction_date', 'desc')
            ->paginate(20);

        return view('user.prediction_history', compact('predictions'));
    }
}