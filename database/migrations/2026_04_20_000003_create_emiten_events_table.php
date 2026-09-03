<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
public function up(): void
{
    Schema::create('emiten_events', function (Blueprint $table) {
        $table->id();
        $table->string('stock_symbol');
        $table->string('title');
        $table->text('description')->nullable();
        $table->enum('type', ['dividen', 'rups', 'laporan', 'lainnya'])->default('lainnya');
        $table->date('event_date');
        $table->decimal('value', 15, 2)->nullable(); // nilai dividen dll
        $table->timestamps();
    });
}


    public function down(): void
    {
        Schema::dropIfExists('emiten_events');
    }
};
