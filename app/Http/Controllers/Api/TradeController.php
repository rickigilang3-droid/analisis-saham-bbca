<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Setting;
use App\Models\StockData;
use App\Models\Transaction;
use App\Models\UserPortfolio;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;

class TradeController extends Controller
{
    /**
     * EKSEKUSI BELI / JUAL
     */
    public function execute(Request $request): JsonResponse
    {
        $data = $request->validate([
            'type'  => 'required|in:BUY,SELL',
            'price' => 'required|numeric|min:1',
            'lot'   => 'required|integer|min:1',
            'stock' => 'nullable|string|max:10',
        ]);

        if (Setting::get('maintenance', '0') === '1') {
            return response()->json(['message' => 'Sistem sedang maintenance.'], 503);
        }

        $user  = auth()->user();
        $stock = strtoupper(trim($data['stock'] ?? 'BBCA'));
        if (str_ends_with($stock, '.JK')) {
            $stock = substr($stock, 0, -3);
        }
        $lot   = (int) $data['lot'];
        $price = (float) $data['price'];
        $total = $lot * 100 * $price;

        $maxLot = (int) Setting::get('max_lot', '100');
        if ($lot > $maxLot) {
            return response()->json(['message' => "Maksimal $maxLot lot per transaksi."], 422);
        }

        try {
            DB::transaction(function () use ($user, $data, $lot, $price, $total, $stock) {
                $portfolio = UserPortfolio::firstOrNew([
                    'user_id' => $user->id,
                    'symbol'  => strtoupper($stock),
                ]);

                if ($data['type'] === 'BUY') {
                    if ($user->balance < $total) {
                        throw new \Exception('Saldo tidak mencukupi.');
                    }

                    $oldValue = $portfolio->lot * 100 * $portfolio->avg_price;
                    $portfolio->lot += $lot;
                    $portfolio->avg_price = $portfolio->lot > 0
                        ? ($oldValue + $total) / ($portfolio->lot * 100)
                        : 0;
                    $portfolio->save();
                    $user->decrement('balance', $total);
                } else {
                    if ($portfolio->lot < $lot) {
                        throw new \Exception('Lot tidak mencukupi untuk ' . strtoupper($stock) . '.');
                    }

                    $portfolio->lot -= $lot;
                    if ($portfolio->lot === 0) {
                        $portfolio->delete();
                    } else {
                        $portfolio->save();
                    }

                    $user->increment('balance', $total);
                }

                Transaction::create([
                    'user_id' => $user->id,
                    'type'    => $data['type'],
                    'stock'   => strtoupper($stock),
                    'lot'     => $lot,
                    'price'   => $price,
                    'total'   => $total,
                ]);
            });

            $user = $user->fresh();
            $holdings = $user->portfolios()->get()->map(fn($row) => [
                'symbol' => strtoupper($row->symbol),
                'lot' => $row->lot,
                'avg_price' => (float) $row->avg_price,
            ]);

            $currentPortfolio = $user->portfolios()->where('symbol', strtoupper($stock))->first();

            return response()->json([
                'message'   => 'Transaksi berhasil.',
                'balance'   => (float) $user->balance,
                'lots'      => $currentPortfolio?->lot ?? 0,
                'avg_price' => (float) ($currentPortfolio?->avg_price ?? 0),
                'holdings'  => $holdings,
            ]);
        } catch (\Exception $e) {
            return response()->json(['message' => $e->getMessage()], 422);
        }
    }

    /**
     * FUNGSI ANALISIS PREDIKSI AI GEMINI (ANTI-CORS)
     */
    public function analyze(Request $request): JsonResponse
    {
        $prompt = $request->input('prompt', '');
        $apiKey = env('GEMINI_API_KEY');
        $ta = $request->input('ta', []);

        if ($apiKey && strlen($apiKey) > 10) {
            try {
                $response = Http::withHeaders(['Content-Type' => 'application/json'])
                    ->timeout(3)
                    ->post("https://generativelanguage.googleapis.com/v1beta/models/gemini-1.5-flash:generateContent?key=$apiKey", [
                        'contents' => [['parts' => [['text' => $prompt]]]],
                    ]);

                if ($response->successful()) {
                    $rawResult = $response->json();
                    $text = $rawResult['candidates'][0]['content']['parts'][0]['text'] ?? '';

                    if (!empty($text)) {
                        return response()->json([
                            'candidates' => [
                                ['content' => ['parts' => [['text' => $text]]]]
                            ]
                        ]);
                    }
                }
            } catch (\Exception $e) {
                // Ignore exception and use fast fallback
            }
        }

        // Fast dynamic technical analysis fallback
        $price   = number_format($ta['cur'] ?? 10250, 0, ',', '.');
        $chg     = number_format($ta['chgPct'] ?? 0, 2);
        $rsiVal  = (float) ($ta['rsi'] ?? 52);
        $rsi     = number_format($rsiVal, 1);
        $signal  = strtoupper($ta['signal'] ?? 'BUY');
        $support = number_format($ta['support'] ?? 10000, 0, ',', '.');
        $resist  = number_format($ta['resist'] ?? 10500, 0, ',', '.');

        $rsiCond = $rsiVal > 70 ? "overbought (jenuh beli)" : ($rsiVal < 30 ? "oversold (jenuh jual)" : "netral terdistribusi dengan baik");
        $rec = $signal === 'BUY'
            ? "Lakukan akumulasi beli secara bertahap pada area support Rp $support."
            : ($signal === 'SELL'
                ? "Pertimbangkan taktis take profit parsial mendekati area resistance Rp $resist."
                : "Hold posisi dan amati pembentukan pola breakout di atas Rp $resist.");

        $fallbackText = "Saham BBCA saat ini diperdagangkan pada level Rp $price ($chg%). Indikator RSI (14) berada pada posisi $rsi yang mengindikasikan kondisi $rsiCond.\n\n" .
            "Secara struktur teknikal harian, pergerakan harga tertahan di atas batas support kunci Rp $support dengan area resistance harian terdekat berada pada Rp $resist. Sinyal teknikal agregat saat ini mengarah pada konfirmasi $signal.\n\n" .
            "Rekomendasi: $rec Tetapkan pembatasan risiko disiplin di bawah garis support.";

        return response()->json([
            'candidates' => [
                ['content' => ['parts' => [['text' => $fallbackText]]]]
            ]
        ]);
    }

    public function portfolio(): JsonResponse
    {
        $user = auth()->user();
        $holdings = $user->portfolios()->get()->map(function ($row) {
            $currentPrice = (float) StockData::where('symbol', strtoupper($row->symbol) . '.JK')
                ->orderBy('trading_date', 'desc')
                ->value('close_price') ?? 0;

            return [
                'symbol'    => strtoupper($row->symbol),
                'lot'       => $row->lot,
                'avg_price' => (float) $row->avg_price,
                'current'   => $currentPrice,
                'value'     => round($row->lot * 100 * $currentPrice, 2),
            ];
        });

        return response()->json([
            'balance'   => (float) $user->balance,
            'holdings'  => $holdings,
        ]);
    }

    public function history(): JsonResponse
    {
        $txs = auth()->user()
            ->transactions()
            ->orderBy('created_at', 'desc')
            ->limit(10)
            ->get()
            ->map(fn($t) => [
                'type'        => $t->type,
                'symbol'      => $t->stock,
                'lot'         => $t->lot,
                'price'       => (float) $t->price,
                'time'        => $t->created_at->timezone('Asia/Jakarta')->format('H:i:s'),
                'created_at'  => $t->created_at->timezone('Asia/Jakarta')->toISOString(),
            ]);

        return response()->json($txs);
    }

    public function reset(): JsonResponse
    {
        $user = auth()->user();
        DB::transaction(function () use ($user) {
            $user->update(['balance' => 100000000]);
            $user->transactions()->delete();
            $user->portfolios()->delete();
        });

        return response()->json(['message' => 'Akun direset.']);
    }

    /**
     * FUNGSI MENGAMBIL DATA KALENDER EVENT EMITEN
     */
    public function events(Request $request): JsonResponse
    {
        $symbol = strtoupper($request->query('symbol', 'BBCA'));
        $month = $request->query('month', date('Y-m')); // Format 'YYYY-MM' dari frontend

        // Karena kamu belum terhubung dengan tabel event yang real,
        // kita gunakan dummy data yang mengikuti bulan yang sedang aktif di frontend.
        $events = [
            [
                'title' => 'Pembagian Dividen Tunai',
                'type' => 'dividen',
                'event_date' => $month . '-12 00:00:00',
                'value' => 50,
                'description' => 'Dividen interim tahun buku berjalan untuk ' . $symbol . '.',
            ],
            [
                'title' => 'RUPS Tahunan',
                'type' => 'rups',
                'event_date' => $month . '-25 09:00:00',
                'value' => null,
                'description' => 'Rapat Umum Pemegang Saham Tahunan.',
            ]
        ];

        return response()->json($events);
    }
}