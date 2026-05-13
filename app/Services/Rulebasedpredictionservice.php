<?php

namespace App\Services;

use App\Models\Prediction;
use App\Models\StockData;

class RuleBasedPredictionService
{
    /**
     * Analisis rule-based menggunakan MA, RSI, MACD, Bollinger Bands
     */
    public function analyze(): array
    {
        $latest = StockData::where('symbol', 'BBCA.JK')
            ->orderBy('trading_date', 'desc')
            ->with('indicator')
            ->first();

        if (!$latest || !$latest->indicator) {
            return ['signal' => 'HOLD', 'confidence' => 0, 'reasoning' => 'Data tidak cukup untuk analisis'];
        }

        $price = (float) $latest->close_price;
        $ind   = $latest->indicator;
        $signals = [];
        $reasons = [];

        // 1. Moving Average crossover
        if ($ind->ma5 && $ind->ma20) {
            if ($ind->ma5 > $ind->ma20) {
                $signals[] = 1; // bullish
                $reasons[] = "MA5 ({$ind->ma5}) > MA20 ({$ind->ma20}) → Golden Cross (Bullish)";
            } else {
                $signals[] = -1; // bearish
                $reasons[] = "MA5 ({$ind->ma5}) < MA20 ({$ind->ma20}) → Death Cross (Bearish)";
            }
        }

        // 2. RSI
        if ($ind->rsi) {
            $rsi = (float) $ind->rsi;
            if ($rsi < 30) {
                $signals[] = 2; // oversold = strong buy
                $reasons[] = "RSI {$rsi} < 30 → Oversold, potensi rebound (Strong BUY)";
            } elseif ($rsi > 70) {
                $signals[] = -2; // overbought = strong sell
                $reasons[] = "RSI {$rsi} > 70 → Overbought, potensi koreksi (Strong SELL)";
            } elseif ($rsi < 45) {
                $signals[] = -0.5;
                $reasons[] = "RSI {$rsi} → Momentum melemah";
            } else {
                $signals[] = 0.5;
                $reasons[] = "RSI {$rsi} → Momentum normal-kuat";
            }
        }

        // 3. MACD
        if ($ind->macd && $ind->macd_signal) {
            if ($ind->macd > $ind->macd_signal) {
                $signals[] = 1;
                $reasons[] = "MACD ({$ind->macd}) > Signal ({$ind->macd_signal}) → Bullish momentum";
            } else {
                $signals[] = -1;
                $reasons[] = "MACD ({$ind->macd}) < Signal ({$ind->macd_signal}) → Bearish momentum";
            }

            if ($ind->macd_histogram > 0) {
                $signals[] = 0.5;
                $reasons[] = "MACD Histogram positif → Momentum naik";
            } else {
                $signals[] = -0.5;
                $reasons[] = "MACD Histogram negatif → Momentum turun";
            }
        }

        // 4. Bollinger Bands
        if ($ind->bollinger_upper && $ind->bollinger_lower) {
            if ($price <= $ind->bollinger_lower) {
                $signals[] = 1.5;
                $reasons[] = "Harga ({$price}) menyentuh Lower Bollinger Band → Potensi rebound";
            } elseif ($price >= $ind->bollinger_upper) {
                $signals[] = -1.5;
                $reasons[] = "Harga ({$price}) menyentuh Upper Bollinger Band → Potensi koreksi";
            } else {
                $reasons[] = "Harga dalam range Bollinger Band (normal)";
            }
        }

        // 5. Price vs MA50
        if ($ind->ma50) {
            if ($price > $ind->ma50) {
                $signals[] = 0.5;
                $reasons[] = "Harga di atas MA50 → Trend jangka menengah bullish";
            } else {
                $signals[] = -0.5;
                $reasons[] = "Harga di bawah MA50 → Trend jangka menengah bearish";
            }
        }

        // Kalkulasi final signal
        $totalScore  = array_sum($signals);
        $maxPossible = 6.5;
        $normalScore = $totalScore / $maxPossible;

        if ($normalScore > 0.2) {
            $signal = 'BUY';
        } elseif ($normalScore < -0.2) {
            $signal = 'SELL';
        } else {
            $signal = 'HOLD';
        }

        $confidence = min(95, abs($normalScore) * 100);

        $result = [
            'signal'     => $signal,
            'confidence' => round($confidence, 1),
            'score'      => round($totalScore, 2),
            'reasoning'  => implode("\n", $reasons),
            'details'    => [
                'price'             => $price,
                'ma5'               => $ind->ma5,
                'ma20'              => $ind->ma20,
                'ma50'              => $ind->ma50,
                'rsi'               => $ind->rsi,
                'macd'              => $ind->macd,
                'bollinger_upper'   => $ind->bollinger_upper,
                'bollinger_lower'   => $ind->bollinger_lower,
            ],
        ];

        // Simpan prediksi
        Prediction::create([
            'symbol'          => 'BBCA.JK',
            'prediction_date' => now()->toDateString(),
            'target_date'     => now()->addDays(3)->toDateString(),
            'prediction_type' => 'rule_based',
            'signal'          => $signal,
            'confidence'      => $confidence,
            'reasoning'       => implode("\n", $reasons),
            'raw_data'        => $result,
        ]);

        return $result;
    }
}