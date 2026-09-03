<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Watchlist;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class WatchlistController extends Controller
{
    public function index(): JsonResponse
    {
        $items = Auth::user()
            ->watchlists()
            ->orderBy('created_at', 'desc')
            ->get()
            ->map(fn($item) => [
                'id'          => $item->id,
                'symbol'      => strtoupper($item->symbol),
                'buy_price'   => $item->buy_price ? (float) $item->buy_price : null,
                'target_price'=> $item->target_price ? (float) $item->target_price : null,
                'stop_loss'   => $item->stop_loss ? (float) $item->stop_loss : null,
                'notes'       => $item->notes,
                'created_at'  => $item->created_at->toDateTimeString(),
            ]);

        return response()->json(['success' => true, 'watchlist' => $items]);
    }

    public function store(Request $request): JsonResponse
    {
        $data = $request->validate([
            'symbol'       => 'required|string|max:10',
            'buy_price'    => 'nullable|numeric|min:0',
            'target_price' => 'nullable|numeric|min:0',
            'stop_loss'    => 'nullable|numeric|min:0',
            'notes'        => 'nullable|string|max:500',
        ]);

        $symbol = strtoupper($data['symbol']);
        if (str_ends_with($symbol, '.JK')) {
            $symbol = substr($symbol, 0, -3);
        }

        $item = Auth::user()->watchlists()->create([
            'symbol'       => $symbol,
            'buy_price'    => $data['buy_price'] ?? null,
            'target_price' => $data['target_price'] ?? null,
            'stop_loss'    => $data['stop_loss'] ?? null,
            'notes'        => $data['notes'] ?? null,
        ]);

        return response()->json(['success' => true, 'item' => $item], 201);
    }

    public function update(Request $request, Watchlist $watchlist): JsonResponse
    {
        if ($watchlist->user_id !== Auth::id()) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        $data = $request->validate([
            'symbol'       => 'sometimes|required|string|max:10',
            'buy_price'    => 'nullable|numeric|min:0',
            'target_price' => 'nullable|numeric|min:0',
            'stop_loss'    => 'nullable|numeric|min:0',
            'notes'        => 'nullable|string|max:500',
        ]);

        $updates = [];
        if (isset($data['symbol'])) {
            $symbol = strtoupper($data['symbol']);
            if (str_ends_with($symbol, '.JK')) {
                $symbol = substr($symbol, 0, -3);
            }
            $updates['symbol'] = $symbol;
        }
        $updates = array_merge($updates, array_filter([
            'buy_price'    => $data['buy_price'] ?? null,
            'target_price' => $data['target_price'] ?? null,
            'stop_loss'    => $data['stop_loss'] ?? null,
            'notes'        => $data['notes'] ?? null,
        ], fn($value) => ! is_null($value)));

        $watchlist->update($updates);

        return response()->json(['success' => true, 'item' => $watchlist]);
    }

    public function destroy(Watchlist $watchlist): JsonResponse
    {
        if ($watchlist->user_id !== Auth::id()) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        $watchlist->delete();

        return response()->json(['success' => true]);
    }
}
