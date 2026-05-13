<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Setting;
use App\Models\Transaction;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;

class AdminApiController extends Controller
{
    // ── Users ─────────────────────────────────────────────────

    public function getUsers(): JsonResponse
    {
        $users = User::withCount('transactions')
            ->orderBy('created_at', 'desc')
            ->get()
            ->map(fn($u) => [
                'id'           => $u->id,
                'name'         => $u->name,
                'email'        => $u->email,
                'role'         => $u->role,
                'status'       => $u->status,
                'balance'      => (float) $u->balance,
                'lots'         => $u->lots,
                'avg_price'    => (float) $u->avg_price,
                'stock'        => $u->stock,
                'transactions_count' => $u->transactions_count,
            ]);

        return response()->json($users);
    }

    public function createUser(Request $request): JsonResponse
    {
        $data = $request->validate([
            'name'     => 'required|string|max:100',
            'email'    => 'required|email|unique:users,email',
            'password' => 'nullable|string|min:6',
            'balance'  => 'nullable|numeric|min:0',
            'role'     => ['nullable', Rule::in(['user', 'admin'])],
            'stock'    => 'nullable|string|max:10',
        ]);

        $user = User::create([
            'name'      => $data['name'],
            'email'     => $data['email'],
            'password'  => Hash::make($data['password'] ?? 'password'),
            'balance'   => $data['balance'] ?? 100000000,
            'role'      => $data['role'] ?? 'user',
            'stock'     => $data['stock'] ?? 'BBCA',
            'status'    => 'active',
        ]);

        return response()->json($user, 201);
    }

    public function updateUser(Request $request, User $user): JsonResponse
    {
        $data = $request->validate([
            'name'             => 'nullable|string|max:100',
            'role'             => ['nullable', Rule::in(['user', 'admin'])],
            'status'           => ['nullable', Rule::in(['active', 'suspended', 'inactive'])],
            'balance'          => 'nullable|numeric|min:0',
            'reset_portfolio'  => 'nullable|boolean',
        ]);

        if (!empty($data['reset_portfolio'])) {
            $data['lots']      = 0;
            $data['avg_price'] = 0;
            $data['balance']   = $data['balance'] ?? 100000000;
            unset($data['reset_portfolio']);
        }

        $user->update(array_filter($data, fn($v) => $v !== null));

        return response()->json($user);
    }

    public function deleteUser(User $user): JsonResponse
    {
        if ($user->id === auth()->id()) {
            return response()->json(['message' => 'Tidak bisa menghapus akun sendiri.'], 403);
        }
        $user->delete();
        return response()->json(['message' => 'User berhasil dihapus.']);
    }

    // ── Transactions ───────────────────────────────────────────

    public function getTransactions(Request $request): JsonResponse
    {
        $query = Transaction::with('user:id,name,email')
            ->orderBy('created_at', 'desc');

        if ($request->type  && $request->type  !== 'all') $query->where('type', $request->type);
        if ($request->stock && $request->stock !== 'all') $query->where('stock', $request->stock);
        if ($request->date)  $query->whereDate('created_at', $request->date);

        $txs = $query->limit(500)->get()->map(fn($t) => [
            'id'       => $t->id,
            'time'     => $t->created_at->toIso8601String(),
            'userId'   => $t->user_id,
            'userName' => $t->user?->name ?? 'Unknown',
            'type'     => $t->type,
            'stock'    => $t->stock,
            'lot'      => $t->lot,
            'price'    => (float) $t->price,
            'total'    => (float) $t->total,
        ]);

        return response()->json($txs);
    }

    // ── Settings ───────────────────────────────────────────────

    public function getSettings(): JsonResponse
    {
        $keys = ['trading_hours', 'weekend_trading', 'maintenance', 'min_lot', 'max_lot', 'commission'];
        $settings = [];
        foreach ($keys as $k) {
            $settings[$k] = Setting::get($k);
        }
        return response()->json($settings);
    }

    public function saveSettings(Request $request): JsonResponse
    {
        $data = $request->validate([
            'trading_hours'   => 'nullable|boolean',
            'weekend_trading' => 'nullable|boolean',
            'maintenance'     => 'nullable|boolean',
            'min_lot'         => 'nullable|integer|min:1',
            'max_lot'         => 'nullable|integer|min:1',
            'commission'      => 'nullable|numeric|min:0',
        ]);

        foreach ($data as $key => $value) {
            Setting::set($key, $value);
        }

        return response()->json(['message' => 'Pengaturan disimpan.']);
    }

    // ── Stats (dashboard KPIs) ─────────────────────────────────

    public function getStats(): JsonResponse
    {
        $totalUsers   = User::count();
        $activeUsers  = User::where('status', 'active')->count();
        $totalTx      = Transaction::count();
        $todayTx      = Transaction::whereDate('created_at', today())->count();
        $totalVolume  = Transaction::sum('total');
        $commission   = (float) Setting::get('commission', '0.15');

        return response()->json([
            'total_users'   => $totalUsers,
            'active_users'  => $activeUsers,
            'total_tx'      => $totalTx,
            'today_tx'      => $todayTx,
            'total_volume'  => (float) $totalVolume,
            'commission'    => $commission,
        ]);
    }
}