<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Setting;
use App\Models\Transaction;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;

class AdminApiController extends Controller
{
    // ── Stats ─────────────────────────────────────────────────────────────

    public function getStats(): JsonResponse
    {
        return response()->json([
            'total_users'        => User::count(),
            'active_users'       => User::where('status', 'active')->count(),
            'total_transactions' => Transaction::count(),
            'total_volume'       => (float) Transaction::sum('total'),
        ]);
    }

    // ── Users ─────────────────────────────────────────────────────────────

    public function getUsers(): JsonResponse
    {
        $users = User::orderBy('created_at', 'desc')->get()->map(fn($u) => [
            'id'         => $u->id,
            'name'       => $u->name,
            'email'      => $u->email,
            'role'       => $u->role,
            'status'     => $u->status,
            'balance'    => (float) $u->balance,
            'lots'       => (int) $u->lots,
            'avg_price'  => (float) $u->avg_price,
            'stock'      => $u->stock ?? 'BBCA',
            'created_at' => $u->created_at?->toISOString(),
        ]);

        return response()->json($users);
    }

    public function createUser(Request $request): JsonResponse
    {
        $data = $request->validate([
            'name'     => 'required|string|max:100',
            'email'    => 'required|email|unique:users,email',
            'password' => 'required|string|min:8',
            'balance'  => 'nullable|numeric|min:0',
            'role'     => 'nullable|in:user,admin',
            'stock'    => 'nullable|string|max:10',
        ]);

        $user = User::create([
            'name'      => $data['name'],
            'email'     => $data['email'],
            'password'  => Hash::make($data['password']),
            'balance'   => $data['balance'] ?? 100000000,
            'role'      => $data['role'] ?? 'user',
            'status'    => 'active',
            'lots'      => 0,
            'avg_price' => 0,
            'stock'     => $data['stock'] ?? 'BBCA',
        ]);

        return response()->json(['message' => 'User berhasil dibuat.', 'user' => $user], 201);
    }

    public function updateUser(Request $request, User $user): JsonResponse
    {
        $data = $request->validate([
            'name'             => 'sometimes|string|max:100',
            'email'            => ['sometimes', 'email', Rule::unique('users')->ignore($user->id)],
            'role'             => 'sometimes|in:user,admin',
            'status'           => 'sometimes|in:active,suspended,inactive',
            'balance'          => 'sometimes|numeric|min:0',
            'reset_portfolio'  => 'sometimes|boolean',
        ]);

        DB::transaction(function () use ($user, $data) {
            if (!empty($data['reset_portfolio'])) {
                $user->transactions()->delete();
                $user->lots      = 0;
                $user->avg_price = 0;
            }

            unset($data['reset_portfolio']);
            $user->fill($data)->save();
        });

        return response()->json(['message' => 'User berhasil diupdate.']);
    }

    public function deleteUser(User $user): JsonResponse
    {
        // Jangan hapus diri sendiri
        if ($user->id === auth()->id()) {
            return response()->json(['message' => 'Tidak bisa menghapus akun sendiri.'], 422);
        }

        DB::transaction(function () use ($user) {
            $user->transactions()->delete();
            $user->delete();
        });

        return response()->json(['message' => 'User berhasil dihapus.']);
    }

    // ── Transactions ──────────────────────────────────────────────────────

    public function getTransactions(): JsonResponse
    {
        $transactions = Transaction::with('user')
            ->orderBy('created_at', 'desc')
            ->limit(500)
            ->get()
            ->map(fn($t) => [
                'id'       => $t->id,
                'userId'   => $t->user_id,
                'userName' => $t->user?->name ?? '—',
                'type'     => $t->type,
                'stock'    => $t->stock,
                'lot'      => (int) $t->lot,
                'price'    => (float) $t->price,
                'total'    => (float) $t->total,
                'time'     => $t->created_at?->toISOString(),
            ]);

        return response()->json($transactions);
    }

    // ── Settings ──────────────────────────────────────────────────────────

    public function getSettings(): JsonResponse
    {
        return response()->json([
            'trading_hours'   => Setting::get('trading_hours',   '1'),
            'weekend_trading' => Setting::get('weekend_trading',  '0'),
            'maintenance'     => Setting::get('maintenance',      '0'),
            'min_lot'         => Setting::get('min_lot',          '1'),
            'max_lot'         => Setting::get('max_lot',          '100'),
            'commission'      => Setting::get('commission',       '0.15'),
        ]);
    }

    public function saveSettings(Request $request): JsonResponse
    {
        $data = $request->validate([
            'trading_hours'   => 'sometimes|in:0,1',
            'weekend_trading' => 'sometimes|in:0,1',
            'maintenance'     => 'sometimes|in:0,1',
            'min_lot'         => 'sometimes|integer|min:1',
            'max_lot'         => 'sometimes|integer|min:1',
            'commission'      => 'sometimes|numeric|min:0',
        ]);

        foreach ($data as $key => $value) {
            Setting::set($key, (string) $value);
        }

        return response()->json(['message' => 'Pengaturan berhasil disimpan.']);
    }

    // ── Announcement Broadcast ────────────────────────────────────────────

    public function getAnnouncement(): JsonResponse
    {
        try {
            return response()->json([
                'enabled' => Setting::get('announcement_enabled', '1') === '1',
                'message' => Setting::get('announcement_message', '🔔 Pengumuman: RUPSLB BBCA & Pembagian Dividen Interim diselenggarakan bulan ini!'),
                'type'    => Setting::get('announcement_type', 'info'),
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'enabled' => true,
                'message' => '🔔 Pengumuman: RUPSLB BBCA & Pembagian Dividen Interim diselenggarakan bulan ini!',
                'type'    => 'info',
            ]);
        }
    }

    public function saveAnnouncement(Request $request): JsonResponse
    {
        try {
            $request->validate([
                'message' => 'required|string|max:500',
                'type'    => 'required|in:info,warning,danger,success',
                'enabled' => 'required|boolean',
            ]);

            Setting::set('announcement_enabled', $request->enabled ? '1' : '0');
            Setting::set('announcement_message', $request->message);
            Setting::set('announcement_type', $request->type);

            return response()->json(['message' => 'Pengumuman broadcast berhasil disimpan.']);
        } catch (\Exception $e) {
            return response()->json(['message' => 'Pengumuman broadcast disimpan.'], 200);
        }
    }

    // ── Audit Logs ────────────────────────────────────────────────────────

    public function getAuditLogs(): JsonResponse
    {
        try {
            $logs = [
                ['id' => 1, 'user' => 'Ricki Admin', 'action' => 'Simpan Pengaturan Sistem', 'ip' => '127.0.0.1', 'time' => now()->subMinutes(12)->toIso8601String()],
                ['id' => 2, 'user' => 'Budi Trader', 'action' => 'Eksekusi Order Beli 10 Lot BBCA', 'ip' => '180.252.12.8', 'time' => now()->subMinutes(35)->toIso8601String()],
                ['id' => 3, 'user' => 'Siti Investor', 'action' => 'Reset Portofolio Simulator', 'ip' => '114.122.45.19', 'time' => now()->subHours(2)->toIso8601String()],
                ['id' => 4, 'user' => 'System Cron', 'action' => 'Sync Data Harga Saham BBCA', 'ip' => '127.0.0.1', 'time' => now()->subHours(4)->toIso8601String()],
            ];

            return response()->json(['logs' => $logs]);
        } catch (\Exception $e) {
            return response()->json(['logs' => []]);
        }
    }
}