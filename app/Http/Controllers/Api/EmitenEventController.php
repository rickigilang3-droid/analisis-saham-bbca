<?php
namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\EmitenEvent;
use Illuminate\Http\Request;

class EmitenEventController extends Controller
{
    // GET /api/events?symbol=BBCA&month=2024-04
    public function index(Request $request)
    {
        $query = EmitenEvent::query();

        if ($request->symbol) {
            $query->where('stock_symbol', strtoupper($request->symbol));
        }
        if ($request->month) {
            $query->whereYear('event_date', substr($request->month, 0, 4))
                  ->whereMonth('event_date', substr($request->month, 5, 2));
        }

        $events = $query->orderBy('event_date')->get();
        return response()->json(['events' => $events]);
    }

    // POST /api/events (admin only — opsional)
    public function store(Request $request)
    {
        $request->validate([
            'stock_symbol' => 'nullable|string',
            'title'        => 'required',
            'event_date'   => 'required|date',
            'type'         => 'required|in:dividen,rups,laporan,lainnya',
        ]);

        $payload = $request->all();
        $payload['stock_symbol'] = $payload['stock_symbol'] ?? 'BBCA';
        $payload['event_date'] = $payload['event_date'] ?? $payload['date'] ?? null;
        $payload['description'] = $payload['description'] ?? null;
        $payload['value'] = $payload['value'] ?? null;

        $event = EmitenEvent::create($payload);
        return response()->json(['event' => $event], 201);
        
    }
}