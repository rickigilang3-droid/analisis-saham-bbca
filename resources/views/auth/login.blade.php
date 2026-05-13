<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login — Saham</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-900 flex items-center justify-center min-h-screen">
    <div class="bg-gray-800 p-8 rounded-2xl shadow-xl w-full max-w-md">
        <h1 class="text-2xl font-bold text-white mb-6 text-center">Login Saham</h1>

        @if ($errors->any())
            <div class="bg-red-500/20 border border-red-500/30 text-red-400 p-3 rounded-lg mb-4 text-sm">
                {{ $errors->first() }}
            </div>
        @endif

        @if (session('status'))
            <div class="bg-green-500/20 border border-green-500/30 text-green-400 p-3 rounded-lg mb-4 text-sm">
                {{ session('status') }}
            </div>
        @endif

        <form method="POST" action="{{ route('login') }}">
            @csrf
            <div class="mb-4">
                <label class="block text-gray-400 text-sm mb-1">Email</label>
                <input type="email" name="email" value="{{ old('email') }}" required
                    class="w-full bg-gray-700 text-white rounded-lg px-4 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500 @error('email') ring-2 ring-red-500 @enderror">
            </div>
            <div class="mb-6">
                <label class="block text-gray-400 text-sm mb-1">Password</label>
                <input type="password" name="password" required
                    class="w-full bg-gray-700 text-white rounded-lg px-4 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500 @error('password') ring-2 ring-red-500 @enderror">
                <div class="text-right mt-2">
                    <a href="{{ route('password.request') }}" class="text-sm text-blue-400 hover:text-blue-300">Lupa Password?</a>
                </div>
            </div>
            <button type="submit"
                class="w-full bg-blue-600 hover:bg-blue-700 text-white font-semibold py-2 rounded-lg transition">
                Masuk
            </button>
        </form>

        <p class="text-center text-gray-500 text-sm mt-4">
            Belum punya akun? <a href="{{ route('register') }}" class="text-blue-400 hover:underline">Daftar</a>
        </p>
    </div>
</body>
</html>