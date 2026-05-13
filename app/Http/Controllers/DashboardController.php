<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Transaction;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;

class DashboardController extends Controller
{
    // ─── Helper: cek apakah user adalah admin ────────────────────────────────
    private function isAdmin(): bool
    {
        return Auth::user()?->role === 'admin';
    }

    // ─── 1. Ambil semua user ──────────────────────────────────────────────────
    public function getUsers()
    {
        if (! $this->isAdmin()) {
            return response()->json(['error' => 'Forbidden'], 403);
        }

        $users = User::orderBy('created_at', 'desc')->get();

        return response()->json($users->map(fn($user) => [
            'id'       => $user->id,
            'name'     => $user->name,
            'email'    => $user->email,
            'role'     => $user->role    ?? 'user',
            'status'   => $user->status  ?? 'active',
            'balance'  => (float) ($user->balance ?? 0),
            'lots'     => (int)   ($user->lots    ?? 0),
            'avg'      => (float) ($user->avg     ?? 0),
            'stock'    => $user->stock   ?? 'BBCA',
            'joinDate' => $user->created_at?->toISOString(),
        ]));
    }

    // ─── 2. Ambil semua transaksi ─────────────────────────────────────────────
    public function getTransactions()
    {
        if (! $this->isAdmin()) {
            return response()->json(['error' => 'Forbidden'], 403);
        }

        $transactions = Transaction::with('user')
            ->latest()
            ->take(500)
            ->get();

        return response()->json($transactions->map(fn($tx) => [
            'id'       => $tx->id,
            'userId'   => $tx->user_id,
            'userName' => $tx->user?->name ?? 'Unknown',
            'type'     => $tx->type,
            'stock'    => $tx->stock,
            'lot'      => (int)   $tx->lot,
            'price'    => (float) $tx->price,
            'total'    => (float) $tx->total,
            'time'     => $tx->created_at?->toISOString(),
        ]));
    }

    // ─── 3. Buat user baru ────────────────────────────────────────────────────
    public function createUser(Request $request)
    {
        if (! $this->isAdmin()) {
            return response()->json(['error' => 'Forbidden'], 403);
        }

        $validated = $request->validate([
            'name'     => 'required|string|max:255',
            'email'    => 'required|email|unique:users,email',
            'password' => 'required|string|min:6',
            'role'     => 'required|in:admin,user',
            'balance'  => 'required|numeric|min:0',
            'stock'    => 'nullable|string|max:10',
        ]);

        $user = User::create([
            'name'     => $validated['name'],
            'email'    => $validated['email'],
            'password' => Hash::make($validated['password']),
            'role'     => $validated['role'],
            'balance'  => $validated['balance'],
            'stock'    => $validated['stock'] ?? 'BBCA',
            'lots'     => 0,
            'avg'      => 0,
            'status'   => 'active',
        ]);

        return response()->json([
            'success' => true,
            'user'    => $user,
        ], 201);
    }

    // ─── 4. Update user ───────────────────────────────────────────────────────
    public function updateUser(Request $request, $id)
    {
        if (! $this->isAdmin()) {
            return response()->json(['error' => 'Forbidden'], 403);
        }

        $user = User::find($id);

        if (! $user) {
            return response()->json(['error' => 'User tidak ditemukan'], 404);
        }

        if ($user->id === Auth::id() && $request->has('role')) {
            return response()->json(['error' => 'Tidak bisa mengubah role diri sendiri'], 422);
        }

        $fillable = array_filter([
            'role'   => $request->role,
            'status' => $request->status,
        ], fn($v) => ! is_null($v));

        if ($fillable) {
            $user->fill($fillable);
        }

        if ($request->has('balance')) {
            $user->balance = (float) $request->balance;

            if ($request->boolean('reset_portfolio')) {
                $user->lots = 0;
                $user->avg  = 0;
            }
        }

        $user->save();

        return response()->json(['success' => true, 'user' => $user]);
    }

    // ─── 5. Hapus user ────────────────────────────────────────────────────────
    public function deleteUser($id)
    {
        if (! $this->isAdmin()) {
            return response()->json(['error' => 'Forbidden'], 403);
        }

        $user = User::find($id);

        if (! $user) {
            return response()->json(['error' => 'User tidak ditemukan'], 404);
        }

        if ($user->id === Auth::id()) {
            return response()->json(['error' => 'Tidak bisa menghapus akun sendiri'], 422);
        }

        $user->delete();

        return response()->json(['success' => true]);
    }

    // ─── 6. Logout (semua role boleh) ────────────────────────────────────────
    public function logout(Request $request)
    {
        $token = $request->user()?->currentAccessToken();

        if (! $token) {
            return response()->json(['error' => 'Tidak ada token aktif'], 401);
        }

        $token->delete();

        return response()->json([
            'success' => true,
            'message' => 'Berhasil logout.',
        ]);
    }
}