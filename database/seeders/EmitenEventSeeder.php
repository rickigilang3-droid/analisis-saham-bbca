<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class EmitenEventSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */public function run(): void
{
    $events = [
        ['stock_symbol'=>'BBCA','title'=>'Dividen Interim 2024','type'=>'dividen','event_date'=>'2024-11-15','value'=>235,'description'=>'Pembagian dividen interim Rp 235/saham'],
        ['stock_symbol'=>'BBCA','title'=>'RUPS Tahunan 2024','type'=>'rups','event_date'=>'2024-04-22','value'=>null,'description'=>'Rapat Umum Pemegang Saham Tahunan'],
        ['stock_symbol'=>'BBCA','title'=>'Laporan Keuangan Q1 2025','type'=>'laporan','event_date'=>'2025-04-30','value'=>null,'description'=>'Rilis laporan keuangan kuartal pertama'],
    ];

    foreach ($events as $ev) {
        \App\Models\EmitenEvent::create($ev);
    }
}
}