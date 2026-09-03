<?php
use Illuminate\Support\Facades\Route;
use Illuminate\Http\Request;
use App\Http\Controllers\Api\TradeController;
use App\Http\Controllers\Api\DiscussionController;
use App\Http\Controllers\Api\AdminApiController;
use App\Http\Controllers\EventController;
use App\Http\Controllers\ProfileController;

// 1. Root redirect
Route::get('/', function () {
    if (!auth()->check()) {
        return redirect()->route('login');
    }

    if (!auth()->user()->isActive()) {
        auth()->guard()->logout();
        return redirect()->route('login')->with('error', 'Akun kamu tidak aktif.');
    }

    return auth()->user()->isAdmin()
        ? redirect()->route('admin')
        : redirect()->route('dashboard');
});

// 2. User Biasa
Route::middleware(['auth'])->group(function () {

    Route::get('/events', [EventController::class, 'index'])->name('events.index');
    Route::post('/events', [EventController::class, 'store'])->name('events.store');
    Route::get('/events/{event}', [EventController::class, 'show'])->name('events.show');

    Route::get('/discussions', [DiscussionController::class, 'index'])->name('discussions.index');
    Route::post('/discussions', [DiscussionController::class, 'store'])->name('discussions.store');
    Route::get('/discussions/{discussion}', [DiscussionController::class, 'show'])->name('discussions.show');
    Route::post('/discussions/{id}/comments', [DiscussionController::class, 'addComment'])->name('discussions.comment');
    Route::post('/discussions/{id}/like', [DiscussionController::class, 'like'])->name('discussions.like');

    Route::get('/dashboard', function () {
        if (!auth()->user()->isActive()) {
            auth()->guard()->logout();
            return redirect()->route('login')->with('error', 'Akun kamu tidak aktif.');
        }
        return view('user.dashboard');
    })->name('dashboard');

    Route::match(['get', 'post'], '/api/ai/analyze', [TradeController::class, 'analyze']);
    Route::get('/api/get-ai-analysis', [TradeController::class, 'analyze']);

    Route::post('/api/trade',             [TradeController::class, 'execute']);
    Route::get('/api/portfolio',          [TradeController::class, 'portfolio']);
    Route::get('/api/trade/history',      [TradeController::class, 'history']);
    Route::post('/api/trade/reset',       [TradeController::class, 'reset']);

    Route::get('/api/watchlist',          [App\Http\Controllers\Api\WatchlistController::class, 'index']);
    Route::post('/api/watchlist',         [App\Http\Controllers\Api\WatchlistController::class, 'store']);
    Route::put('/api/watchlist/{watchlist}', [App\Http\Controllers\Api\WatchlistController::class, 'update']);
    Route::delete('/api/watchlist/{watchlist}', [App\Http\Controllers\Api\WatchlistController::class, 'destroy']);

    Route::get('/api/market/fundamentals',[App\Http\Controllers\Api\MarketController::class, 'fundamentals']);
    Route::get('/api/market/backtest',    [App\Http\Controllers\Api\MarketController::class, 'backtest']);
    Route::get('/api/market/performance', [App\Http\Controllers\Api\MarketController::class, 'performance']);
    Route::get('/api/market/analysis',    [App\Http\Controllers\Api\MarketController::class, 'analysis']);
    Route::get('/api/market/sentiment',   [App\Http\Controllers\Api\MarketController::class, 'sentiment']);

    Route::get('/api/discussions', [DiscussionController::class, 'index']);
    Route::post('/api/discussions', [DiscussionController::class, 'store']);
    Route::post('/api/discussions/{id}/comments', [DiscussionController::class, 'addComment']);
    Route::post('/api/discussions/{id}/like', [DiscussionController::class, 'like']);
    Route::delete('/api/discussions/{id}', [DiscussionController::class, 'destroy']);

    Route::get('/api/events', [App\Http\Controllers\Api\EmitenEventController::class, 'index']);
    Route::post('/api/events', [App\Http\Controllers\Api\EmitenEventController::class, 'store']);

    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::post('/profile/avatar', [ProfileController::class, 'updateAvatar'])->name('profile.avatar');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

// 3. Admin Panel
Route::middleware(['auth', 'admin'])->prefix('admin')->group(function () {
    Route::get('/', function () {
        return view('admin.dashboard');
    })->name('admin');

    Route::get('/dashboard', function () {
        return view('admin.dashboard');
    });

    Route::get('/users', function (Request $request) {
        if ($request->expectsJson() || $request->ajax()) {
            return app(AdminApiController::class)->getUsers();
        }
        return view('admin.dashboard');
    })->name('admin.users');

    Route::get('/transactions', function (Request $request) {
        if ($request->expectsJson() || $request->ajax()) {
            return app(AdminApiController::class)->getTransactions();
        }
        return view('admin.dashboard');
    })->name('admin.transactions');

    Route::get('/reports', function () {
        return view('admin.dashboard');
    })->name('admin.reports');

    Route::get('/settings', function (Request $request) {
        if ($request->expectsJson() || $request->ajax()) {
            return app(AdminApiController::class)->getSettings();
        }
        return view('admin.dashboard');
    })->name('admin.settings');

    Route::get('/stats', [AdminApiController::class, 'getStats'])->name('admin.stats');
    Route::post('/users', [AdminApiController::class, 'createUser']);
    Route::put('/users/{user}', [AdminApiController::class, 'updateUser']);
    Route::delete('/users/{user}', [AdminApiController::class, 'deleteUser']);
    Route::post('/settings', [AdminApiController::class, 'saveSettings']);
});

// 4. Auth Routes
require __DIR__.'/auth.php';