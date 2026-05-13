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
            $query->where('stock_symbol', $request->symbol);
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
            'stock_symbol' => 'required',
            'title'        => 'required',
            'event_date'   => 'required|date',
            'type'         => 'required|in:dividen,rups,laporan,lainnya',
        ]);

        $event = EmitenEvent::create($request->all());
        return response()->json(['event' => $event], 201);
        
    }
}