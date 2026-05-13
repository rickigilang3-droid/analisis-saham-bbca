<?php

use App\Http\Controllers\Api\AdminApiController;
use App\Http\Controllers\Api\MarketController;
use App\Http\Controllers\Api\TradeController;
use App\Http\Controllers\Api\WatchlistController;
use App\Http\Controllers\Api\DiscussionController;
use App\Http\Controllers\Api\EmitenEventController;
use Illuminate\Support\Facades\Route;

// ── Trading API (User) ───────────────────────────────────────────────────
Route::middleware('auth:sanctum')->group(function () {
    Route::post('/trade',             [TradeController::class, 'execute']);
    Route::get('/portfolio',          [TradeController::class, 'portfolio']);
    Route::get('/trade/history',      [TradeController::class, 'history']);
    Route::post('/trade/reset',       [TradeController::class, 'reset']);
    Route::post('/ai/analyze',        [TradeController::class, 'analyze']);

    Route::get('/watchlist',          [WatchlistController::class, 'index']);
    Route::post('/watchlist',         [WatchlistController::class, 'store']);
    Route::put('/watchlist/{watchlist}', [WatchlistController::class, 'update']);
    Route::delete('/watchlist/{watchlist}', [WatchlistController::class, 'destroy']);

    Route::get('/market/quote',       [MarketController::class, 'quote']);
    Route::get('/market/fundamentals',[MarketController::class, 'fundamentals']);
    Route::get('/market/backtest',    [MarketController::class, 'backtest']);
    Route::get('/market/performance', [MarketController::class, 'performance']);
    Route::get('/market/sentiment',   [MarketController::class, 'sentiment']);
});

// ── Admin Area (Prefix: /api/admin) ──────────────────────────────────────
Route::middleware(['auth:sanctum', 'admin'])->prefix('admin')->group(function () {
    // Jalur: /api/admin/stats
    Route::get('/stats', [AdminApiController::class, 'getStats']);

    // Jalur: /api/admin/users <--- INI YANG TADI ILANG, RICKI!
    Route::get('/users', [AdminApiController::class, 'getUsers']);
    Route::post('/users', [AdminApiController::class, 'createUser']);
    Route::put('/users/{user}', [AdminApiController::class, 'updateUser']);
    Route::delete('/users/{user}', [AdminApiController::class, 'deleteUser']);

    // Jalur: /api/admin/transactions
    Route::get('/transactions', [AdminApiController::class, 'getTransactions']);
    
    // Jalur: /api/admin/settings
    Route::get('/settings', [AdminApiController::class, 'getSettings']);
    Route::post('/settings', [AdminApiController::class, 'saveSettings']);
});

Route::get('/health', fn() => response()->json(['status' => 'ok']));
Route::middleware('auth:sanctum')->group(function () {
    // Diskusi
    Route::get('/discussions', [DiscussionController::class, 'index']);
    Route::post('/discussions', [DiscussionController::class, 'store']);
    Route::post('/discussions/{id}/comments', [DiscussionController::class, 'addComment']);
    Route::post('/discussions/{id}/like', [DiscussionController::class, 'like']);
    Route::delete('/discussions/{id}', [DiscussionController::class, 'destroy']);

    // Kalender Event
    Route::get('/events', [EmitenEventController::class, 'index']);
    Route::post('/events', [EmitenEventController::class, 'store']);
});
