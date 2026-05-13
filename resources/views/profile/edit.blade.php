<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Profil Saya — Saham</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-900 text-white">
    <!-- Navigation -->
    <nav class="bg-gray-800 border-b border-gray-700">
        <div class="max-w-6xl mx-auto px-4 py-4 flex justify-between items-center">
            <a href="{{ route('dashboard') }}" class="text-xl font-bold text-blue-400">📈 Saham</a>
            <div class="flex items-center gap-4">
                <a href="{{ route('dashboard') }}" class="text-gray-400 hover:text-white">Dashboard</a>
                <form method="POST" action="{{ route('logout') }}" style="display: inline;">
                    @csrf
                    <button type="submit" class="text-gray-400 hover:text-white">Logout</button>
                </form>
            </div>
        </div>
    </nav>

    <div class="max-w-4xl mx-auto px-4 py-8">
        <!-- Header -->
        <div class="mb-8">
            <h1 class="text-3xl font-bold mb-2">Profil Saya</h1>
            <p class="text-gray-400">Kelola informasi akun Anda</p>
        </div>

        <!-- Success Message -->
        @if (session('status') === 'profile-updated')
            <div class="bg-green-500/20 border border-green-500/30 text-green-400 p-4 rounded-lg mb-6">
                ✓ Profil berhasil diperbarui
            </div>
        @elseif (session('status') === 'avatar-updated')
            <div class="bg-green-500/20 border border-green-500/30 text-green-400 p-4 rounded-lg mb-6">
                ✓ Foto profil berhasil diperbarui
            </div>
        @endif

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            <!-- Sidebar - Avatar Section -->
            <div class="lg:col-span-1">
                <div class="bg-gray-800 rounded-lg p-6 border border-gray-700">
                    <h3 class="text-lg font-semibold mb-4">Foto Profil</h3>
                    
                    <!-- Avatar Display -->
                    <div class="mb-6 text-center">
                        @if ($avatarUrl)
                            <img src="{{ $avatarUrl }}" alt="{{ $user->name }}" 
                                class="w-32 h-32 rounded-full mx-auto object-cover border-4 border-blue-500">
                        @else
                            <div class="w-32 h-32 rounded-full mx-auto bg-gray-700 flex items-center justify-center border-4 border-gray-600">
                                <span class="text-3xl">👤</span>
                            </div>
                        @endif
                    </div>

                    <!-- Avatar Upload Form -->
                    <form action="{{ route('profile.avatar') }}" method="POST" enctype="multipart/form-data">
                        @csrf
                        <div class="mb-4">
                            <label class="block text-sm text-gray-400 mb-2">Unggah Foto (Max 2MB)</label>
                            <input type="file" name="avatar" accept="image/*" 
                                class="w-full bg-gray-700 text-white rounded px-3 py-2 text-sm cursor-pointer" required>
                            @error('avatar')
                                <p class="text-red-400 text-xs mt-1">{{ $message }}</p>
                            @enderror
                        </div>
                        <button type="submit" class="w-full bg-blue-600 hover:bg-blue-700 text-white font-semibold py-2 rounded transition">
                            Unggah Foto
                        </button>
                    </form>

                    <!-- User Stats -->
                    <div class="mt-6 pt-6 border-t border-gray-700">
                        <div class="mb-4">
                            <p class="text-xs text-gray-500 mb-1">SALDO</p>
                            <p class="text-lg font-bold text-green-400">Rp {{ number_format($user->balance, 0, ',', '.') }}</p>
                        </div>
                        <div class="mb-4">
                            <p class="text-xs text-gray-500 mb-1">ROLE</p>
                            <p class="text-lg font-bold capitalize">{{ $user->role === 'admin' ? 'Administrator' : 'Pengguna' }}</p>
                        </div>
                        <div>
                            <p class="text-xs text-gray-500 mb-1">STATUS</p>
                            <p class="text-lg font-bold">
                                <span class="px-2 py-1 rounded text-xs {{ $user->is_active ? 'bg-green-500/20 text-green-400' : 'bg-red-500/20 text-red-400' }}">
                                    {{ $user->is_active ? 'Aktif' : 'Nonaktif' }}
                                </span>
                            </p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Main Content -->
            <div class="lg:col-span-2 space-y-6">
                <!-- Edit Profile Information -->
                <div class="bg-gray-800 rounded-lg p-6 border border-gray-700">
                    <h3 class="text-lg font-semibold mb-4">Informasi Profil</h3>
                    
                    <form method="POST" action="{{ route('profile.update') }}">
                        @csrf
                        @method('PATCH')

                        <div class="mb-4">
                            <label class="block text-gray-400 text-sm mb-1">Nama Lengkap</label>
                            <input type="text" name="name" value="{{ old('name', $user->name) }}" required
                                class="w-full bg-gray-700 text-white rounded-lg px-4 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500 @error('name') ring-2 ring-red-500 @enderror">
                            @error('name')
                                <p class="text-red-400 text-xs mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        <div class="mb-4">
                            <label class="block text-gray-400 text-sm mb-1">Email</label>
                            <input type="email" name="email" value="{{ old('email', $user->email) }}" required
                                class="w-full bg-gray-700 text-white rounded-lg px-4 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500 @error('email') ring-2 ring-red-500 @enderror">
                            @error('email')
                                <p class="text-red-400 text-xs mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white font-semibold py-2 px-6 rounded transition">
                            Simpan Perubahan
                        </button>
                    </form>
                </div>

                <!-- Change Password -->
                <div class="bg-gray-800 rounded-lg p-6 border border-gray-700">
                    <h3 class="text-lg font-semibold mb-4">Ubah Password</h3>
                    
                    <form method="POST" action="{{ route('password.update') }}">
                        @csrf
                        @method('PUT')

                        <div class="mb-4">
                            <label class="block text-gray-400 text-sm mb-1">Password Lama</label>
                            <input type="password" name="current_password" required
                                class="w-full bg-gray-700 text-white rounded-lg px-4 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500 @error('current_password') ring-2 ring-red-500 @enderror">
                            @error('current_password')
                                <p class="text-red-400 text-xs mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        <div class="mb-4">
                            <label class="block text-gray-400 text-sm mb-1">Password Baru</label>
                            <input type="password" name="password" required
                                class="w-full bg-gray-700 text-white rounded-lg px-4 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500 @error('password') ring-2 ring-red-500 @enderror">
                            @error('password')
                                <p class="text-red-400 text-xs mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        <div class="mb-6">
                            <label class="block text-gray-400 text-sm mb-1">Konfirmasi Password</label>
                            <input type="password" name="password_confirmation" required
                                class="w-full bg-gray-700 text-white rounded-lg px-4 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500">
                        </div>

                        <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white font-semibold py-2 px-6 rounded transition">
                            Ubah Password
                        </button>
                    </form>
                </div>

                <!-- Danger Zone -->
                <div class="bg-red-900/20 border border-red-500/30 rounded-lg p-6">
                    <h3 class="text-lg font-semibold mb-4 text-red-400">⚠️ Zona Berbahaya</h3>
                    
                    <form method="POST" action="{{ route('profile.destroy') }}" onsubmit="return confirm('Yakin ingin menghapus akun? Tindakan ini tidak dapat dibatalkan.');">
                        @csrf
                        @method('DELETE')

                        <p class="text-gray-400 text-sm mb-4">Hapus akun Anda secara permanen. Tindakan ini tidak dapat dibatalkan.</p>

                        <div class="mb-4">
                            <label class="block text-gray-400 text-sm mb-1">Masukkan Password untuk Konfirmasi</label>
                            <input type="password" name="password" required
                                class="w-full bg-gray-700 text-white rounded-lg px-4 py-2 focus:outline-none focus:ring-2 focus:ring-red-500 @error('password') ring-2 ring-red-500 @enderror">
                            @error('password')
                                <p class="text-red-400 text-xs mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        <button type="submit" class="bg-red-600 hover:bg-red-700 text-white font-semibold py-2 px-6 rounded transition">
                            Hapus Akun
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</body>
</html>
