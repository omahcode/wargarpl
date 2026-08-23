@extends('layouts.app')

@section('title', 'Profil Saya - Administrator')

@section('content')
<div class="max-w-4xl space-y-8 animate-fadeIn">
    <!-- Header -->
    <div>
        <h2 class="text-2xl font-bold text-slate-900 font-display">Pengaturan Profil</h2>
        <p class="text-sm text-slate-500 font-medium mt-1">Kelola data akun Anda dan perbarui password keamanan Anda secara berkala.</p>
    </div>

    <!-- Grid Container -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
        <!-- Left Column: Account Summary Card -->
        <div class="bg-white rounded-2xl border border-slate-200 shadow-sm p-6 flex flex-col items-center text-center gap-4">
            <div class="w-20 h-20 rounded-full bg-blue-600 border border-blue-500 text-white flex items-center justify-center font-extrabold text-2xl select-none shadow-sm">
                {{ strtoupper(substr($user->name, 0, 2)) }}
            </div>
            <div>
                <h3 class="text-lg font-bold text-slate-900">{{ $user->name }}</h3>
                <span class="inline-flex items-center gap-1.5 px-3 py-1 mt-1 text-[10px] font-extrabold text-blue-600 bg-blue-50 border border-blue-200 rounded-full uppercase tracking-wider">
                    <span class="w-1.5 h-1.5 rounded-full bg-blue-500"></span>
                    {{ str_replace('_', ' ', $user->role) }}
                </span>
            </div>
            <div class="w-full border-t border-slate-100 pt-4 space-y-2.5 text-left text-xs">
                <div>
                    <span class="text-slate-400 font-medium block">Username</span>
                    <span class="text-slate-800 font-semibold block">{{ $user->username }}</span>
                </div>
                <div>
                    <span class="text-slate-400 font-medium block">Alamat Email</span>
                    <span class="text-slate-800 font-semibold block">{{ $user->email }}</span>
                </div>
                <div>
                    <span class="text-slate-400 font-medium block">Terdaftar Sejak</span>
                    <span class="text-slate-800 font-semibold block">{{ $user->created_at ? Carbon\Carbon::parse($user->created_at)->translatedFormat('d F Y') : '-' }}</span>
                </div>
            </div>
        </div>

        <!-- Right Column: Settings Panel (Ubah Password) -->
        <div class="md:col-span-2 bg-white rounded-2xl border border-slate-200 shadow-sm p-6 space-y-6">
            <div>
                <h3 class="text-sm font-bold text-slate-400 uppercase tracking-wider">Keamanan & Ubah Password</h3>
                <p class="text-xs text-slate-500 mt-1">Perbarui password akun administrator Anda untuk menjaga integritas data sistem.</p>
            </div>

            <!-- Error Messages -->
            @if ($errors->any())
                <div class="p-4 rounded-xl bg-red-50 border border-red-200 text-xs font-semibold text-red-600">
                    <ul class="list-disc list-inside space-y-1">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <!-- Form -->
            <form method="POST" action="{{ route('change-password') }}" class="space-y-4">
                @csrf
                <div>
                    <label for="current_password" class="block text-xs font-bold text-slate-500 uppercase tracking-wider mb-2">Password Saat Ini</label>
                    <input 
                        type="password" 
                        id="current_password" 
                        name="current_password" 
                        required 
                        class="w-full px-3 py-2.5 rounded-xl border border-slate-200 focus:border-slate-900 focus:ring-1 focus:ring-slate-900 outline-none text-sm font-medium transition bg-slate-50/50 focus:bg-white" 
                    />
                </div>
                
                <div>
                    <label for="new_password" class="block text-xs font-bold text-slate-500 uppercase tracking-wider mb-2">Password Baru</label>
                    <input 
                        type="password" 
                        id="new_password" 
                        name="new_password" 
                        required 
                        class="w-full px-3 py-2.5 rounded-xl border border-slate-200 focus:border-slate-900 focus:ring-1 focus:ring-slate-900 outline-none text-sm font-medium transition bg-slate-50/50 focus:bg-white" 
                    />
                    <p class="text-[10px] text-slate-400 mt-1">Minimal harus terdiri dari 8 karakter.</p>
                </div>

                <div>
                    <label for="new_password_confirmation" class="block text-xs font-bold text-slate-500 uppercase tracking-wider mb-2">Konfirmasi Password Baru</label>
                    <input 
                        type="password" 
                        id="new_password_confirmation" 
                        name="new_password_confirmation" 
                        required 
                        class="w-full px-3 py-2.5 rounded-xl border border-slate-200 focus:border-slate-900 focus:ring-1 focus:ring-slate-900 outline-none text-sm font-medium transition bg-slate-50/50 focus:bg-white" 
                    />
                </div>

                <div class="pt-2">
                    <button type="submit" class="px-5 py-2.5 bg-slate-900 hover:bg-slate-800 text-white rounded-xl text-xs font-bold transition shadow-sm active:scale-[0.98]">
                        Perbarui Password
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
