<?php

namespace App\Http\Controllers;

use App\Models\EmitenEvent;
use Illuminate\Http\Request;

class EventController extends Controller
{
    public function index()
    {
        $events = EmitenEvent::orderBy('event_date', 'asc')->paginate(20);
        return view('events.index', compact('events'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'title' => 'required',
            'date' => 'required|date',
            'type' => 'required|in:dividen,rups,laporan,lainnya',
            'description' => 'nullable|string',
        ]);

        EmitenEvent::create([
            'stock_symbol' => $request->input('stock_symbol', 'BBCA'),
            'title' => $request->title,
            'description' => $request->description,
            'type' => $request->type,
            'event_date' => $request->date,
            'value' => $request->input('value'),
        ]);

        return redirect()->route('events.index')->with('status', 'Event berhasil ditambahkan.');
    }

    public function show($id)
    {
        $event = EmitenEvent::findOrFail($id);
        return view('events.show', compact('event'));
    }
}
