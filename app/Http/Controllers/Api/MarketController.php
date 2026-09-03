<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\StockData;
use App\Models\Prediction;
use App\Models\Transaction;
use App\Models\UserPortfolio;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Http;

class MarketController extends Controller
{
    public function quote(Request $request): JsonResponse
    {
        $symbol = $this->normalizeSymbol($request->get('symbol', 'BBCA'));
        $result = $this->fetchQuote($symbol);

        return response()->json(array_merge(['success' => true, 'symbol' => $symbol], $result));
    }

    public function fundamentals(Request $request): JsonResponse
    {
        $symbol = $this->normalizeSymbol($request->get('symbol', 'BBCA'));
        $data = $this->fetchFundamentals($symbol);

        return response()->json(['success' => true, 'symbol' => $symbol, 'fundamentals' => $data]);
    }

    public function valuation(Request $request): JsonResponse
    {
        $symbol = $this->normalizeSymbol($request->get('symbol', 'BBCA'));
        $currentPrice = 6775;

        $dcfFairValue = 7450;
        $ddmFairValue = 7200;
        $marginOfSafety = 9.96;

        $quarterly = [
            ['period' => 'Q3 2025', 'net_profit' => '13.8 T', 'nii' => '21.2 T', 'npl' => '1.9%', 'casa' => '80.4%', 'roe' => '21.8%'],
            ['period' => 'Q4 2025', 'net_profit' => '14.5 T', 'nii' => '22.1 T', 'npl' => '1.8%', 'casa' => '80.6%', 'roe' => '22.1%'],
            ['period' => 'Q1 2026', 'net_profit' => '14.1 T', 'nii' => '21.8 T', 'npl' => '1.9%', 'casa' => '80.2%', 'roe' => '21.5%'],
            ['period' => 'Q2 2026', 'net_profit' => '14.8 T', 'nii' => '22.6 T', 'npl' => '1.8%', 'casa' => '81.1%', 'roe' => '22.4%'],
        ];

        return response()->json([
            'success'          => true,
            'symbol'           => $symbol,
            'current_price'    => $currentPrice,
            'dcf_fair_value'   => $dcfFairValue,
            'ddm_fair_value'   => $ddmFairValue,
            'margin_of_safety' => $marginOfSafety,
            'status'           => $marginOfSafety > 0 ? 'UNDERVALUED' : 'OVERVALUED',
            'quarterly'        => $quarterly,
        ]);
    }

    public function announcement(): JsonResponse
    {
        return response()->json([
            'enabled' => \App\Models\Setting::get('announcement_enabled', '1') === '1',
            'message' => \App\Models\Setting::get('announcement_message', '🔔 Pengumuman: RUPSLB BBCA & Pembagian Dividen Interim diselenggarakan bulan ini!'),
            'type'    => \App\Models\Setting::get('announcement_type', 'info'),
        ]);
    }

    public function backtest(Request $request): JsonResponse
    {
        $symbol = $this->normalizeSymbol($request->get('symbol', 'BBCA'));
        $history = StockData::where('symbol', $symbol)->orderBy('trading_date')->get();

        if ($history->count() < 30) {
            return response()->json(['success' => false, 'message' => 'Tidak cukup data untuk backtest.'], 422);
        }

        $prices = $history->pluck('close_price')->map(fn($value) => (float) $value)->toArray();
        $cash = 100000000;
        $position = 0;
        $lotSize = 100;
        $tradeRecords = [];
        $entryPrice = 0;

        for ($i = 25; $i < count($prices); $i++) {
            $ema7 = array_sum(array_slice($prices, $i - 6, 7)) / 7;
            $ema25 = array_sum(array_slice($prices, $i - 24, 25)) / 25;
            $prev7 = array_sum(array_slice($prices, $i - 7, 7)) / 7;
            $prev25 = array_sum(array_slice($prices, $i - 25, 25)) / 25;
            $price = $prices[$i];

            if ($position === 0 && $prev7 <= $prev25 && $ema7 > $ema25) {
                $position = 1;
                $entryPrice = $price;
                $tradeRecords[] = ['type' => 'BUY', 'price' => $price, 'profit' => null];
            }

            if ($position === 1 && $prev7 >= $prev25 && $ema7 < $ema25) {
                $profit = ($price - $entryPrice) * $lotSize;
                $tradeRecords[] = ['type' => 'SELL', 'price' => $price, 'profit' => $profit];
                $cash += $profit;
                $position = 0;
            }
        }

        if ($position === 1) {
            $finalPrice = end($prices);
            $profit = ($finalPrice - $entryPrice) * $lotSize;
            $tradeRecords[] = ['type' => 'SELL', 'price' => $finalPrice, 'profit' => $profit];
            $cash += $profit;
        }

        $wins = collect($tradeRecords)->where('type', 'SELL')->filter(fn($trade) => $trade['profit'] > 0)->count();
        $sellCount = collect($tradeRecords)->where('type', 'SELL')->count();
        $roi = (($cash - 100000000) / 100000000) * 100;

        return response()->json([
            'success'    => true,
            'symbol'     => $symbol,
            'trades'     => $tradeRecords,
            'trade_count'=> count($tradeRecords),
            'win_rate'   => $sellCount > 0 ? round(($wins / $sellCount) * 100, 1) : 0,
            'roi'        => round($roi, 2),
            'period'     => $history->count() . ' hari',
        ]);
    }

    public function performance(Request $request): JsonResponse
    {
        $holdings = Auth::user()->portfolios()->get();
        $symbolPrices = [];
        $totalValue = 0;
        $totalCost = 0;

        foreach ($holdings as $holding) {
            $symbolKey = strtoupper($holding->symbol);
            $currentPrice = StockData::where('symbol', $symbolKey . '.JK')
                ->orderBy('trading_date', 'desc')
                ->value('close_price') ?? 0;
            $symbolPrices[$symbolKey] = (float) $currentPrice;
            $totalValue += $holding->lot * 100 * $currentPrice;
            $totalCost += $holding->lot * 100 * $holding->avg_price;
        }

        $history = [];
        $days = 14;
        for ($d = $days; $d >= 0; $d--) {
            $date = now()->subDays($d)->format('Y-m-d');
            $dailyValue = 0;

            foreach ($holdings as $holding) {
                $price = StockData::where('symbol', strtoupper($holding->symbol) . '.JK')
                    ->where('trading_date', '<=', $date)
                    ->orderBy('trading_date', 'desc')
                    ->value('close_price') ?? 0;
                $dailyValue += $holding->lot * 100 * $price;
            }

            $history[] = ['date' => $date, 'value' => round($dailyValue, 2)];
        }

        return response()->json([
            'success'      => true,
            'holdings'     => $holdings->map(fn($item) => [
                'symbol'    => strtoupper($item->symbol),
                'lot'       => $item->lot,
                'avg_price' => (float) $item->avg_price,
                'current'   => $symbolPrices[strtoupper($item->symbol)] ?? 0,
                'value'     => round($item->lot * 100 * ($symbolPrices[strtoupper($item->symbol)] ?? 0), 2),
            ]),
            'total_value'  => round($totalValue, 2),
            'total_cost'   => round($totalCost, 2),
            'unrealized'   => round($totalValue - $totalCost, 2),
            'history'      => $history,
        ]);
    }

    public function sentiment(): JsonResponse
    {
        $rssUrl = 'https://www.cnbcindonesia.com/market/rss';
        try {
            $response = Http::timeout(10)->get('https://api.rss2json.com/v1/api.json', ['rss_url' => $rssUrl]);
            if (! $response->successful()) {
                throw new \Exception('RSS fetch failed');
            }

            $data = $response->json();
            $items = collect($data['items'] ?? [])->take(6);
            $headlines = $items->map(fn($item) => [
                'title' => $item['title'] ?? '',
                'link'  => $item['link'] ?? '#',
                'pubDate' => $item['pubDate'] ?? '',
            ]);
            $score = $this->scoreSentiment($headlines->pluck('title')->join(' '));

            return response()->json([
                'success' => true,
                'score'   => $score,
                'sentiment' => $score >= 55 ? 'Positif' : ($score <= 45 ? 'Negatif' : 'Netral'),
                'headline_count' => $headlines->count(),
                'headlines' => $headlines,
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal mengambil berita: ' . $e->getMessage(),
            ], 500);
        }
    }

    private function normalizeSymbol(string $symbol): string
    {
        $symbol = strtoupper(trim($symbol));
        return str_ends_with($symbol, '.JK') ? $symbol : $symbol . '.JK';
    }

    private function fetchQuote(string $symbol): array
    {
        try {
            $url = "https://query1.finance.yahoo.com/v8/finance/chart/{$symbol}";
            $response = Http::withHeaders([
                'User-Agent' => 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/120.0.0.0 Safari/537.36'
            ])->timeout(10)->get($url, [
                'interval' => '1d',
                'range' => '1mo',
                'includeAdjustedClose' => 'true',
            ]);

            $payload = $response->json('chart.result.0') ?? null;
            if (! $payload) {
                throw new \Exception('Quote tidak ditemukan');
            }

            $meta = $payload['meta'] ?? [];
            $quote = $payload['indicators']['quote'][0] ?? [];
            $timestamps = $payload['timestamp'] ?? [];
            $closeValues = $quote['close'] ?? [];
            $regularPrice = (float) ($meta['regularMarketPrice'] ?? 0);
            $latestClose = $regularPrice > 0 ? $regularPrice : null;

            if (! $latestClose && ! empty($closeValues)) {
                foreach (array_reverse($closeValues) as $value) {
                    if (is_numeric($value) && $value > 0) {
                        $latestClose = (float) $value;
                        break;
                    }
                }
            }

            $latestClose = $latestClose ?? 6775;

            return [
                'price' => round($latestClose, 3),
                'previous_close' => round((float) ($meta['chartPreviousClose'] ?? $meta['previousClose'] ?? 6675), 3),
                'open' => round((float) ($meta['regularMarketOpen'] ?? 6700), 3),
                'high' => round((float) ($meta['regularMarketDayHigh'] ?? 6800), 3),
                'low' => round((float) ($meta['regularMarketDayLow'] ?? 6625), 3),
                'market_cap' => $this->safeNumber($meta['marketCap'] ?? 835000000000000),
                'currency' => $meta['currency'] ?? 'IDR',
            ];
        } catch (\Exception $e) {
            if (str_contains(strtolower($symbol), 'bbca')) {
                return [
                    'price' => 6775,
                    'previous_close' => 6675,
                    'open' => 6700,
                    'high' => 6800,
                    'low' => 6625,
                    'market_cap' => 835000000000000,
                    'currency' => 'IDR',
                ];
            }

            return [
                'price' => 6775,
                'previous_close' => 6675,
                'open' => 6700,
                'high' => 6800,
                'low' => 6625,
                'market_cap' => 835000000000000,
                'currency' => 'IDR',
            ];
        }
    }

    private function fetchFundamentals(string $symbol): array
    {
        try {
            $url = "https://query2.finance.yahoo.com/v10/finance/quoteSummary/{$symbol}";
            $response = Http::timeout(10)->get($url, ['modules' => 'price,summaryDetail,defaultKeyStatistics,financialData']);
            $payload = $response->json()['quoteSummary']['result'][0] ?? null;
            if (! $payload) {
                throw new \Exception('Fundamental data tidak ditemukan');
            }

            $price = $payload['price'] ?? [];
            $detail = $payload['summaryDetail'] ?? [];
            $stats = $payload['defaultKeyStatistics'] ?? [];
            $finance = $payload['financialData'] ?? [];

            return [
                'market_cap'      => $this->safeNumber($price['marketCap'] ?? null),
                'trailing_pe'     => $this->safeNumber($detail['trailingPE'] ?? $price['trailingPE'] ?? null),
                'forward_pe'      => $this->safeNumber($detail['forwardPE'] ?? null),
                'price_to_book'   => $this->safeNumber($detail['priceToBook'] ?? null),
                'dividend_yield'  => $this->safeNumber($detail['dividendYield'] ?? null) * 100,
                'eps'             => $this->safeNumber($price['epsTrailingTwelveMonths'] ?? null),
                'revenue_per_share' => $this->safeNumber($price['revenuePerShare'] ?? null),
                'current_price'   => $this->safeNumber($price['regularMarketPrice'] ?? null),
                'currency'        => $price['currency'] ?? 'IDR',
            ];
        } catch (\Exception $e) {
            return [
                'market_cap'      => 1250000000000000,
                'trailing_pe'     => 23.4,
                'forward_pe'      => 21.7,
                'price_to_book'   => 4.8,
                'dividend_yield'  => 2.8,
                'eps'             => 985.4,
                'revenue_per_share' => 4.8,
                'current_price'   => 0,
                'currency'        => 'IDR',
            ];
        }
    }

    private function safeNumber($value): float
    {
        if (is_array($value) && isset($value['raw'])) {
            return (float) $value['raw'];
        }

        return $value !== null ? (float) $value : 0.0;
    }

    private function scoreSentiment(string $text): int
    {
        $positive = ['naik', 'bull', 'positif', 'kuat', 'optimis', 'menguat', 'breakout'];
        $negative = ['turun', 'bear', 'resesi', 'melemah', 'rugi', 'volatil', 'koreksi'];

        $score = 50;
        foreach ($positive as $word) {
            if (stripos($text, $word) !== false) {
                $score += 5;
            }
        }
        foreach ($negative as $word) {
            if (stripos($text, $word) !== false) {
                $score -= 5;
            }
        }

        return max(0, min(100, $score));
    }
}
