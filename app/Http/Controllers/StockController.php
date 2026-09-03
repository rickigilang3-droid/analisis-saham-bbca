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

    public function dashboard(Request $request)
    {
        $symbol = $this->normalizeSymbol($request->query('symbol', 'BBCA'));

        $latest      = $this->stockService->getLatestPrice($symbol);
        $chartData   = $this->stockService->getHistoryForChart(60, $symbol);
        $predictions = Prediction::where('symbol', $symbol)
            ->orderBy('created_at', 'desc')
            ->take(5)
            ->get();

        $latestPrediction = Prediction::where('symbol', $symbol)
            ->where('prediction_type', 'combined')
            ->orderBy('created_at', 'desc')
            ->first();

        return view('user.dashboard', compact('latest', 'chartData', 'predictions', 'latestPrediction', 'symbol'));
    }

    public function predict(Request $request)
    {
        $symbol = $this->normalizeSymbol($request->input('symbol', 'BBCA'));

        // Pastikan data terbaru sudah ada
        $this->stockService->fetchFromYahoo(90, $symbol);

        // Jalankan semua metode prediksi
        $rulePrediction = $this->ruleService->analyze($symbol);
        $mlPrediction   = $this->mlService->analyze($symbol);
        $aiPrediction   = $this->aiService->analyze($symbol);

        // Gabungkan
        $combined = $this->mlService->combinedAnalysis($symbol, $rulePrediction, $mlPrediction, $aiPrediction);

        return response()->json([
            'success'     => true,
            'symbol'      => $symbol,
            'rule_based'  => $rulePrediction,
            'ml'          => $mlPrediction,
            'ai'          => $aiPrediction,
            'combined'    => $combined,
        ]);
    }

    public function refreshData(Request $request)
    {
        $symbol = $this->normalizeSymbol($request->query('symbol', 'BBCA'));
        $result = $this->stockService->fetchFromYahoo(90, $symbol);
        return response()->json($result);
    }

    public function chartData(Request $request)
    {
        $days = (int) $request->get('days', 60);
        $symbol = $this->normalizeSymbol($request->get('symbol', 'BBCA'));
        $data = $this->stockService->getHistoryForChart($days, $symbol);

        return response()->json([
            'symbol'  => $symbol,
            'labels'  => array_column($data, 'trading_date'),
            'closes'  => array_column($data, 'close_price'),
            'volumes' => array_column($data, 'volume'),
            'highs'   => array_column($data, 'high_price'),
            'lows'    => array_column($data, 'low_price'),
            'opens'   => array_column($data, 'open_price'),
        ]);
    }

    public function history(Request $request)
    {
        $symbol = $this->normalizeSymbol($request->query('symbol', 'BBCA'));
        $predictions = Prediction::where('symbol', $symbol)
            ->orderBy('prediction_date', 'desc')
            ->paginate(20);

        return view('user.prediction_history', compact('predictions', 'symbol'));
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