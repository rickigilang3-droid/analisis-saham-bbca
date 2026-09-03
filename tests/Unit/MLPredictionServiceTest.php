<?php

namespace Tests\Unit;

use App\Models\StockData;
use App\Models\StockIndicator;
use App\Services\MLPredictionService;
use App\Services\RuleBasedPredictionService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class MLPredictionServiceTest extends TestCase
{
    use RefreshDatabase;

    public function test_it_uses_local_fallback_when_ml_service_is_unavailable(): void
    {
        config(['services.ml_service.enabled' => true]);
        $this->seedBullishData();

        $service = new MLPredictionService();
        $result = $service->analyze();

        $this->assertSame('BUY', $result['signal']);
        $this->assertGreaterThan(0, $result['confidence']);
        $this->assertStringContainsString('fallback', strtolower($result['reasoning']));
    }

    public function test_rule_based_prediction_accepts_requested_symbol(): void
    {
        $this->seedBullishData();

        $service = new RuleBasedPredictionService();
        $result = $service->analyze('BBCA.JK');

        $this->assertSame('BUY', $result['signal']);
        $this->assertDatabaseHas('predictions', [
            'symbol' => 'BBCA.JK',
            'prediction_type' => 'rule_based',
        ]);
    }

    private function seedBullishData(): void
    {
        $baseDate = now()->subDays(40)->startOfDay();
        $prices = [9500, 9550, 9600, 9650, 9700, 9750, 9800, 9850, 9900, 9950, 10000,
            10050, 10100, 10150, 10200, 10250, 10300, 10350, 10400, 10450, 10500, 10550,
            10600, 10650, 10700, 10750, 10800, 10850, 10900, 10950, 11000, 11050, 11100,
            11150, 11200, 11250, 11300, 11350, 11400, 11450, 11500];

        foreach ($prices as $index => $close) {
            $date = $baseDate->copy()->addDays($index);
            $stock = StockData::create([
                'symbol' => 'BBCA.JK',
                'trading_date' => $date,
                'open_price' => $close - 30,
                'high_price' => $close + 35,
                'low_price' => $close - 40,
                'close_price' => $close,
                'volume' => 1000000 + ($index * 1000),
                'adj_close' => $close,
            ]);

            StockIndicator::create([
                'stock_data_id' => $stock->id,
                'ma5' => $close,
                'ma10' => $close - 100,
                'ma20' => $close - 150,
                'ma50' => $close - 200,
                'rsi' => 62,
                'macd' => 2.4,
                'macd_signal' => 1.1,
                'macd_histogram' => 1.3,
                'bollinger_upper' => $close + 65,
                'bollinger_middle' => $close,
                'bollinger_lower' => $close - 65,
            ]);
        }
    }
}
