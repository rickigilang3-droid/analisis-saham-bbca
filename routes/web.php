<?php
use App\Http\Controllers\Api\DiscussionController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\TradeController;
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

    Route::get('/dashboard', function () {
        if (!auth()->user()->isActive()) {
            auth()->guard()->logout();
            return redirect()->route('login')->with('error', 'Akun kamu tidak aktif.');
        }
        return view('user.dashboard');
    })->name('dashboard');

    Route::get('/api/get-ai-analysis', [TradeController::class, 'getAiAnalysis']);

    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::post('/profile/avatar', [ProfileController::class, 'updateAvatar'])->name('profile.avatar');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

// 3. Admin Panel
Route::middleware(['auth', 'admin'])->group(function () {
    Route::get('/admin', function () {
        return view('admin.dashboard');
    })->name('admin');
});

// 4. Auth Routes
require __DIR__.'/auth.php';