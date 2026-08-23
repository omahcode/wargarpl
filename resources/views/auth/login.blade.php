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
            background-color: #fafbfc;
        }
    </style>
</head>
<body class="text-slate-900 min-h-screen flex flex-col justify-between p-6">
    <!-- Top Spacer -->
    <div></div>

    <!-- Main Content Container -->
    <div class="w-full max-w-[390px] mx-auto my-auto space-y-6">
        <!-- Logo and Title (No card wrapper around logo) -->
        <div class="text-center">
            <div class="inline-flex items-center justify-center mb-4 transition-transform duration-300 hover:scale-105">
                <img src="{{ asset('logo-smk.svg') }}" class="w-16 h-16 object-contain" alt="Logo SMK" />
            </div>
            <h2 class="text-xl font-bold tracking-tight text-slate-950 font-display">SobatData Neper</h2>
            <p class="text-xs text-slate-400 font-medium mt-1">
                Portal Data Terpadu Pendidik & Peserta Didik <br>
                <span class="text-slate-500 font-semibold">SMK Negeri 1 Cirebon</span>
            </p>
        </div>

        <!-- Form Card (Ultra Minimalist) -->
        <div class="bg-white border border-slate-100 rounded-3xl p-6 sm:p-8 shadow-sm">
            <!-- Session Status / Errors -->
            @if ($errors->any())
                <div class="mb-5 p-3.5 rounded-xl bg-red-50 text-xs text-red-600 font-semibold flex items-start gap-2">
                    <svg class="w-4 h-4 text-red-500 flex-shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path>
                    </svg>
                    <span>{{ $errors->first() }}</span>
                </div>
            @endif

            @if (session('success'))
                <div class="mb-5 p-3.5 rounded-xl bg-green-50 text-xs text-green-600 font-semibold flex items-start gap-2">
                    <svg class="w-4 h-4 text-green-500 flex-shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                    <span>{{ session('success') }}</span>
                </div>
            @endif

            <!-- Login Form -->
            <form method="POST" action="{{ route('login') }}" class="space-y-4">
                @csrf

                <!-- Input Login -->
                <div>
                    <label for="login" class="block text-[10px] font-bold text-slate-400 uppercase tracking-wider mb-1.5">NIS / NIP / Email</label>
                    <div class="relative">
                        <span class="absolute inset-y-0 left-0 flex items-center pl-3.5 text-slate-400">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                            </svg>
                        </span>
                        <input 
                            type="text" 
                            id="login" 
                            name="login" 
                            value="{{ old('login') }}" 
                            required 
                            autofocus
                            placeholder="Masukkan identitas Anda" 
                            class="w-full pl-10 pr-4 py-2.5 rounded-xl border border-slate-200 focus:border-slate-950 focus:ring-0 outline-none text-xs font-semibold placeholder-slate-400 transition"
                        />
                    </div>
                </div>

                <!-- Input Password -->
                <div x-data="{ showPassword: false }">
                    <label for="password" class="block text-[10px] font-bold text-slate-400 uppercase tracking-wider mb-1.5">Password</label>
                    <div class="relative">
                        <span class="absolute inset-y-0 left-0 flex items-center pl-3.5 text-slate-400">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"></path>
                            </svg>
                        </span>
                        <input 
                            :type="showPassword ? 'text' : 'password'" 
                            id="password" 
                            name="password" 
                            required 
                            placeholder="••••••••" 
                            class="w-full pl-10 pr-10 py-2.5 rounded-xl border border-slate-200 focus:border-slate-950 focus:ring-0 outline-none text-xs font-semibold placeholder-slate-400 transition"
                        />
                        <button 
                            type="button" 
                            @click="showPassword = !showPassword" 
                            class="absolute inset-y-0 right-0 flex items-center pr-3 text-slate-400 hover:text-slate-600 focus:outline-none"
                        >
                            <!-- Eye Icon (Show) -->
                            <template x-if="!showPassword">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path></svg>
                            </template>
                            <!-- Eye Off Icon (Hide) -->
                            <template x-if="showPassword">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.542-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.88 9.88l-3.29-3.29m7.532 7.532l3.29 3.29M3 3l18 18"></path></svg>
                            </template>
                        </button>
                    </div>
                </div>

                <!-- Remember Me -->
                <div class="flex items-center justify-between pt-0.5">
                    <label class="flex items-center text-[11px] text-slate-500 font-bold cursor-pointer select-none">
                        <input type="checkbox" name="remember" class="w-3.5 h-3.5 rounded border-slate-300 text-slate-950 focus:ring-0 mr-2 cursor-pointer accent-slate-950">
                        Ingat Saya
                    </label>
                </div>

                <!-- Submit Button -->
                <div class="pt-1">
                    <button 
                        type="submit" 
                        class="w-full py-2.5 bg-slate-950 hover:bg-slate-900 active:scale-[0.98] text-white rounded-xl text-xs font-bold transition shadow-sm"
                    >
                        Masuk ke Portal
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- Footer Note -->
    <div class="text-center w-full">
        <p class="text-[9px] text-slate-400 font-bold uppercase tracking-wider">Single Identity & Master Data Portal</p>
        <p class="text-[8px] text-slate-400 mt-0.5">&copy; {{ date('Y') }} SMKN 1 Cirebon. All rights reserved.</p>
    </div>
</body>
</html>
