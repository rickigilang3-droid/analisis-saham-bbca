<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('settings', function (Blueprint $table) {
            $table->id();
            $table->string('key')->unique();
            $table->text('value')->nullable();
            $table->timestamps();
        });

        // Seed default settings
        \App\Models\Setting::insert([
            ['key' => 'maintenance',     'value' => '0',    'created_at' => now(), 'updated_at' => now()],
            ['key' => 'trading_hours',   'value' => '1',    'created_at' => now(), 'updated_at' => now()],
            ['key' => 'weekend_trading', 'value' => '0',    'created_at' => now(), 'updated_at' => now()],
            ['key' => 'min_lot',         'value' => '1',    'created_at' => now(), 'updated_at' => now()],
            ['key' => 'max_lot',         'value' => '100',  'created_at' => now(), 'updated_at' => now()],
            ['key' => 'commission',      'value' => '0.15', 'created_at' => now(), 'updated_at' => now()],
        ]);
    }

    public function down(): void
    {
        Schema::dropIfExists('settings');
    }
};