<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

class AIController extends Controller
{
    public function analyze(Request $request)
    {
        $request->validate([
            'prompt' => 'required|string|max:3000',
        ]);

        $apiKey = env('GEMINI_API_KEY');

        if (!$apiKey) {
            return response()->json([
                'error' => 'GEMINI_API_KEY tidak ditemukan di .env'
            ], 500);
        }

        $response = Http::timeout(30)
            ->post(
                'https://generativelanguage.googleapis.com/v1beta/models/gemini-1.5-flash:generateContent?key=' . $apiKey,
                [
                    'contents' => [
                        [
                            'parts' => [
                                ['text' => $request->input('prompt')]
                            ]
                        ]
                    ],
                    'generationConfig' => [
                        'maxOutputTokens' => 500,
                        'temperature'     => 0.7,
                    ]
                ]
            );

        if ($response->failed()) {
            return response()->json([
                'error'    => 'Gemini API error',
                'details'  => $response->json()
            ], $response->status());
        }

        return response()->json($response->json());
    }
}