<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - SobatData Neper</title>
    <link rel="icon" type="image/svg+xml" href="{{ asset('logo-smk.svg') }}">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=instrument-sans:400,500,600,700" rel="stylesheet" />
    <style>
        body {
            font-family: 'Instrument Sans', sans-serif;
        }
    </style>
</head>
<body class="bg-slate-50 text-slate-900 min-h-screen flex items-center justify-center p-4">
    <div class="w-full max-w-md bg-white rounded-2xl border border-slate-200 shadow-sm p-8">
        <!-- Logo and Heading -->
        <div class="text-center mb-8">
            <img src="{{ asset('logo-smk.svg') }}" class="w-16 h-16 object-contain mx-auto mb-4" alt="Logo SMK" />
            <h2 class="text-2xl font-bold tracking-tight text-slate-900">SobatData Neper</h2>
            <p class="text-xs text-slate-500 font-semibold mt-1">Sistem Informasi Data Terpadu Neper<br>SMK Negeri 1 Cirebon</p>
        </div>

        <!-- Session Status / Errors -->
        @if ($errors->any())
            <div class="mb-6 p-4 rounded-xl bg-red-50 border border-red-200 text-sm text-red-600 font-medium">
                {{ $errors->first() }}
            </div>
        @endif

        @if (session('success'))
            <div class="mb-6 p-4 rounded-xl bg-green-50 border border-green-200 text-sm text-green-600 font-medium">
                {{ session('success') }}
            </div>
        @endif

        <!-- Login Form -->
        <form method="POST" action="{{ route('login') }}" class="space-y-5">
            @csrf

            <div>
                <label for="login" class="block text-xs font-semibold text-slate-500 uppercase tracking-wider mb-2">NIS / NIP / Email</label>
                <input 
                    type="text" 
                    id="login" 
                    name="login" 
                    value="{{ old('login') }}" 
                    required 
                    autofocus
                    placeholder="Masukkan Nomor Identitas atau Email" 
                    class="w-full px-4 py-3 rounded-xl border border-slate-200 focus:border-slate-900 focus:ring-1 focus:ring-slate-900 outline-none text-sm transition font-medium placeholder-slate-400 bg-slate-50/50 focus:bg-white"
                />
            </div>

            <div>
                <label for="password" class="block text-xs font-semibold text-slate-500 uppercase tracking-wider mb-2">Password</label>
                <input 
                    type="password" 
                    id="password" 
                    name="password" 
                    required 
                    placeholder="Masukkan Password Anda" 
                    class="w-full px-4 py-3 rounded-xl border border-slate-200 focus:border-slate-900 focus:ring-1 focus:ring-slate-900 outline-none text-sm transition font-medium placeholder-slate-400 bg-slate-50/50 focus:bg-white"
                />
            </div>

            <div class="flex items-center justify-between pt-1">
                <label class="flex items-center text-sm text-slate-600 font-medium cursor-pointer">
                    <input type="checkbox" name="remember" class="w-4 h-4 rounded border-slate-300 text-slate-900 focus:ring-slate-900 mr-2 cursor-pointer">
                    Ingat Saya
                </label>
            </div>

            <div>
                <button 
                    type="submit" 
                    class="w-full py-3 bg-slate-900 hover:bg-slate-800 text-white rounded-xl text-sm font-semibold transition-all shadow-sm active:scale-[0.99] mt-2"
                >
                    MASUK
                </button>
            </div>
        </form>

        <!-- Footer Note -->
        <div class="text-center mt-8 pt-6 border-t border-slate-100">
            <span class="text-xs text-slate-400 font-medium">Single Identity & Master Data Portal</span>
        </div>
    </div>
</body>
</html>
