<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('stock_data', function (Blueprint $table) {
            $table->id();
            $table->string('symbol')->default('BBCA.JK');
            $table->date('trading_date');
            $table->decimal('open_price', 12, 2);
            $table->decimal('high_price', 12, 2);
            $table->decimal('low_price', 12, 2);
            $table->decimal('close_price', 12, 2);
            $table->bigInteger('volume');
            $table->decimal('adj_close', 12, 2)->nullable();
            $table->timestamps();

            $table->unique(['symbol', 'trading_date']);
            $table->index('trading_date');
        });

        Schema::create('stock_indicators', function (Blueprint $table) {
            $table->id();
            $table->foreignId('stock_data_id')->constrained('stock_data')->onDelete('cascade');
            $table->decimal('ma5', 12, 2)->nullable();
            $table->decimal('ma10', 12, 2)->nullable();
            $table->decimal('ma20', 12, 2)->nullable();
            $table->decimal('ma50', 12, 2)->nullable();
            $table->decimal('rsi', 8, 4)->nullable();
            $table->decimal('macd', 12, 4)->nullable();
            $table->decimal('macd_signal', 12, 4)->nullable();
            $table->decimal('macd_histogram', 12, 4)->nullable();
            $table->decimal('bollinger_upper', 12, 2)->nullable();
            $table->decimal('bollinger_middle', 12, 2)->nullable();
            $table->decimal('bollinger_lower', 12, 2)->nullable();
            $table->timestamps();
        });

        Schema::create('predictions', function (Blueprint $table) {
            $table->id();
            $table->string('symbol')->default('BBCA.JK');
            $table->date('prediction_date');
            $table->date('target_date');
            $table->enum('prediction_type', ['rule_based', 'ml', 'ai', 'combined']);
            $table->enum('signal', ['BUY', 'SELL', 'HOLD']);
            $table->decimal('confidence', 5, 2)->nullable()->comment('0-100 percent');
            $table->decimal('predicted_price', 12, 2)->nullable();
            $table->decimal('actual_price', 12, 2)->nullable();
            $table->text('reasoning')->nullable();
            $table->json('raw_data')->nullable();
            $table->timestamps();

            $table->index(['symbol', 'prediction_date']);
        });

        Schema::create('watchlists', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->string('symbol');
            $table->decimal('buy_price', 12, 2)->nullable();
            $table->decimal('target_price', 12, 2)->nullable();
            $table->decimal('stop_loss', 12, 2)->nullable();
            $table->text('notes')->nullable();
            $table->timestamps();
        });

        Schema::create('user_portfolios', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->string('symbol');
            $table->integer('lot')->default(0)->comment('1 lot = 100 lembar');
            $table->decimal('avg_price', 12, 2);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('user_portfolios');
        Schema::dropIfExists('watchlists');
        Schema::dropIfExists('predictions');
        Schema::dropIfExists('stock_indicators');
        Schema::dropIfExists('stock_data');
    }
};