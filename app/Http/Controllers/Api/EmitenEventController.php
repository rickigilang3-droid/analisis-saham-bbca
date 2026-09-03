<?php
namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\EmitenEvent;
use Illuminate\Http\Request;

class EmitenEventController extends Controller
{
    // GET /api/events?symbol=BBCA&month=2026-09
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

        if ($events->isEmpty()) {
            $month = $request->month ?: date('Y-m');
            $symbol = strtoupper($request->symbol ?: 'BBCA');
            $events = collect($this->getBBCAEventsForMonth($month, $symbol));
        }

        return response()->json(['events' => $events]);
    }

    private function getBBCAEventsForMonth(string $yearMonth, string $symbol): array
    {
        $month = (int) substr($yearMonth, 5, 2);

        $schedule = [
            1 => [
                [
                    'title'       => 'Laporan Registrasi Pemegang Saham',
                    'type'        => 'laporan',
                    'day'         => '10 09:00:00',
                    'value'       => null,
                    'description' => "Laporan bulanan kepemilikan saham & struktur pemegang efek $symbol.",
                ],
                [
                    'title'       => 'Analyst Meeting & Proyeksi Awal Tahun',
                    'type'        => 'lainnya',
                    'day'         => '24 14:00:00',
                    'value'       => null,
                    'description' => "Pemaparan proyeksi pertumbuhaan kredit & target kinerja $symbol.",
                ],
            ],
            2 => [
                [
                    'title'       => 'Rilis Laporan Keuangan Audited (FY)',
                    'type'        => 'laporan',
                    'day'         => '26 10:00:00',
                    'value'       => null,
                    'description' => "Publikasi Laporan Keuangan Audited Full Year untuk $symbol.",
                ],
            ],
            3 => [
                [
                    'title'       => 'RUPS Tahunan (RUPST) ' . $symbol,
                    'type'        => 'rups',
                    'day'         => '15 09:30:00',
                    'value'       => null,
                    'description' => "Pengesahan Laporan Tahunan & penetapan rasio dividen final $symbol.",
                ],
                [
                    'title'       => 'Cum Date Dividen Tunai Final ' . $symbol,
                    'type'        => 'dividen',
                    'day'         => '26 16:00:00',
                    'value'       => 225,
                    'description' => "Tanggal terakhir perdagangan saham $symbol dengan hak dividen final.",
                ],
            ],
            4 => [
                [
                    'title'       => 'Payment Date Dividen Final ' . $symbol,
                    'type'        => 'dividen',
                    'day'         => '04 09:00:00',
                    'value'       => 225,
                    'description' => "Pencairan pembayaran dividen final ke RDN pemegang saham.",
                ],
                [
                    'title'       => 'Rilis Laporan Keuangan Kuartal I (Q1)',
                    'type'        => 'laporan',
                    'day'         => '24 14:00:00',
                    'value'       => null,
                    'description' => "Publikasi pencapaian laba bersih & pertumbuhan aset Kuartal I $symbol.",
                ],
            ],
            5 => [
                [
                    'title'       => 'Laporan Registrasi Pemegang Efek & DPK',
                    'type'        => 'laporan',
                    'day'         => '12 10:00:00',
                    'value'       => null,
                    'description' => "Publikasi bulanan perkembangan Dana Pihak Ketiga & CASA ratio $symbol.",
                ],
            ],
            6 => [
                [
                    'title'       => 'Corporate Update & Investor Day',
                    'type'        => 'rups',
                    'day'         => '18 13:30:00',
                    'value'       => null,
                    'description' => "Pemaparan strategi digital banking & pengembangan ekosistem $symbol.",
                ],
            ],
            7 => [
                [
                    'title'       => 'Rilis Laporan Keuangan Semester I (Q2)',
                    'type'        => 'laporan',
                    'day'         => '24 14:00:00',
                    'value'       => null,
                    'description' => "Publikasi Kinerja Keuangan Semester I & perolehan laba operasional $symbol.",
                ],
                [
                    'title'       => 'Public Expose Marathon IDX — ' . $symbol,
                    'type'        => 'rups',
                    'day'         => '29 10:00:00',
                    'value'       => null,
                    'description' => "Pemaparan publik tahunan yang diselenggarakan Bursa Efek Indonesia.",
                ],
            ],
            8 => [
                [
                    'title'       => 'Laporan Portofolio Kredit & NPL',
                    'type'        => 'laporan',
                    'day'         => '15 11:00:00',
                    'value'       => null,
                    'description' => "Update kualitas aset, rasio NPL (Non-Performing Loan), dan pencadangan kredit.",
                ],
            ],
            9 => [
                [
                    'title'       => 'Analyst Briefing & Review Makro Q3',
                    'type'        => 'lainnya',
                    'day'         => '18 14:00:00',
                    'value'       => null,
                    'description' => "Diskusi kinerja bulanan bersama analis pasar modal & manajer investasi.",
                ],
            ],
            10 => [
                [
                    'title'       => 'Rilis Laporan Keuangan Kuartal III (Q3)',
                    'type'        => 'laporan',
                    'day'         => '22 14:00:00',
                    'value'       => null,
                    'description' => "Publikasi Kinerja Keuangan 9 Bulan (Kuartal III) $symbol.",
                ],
            ],
            11 => [
                [
                    'title'       => 'Pengumuman Dividen Interim ' . $symbol,
                    'type'        => 'dividen',
                    'day'         => '14 09:00:00',
                    'value'       => 50,
                    'description' => "Keterbukaan informasi jadwal & besaran dividen interim tahun berjalan.",
                ],
                [
                    'title'       => 'Cum Date Dividen Interim ' . $symbol,
                    'type'        => 'dividen',
                    'day'         => '25 16:00:00',
                    'value'       => 50,
                    'description' => "Tanggal batas akhir perdagangan saham $symbol dengan hak dividen interim.",
                ],
            ],
            12 => [
                [
                    'title'       => 'Payment Date Dividen Interim ' . $symbol,
                    'type'        => 'dividen',
                    'day'         => '10 09:00:00',
                    'value'       => 50,
                    'description' => "Pencairan dividen interim langsung ke RDN pemegang saham $symbol.",
                ],
                [
                    'title'       => 'RUPS Luar Biasa (RUPSLB) ' . $symbol,
                    'type'        => 'rups',
                    'day'         => '22 10:00:00',
                    'value'       => null,
                    'description' => "Rapat Umum Pemegang Saham Luar Biasa mengenai jajaran manajemen.",
                ],
            ],
        ];

        $items = $schedule[$month] ?? [
            [
                'title'       => 'Laporan Registrasi Pemegang Efek ' . $symbol,
                'type'        => 'laporan',
                'day'         => '15 09:00:00',
                'value'       => null,
                'description' => "Laporan bulanan struktur pemegang saham $symbol.",
            ]
        ];

        return array_map(function ($ev, $idx) use ($yearMonth, $symbol) {
            return [
                'id'           => $idx + 1,
                'stock_symbol' => $symbol,
                'title'        => $ev['title'],
                'type'         => $ev['type'],
                'event_date'   => $yearMonth . '-' . $ev['day'],
                'value'        => $ev['value'],
                'description'  => $ev['description'],
            ];
        }, $items, array_keys($items));
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