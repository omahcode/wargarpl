<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Dashboard') - SIDATA NEPER</title>
    <link rel="icon" type="image/svg+xml" href="{{ asset('logo-smk.svg') }}">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=instrument-sans:400,500,600,700" rel="stylesheet" />
    <style>
        body {
            font-family: 'Instrument Sans', sans-serif;
        }
        /* Remove sidebar scrollbar */
        .no-scrollbar::-webkit-scrollbar {
            display: none;
        }
        .no-scrollbar {
            -ms-overflow-style: none;  /* IE and Edge */
            scrollbar-width: none;  /* Firefox */
        }
    </style>
    @yield('styles')
</head>
<body class="bg-slate-50 text-slate-800 antialiased min-h-screen flex" x-data="{ mobileMenuOpen: false }">

    <!-- Mobile Sidebar Backdrop -->
    <div class="fixed inset-0 z-40 bg-slate-900/40 lg:hidden" 
         x-show="mobileMenuOpen" 
         x-transition:enter="transition ease-out duration-300"
         x-transition:enter-start="opacity-0"
         x-transition:enter-end="opacity-100"
         x-transition:leave="transition ease-in duration-200"
         x-transition:leave-start="opacity-100"
         x-transition:leave-end="opacity-0"
         @click="mobileMenuOpen = false" 
         style="display: none;">
    </div>

    <!-- Sidebar -->
    <aside class="fixed inset-y-0 left-0 z-50 w-72 bg-slate-950 text-slate-300 flex flex-col transform lg:translate-x-0 lg:static transition-transform duration-300 ease-in-out border-r border-slate-900"
           :class="mobileMenuOpen ? 'translate-x-0' : '-translate-x-full'">
        
        <!-- Header/Logo -->
        <div class="h-16 px-6 flex items-center justify-between border-b border-slate-900 bg-slate-950">
            <div class="flex items-center gap-3">
                <img src="{{ asset('logo-smk.svg') }}" class="w-8 h-8 object-contain" alt="Logo SMK" />
                <div>
                    <h1 class="text-xs font-bold text-white tracking-wide">SIDATA NEPER</h1>
                    <p class="text-[9px] text-slate-500 font-semibold uppercase tracking-wider">SMKN 1 Cirebon</p>
                </div>
            </div>
            <!-- Close Button Mobile -->
            <button @click="mobileMenuOpen = false" class="text-slate-400 hover:text-white lg:hidden">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
            </button>
        </div>

        <!-- Navigation Links (no-scrollbar) -->
        <nav class="flex-1 p-4 space-y-6 overflow-y-auto no-scrollbar select-none">
            @if(auth()->user()->isAdmin())
                <!-- Admin Navigation -->
                <div>
                    <div class="px-3 text-[10px] font-bold text-slate-500 uppercase tracking-wider mb-2">Utama</div>
                    <div class="space-y-1">
                        <a href="{{ route('admin.dashboard') }}" class="group flex items-center gap-3 px-3 py-2.5 rounded-xl text-sm font-medium transition {{ request()->routeIs('admin.dashboard') ? 'bg-slate-900 text-white font-semibold' : 'hover:bg-slate-900/50 hover:text-white' }}">
                            <svg class="w-5 h-5 text-slate-400 group-hover:text-white transition" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6a2 2 0 012-2h2a2 2 0 012 2v4a2 2 0 01-2 2H6a2 2 0 01-2-2V6zM14 6a2 2 0 012-2h2a2 2 0 012 2v4a2 2 0 01-2 2h-2a2 2 0 01-2-2V6zM4 16a2 2 0 012-2h2a2 2 0 012 2v4a2 2 0 01-2 2H6a2 2 0 01-2-2v-4zM14 16a2 2 0 012-2h2a2 2 0 012 2v4a2 2 0 01-2 2h-2a2 2 0 01-2-2v-4z"></path></svg>
                            Dashboard
                        </a>
                    </div>
                </div>

                <div>
                    <div class="px-3 text-[10px] font-bold text-slate-500 uppercase tracking-wider mb-2">Data Master</div>
                    <div class="space-y-1">
                        <a href="{{ route('admin.students.index') }}" class="group flex items-center gap-3 px-3 py-2.5 rounded-xl text-sm font-medium transition {{ request()->routeIs('admin.students.*') ? 'bg-slate-900 text-white font-semibold' : 'hover:bg-slate-900/50 hover:text-white' }}">
                            <svg class="w-5 h-5 text-slate-400 group-hover:text-white transition" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 14l9-5-9-5-9 5 9 5zm0 0l6.16-3.422a12.083 12.083 0 01.665 6.479A11.952 11.952 0 0012 20.055a11.952 11.952 0 00-6.824-2.998 12.078 12.078 0 01.665-6.479L12 14zm-4 6v-7.5l4-2.222"></path></svg>
                            Siswa
                        </a>
                        <a href="{{ route('admin.employees.index') }}" class="group flex items-center gap-3 px-3 py-2.5 rounded-xl text-sm font-medium transition {{ request()->routeIs('admin.employees.*') ? 'bg-slate-900 text-white font-semibold' : 'hover:bg-slate-900/50 hover:text-white' }}">
                            <svg class="w-5 h-5 text-slate-400 group-hover:text-white transition" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path></svg>
                            Guru & Tendik
                        </a>
                        <a href="{{ route('admin.classes.index') }}" class="group flex items-center gap-3 px-3 py-2.5 rounded-xl text-sm font-medium transition {{ request()->routeIs('admin.classes.*') ? 'bg-slate-900 text-white font-semibold' : 'hover:bg-slate-900/50 hover:text-white' }}">
                            <svg class="w-5 h-5 text-slate-400 group-hover:text-white transition" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"></path></svg>
                            Rombongan Belajar
                        </a>
                        <a href="{{ route('admin.majors.index') }}" class="group flex items-center gap-3 px-3 py-2.5 rounded-xl text-sm font-medium transition {{ request()->routeIs('admin.majors.*') ? 'bg-slate-900 text-white font-semibold' : 'hover:bg-slate-900/50 hover:text-white' }}">
                            <svg class="w-5 h-5 text-slate-400 group-hover:text-white transition" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"></path></svg>
                            Jurusan
                        </a>
                    </div>
                </div>

                <div>
                    <div class="px-3 text-[10px] font-bold text-slate-500 uppercase tracking-wider mb-2">Verifikasi Data</div>
                    <div class="space-y-1">
                        <a href="{{ route('admin.monitoring') }}" class="group flex items-center gap-3 px-3 py-2.5 rounded-xl text-sm font-medium transition {{ request()->routeIs('admin.monitoring') ? 'bg-slate-900 text-white font-semibold' : 'hover:bg-slate-900/50 hover:text-white' }}">
                            <svg class="w-5 h-5 text-slate-400 group-hover:text-white transition" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path></svg>
                            Monitoring Campaign
                        </a>
                    </div>
                </div>

                <div>
                    <div class="px-3 text-[10px] font-bold text-slate-500 uppercase tracking-wider mb-2">Integrasi</div>
                    <div class="space-y-1">
                        <a href="{{ route('admin.clients.index') }}" class="group flex items-center gap-3 px-3 py-2.5 rounded-xl text-sm font-medium transition {{ request()->routeIs('admin.clients.*') ? 'bg-slate-900 text-white font-semibold' : 'hover:bg-slate-900/50 hover:text-white' }}">
                            <svg class="w-5 h-5 text-slate-400 group-hover:text-white transition" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 9l3 3-3 3m5 0h3M5 20h14a2 2 0 002-2V6a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 00-2 2z"></path></svg>
                            API Management
                        </a>
                        <a href="{{ route('admin.api-docs') }}" class="group flex items-center gap-3 px-3 py-2.5 rounded-xl text-sm font-medium transition {{ request()->routeIs('admin.api-docs') ? 'bg-slate-900 text-white font-semibold' : 'hover:bg-slate-900/50 hover:text-white' }}">
                            <svg class="w-5 h-5 text-slate-400 group-hover:text-white transition" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"></path></svg>
                            Dokumentasi API
                        </a>
                    </div>
                </div>

                <div>
                    <div class="px-3 text-[10px] font-bold text-slate-500 uppercase tracking-wider mb-2">Sistem</div>
                    <div class="space-y-1">
                        <a href="{{ route('admin.periods.index') }}" class="group flex items-center gap-3 px-3 py-2.5 rounded-xl text-sm font-medium transition {{ request()->routeIs('admin.periods.*') ? 'bg-slate-900 text-white font-semibold' : 'hover:bg-slate-900/50 hover:text-white' }}">
                            <svg class="w-5 h-5 text-slate-400 group-hover:text-white transition" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg>
                            Periode Akademik
                        </a>
                        <a href="{{ route('admin.audit-logs') }}" class="group flex items-center gap-3 px-3 py-2.5 rounded-xl text-sm font-medium transition {{ request()->routeIs('admin.audit-logs') ? 'bg-slate-900 text-white font-semibold' : 'hover:bg-slate-900/50 hover:text-white' }}">
                            <svg class="w-5 h-5 text-slate-400 group-hover:text-white transition" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01"></path></svg>
                            Audit Log
                        </a>
                    </div>
                </div>
            @elseif(auth()->user()->isSiswa())
                <!-- Student Navigation -->
                <div>
                    <div class="px-3 text-[10px] font-bold text-slate-500 uppercase tracking-wider mb-2">Siswa</div>
                    <div class="space-y-1">
                        <a href="{{ route('student.dashboard') }}" class="group flex items-center gap-3 px-3 py-2.5 rounded-xl text-sm font-medium transition {{ request()->routeIs('student.dashboard') ? 'bg-slate-900 text-white font-semibold' : 'hover:bg-slate-900/50 hover:text-white' }}">
                            <svg class="w-5 h-5 text-slate-400 group-hover:text-white transition" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6a2 2 0 012-2h2a2 2 0 012 2v4a2 2 0 01-2 2H6a2 2 0 01-2-2V6zM14 6a2 2 0 012-2h2a2 2 0 012 2v4a2 2 0 01-2 2h-2a2 2 0 01-2-2V6zM4 16a2 2 0 012-2h2a2 2 0 012 2v4a2 2 0 01-2 2H6a2 2 0 01-2-2v-4zM14 16a2 2 0 012-2h2a2 2 0 012 2v4a2 2 0 01-2 2h-2a2 2 0 01-2-2v-4z"></path></svg>
                            Dashboard & Profil
                        </a>
                        <a href="{{ route('student.edit') }}" class="group flex items-center gap-3 px-3 py-2.5 rounded-xl text-sm font-medium transition {{ request()->routeIs('student.edit') ? 'bg-slate-900 text-white font-semibold' : 'hover:bg-slate-900/50 hover:text-white' }}">
                            <svg class="w-5 h-5 text-slate-400 group-hover:text-white transition" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path></svg>
                            Perbaikan Profil
                        </a>
                    </div>
                </div>
            @elseif(auth()->user()->isEmployee())
                <!-- Employee Navigation -->
                <div>
                    <div class="px-3 text-[10px] font-bold text-slate-500 uppercase tracking-wider mb-2">Pegawai / PTK</div>
                    <div class="space-y-1">
                        <a href="{{ route('employee.dashboard') }}" class="group flex items-center gap-3 px-3 py-2.5 rounded-xl text-sm font-medium transition {{ request()->routeIs('employee.dashboard') ? 'bg-slate-900 text-white font-semibold' : 'hover:bg-slate-900/50 hover:text-white' }}">
                            <svg class="w-5 h-5 text-slate-400 group-hover:text-white transition" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6a2 2 0 012-2h2a2 2 0 012 2v4a2 2 0 01-2 2H6a2 2 0 01-2-2V6zM14 6a2 2 0 012-2h2a2 2 0 012 2v4a2 2 0 01-2 2h-2a2 2 0 01-2-2V6zM4 16a2 2 0 012-2h2a2 2 0 012 2v4a2 2 0 01-2 2H6a2 2 0 01-2-2v-4zM14 16a2 2 0 012-2h2a2 2 0 012 2v4a2 2 0 01-2 2h-2a2 2 0 01-2-2v-4z"></path></svg>
                            Dashboard & Profil
                        </a>
                    </div>
                </div>
            @endif
        </nav>
    </aside>

    <!-- Main Content Area -->
    <div class="flex-1 flex flex-col min-w-0 min-h-screen">
        <!-- Top Bar -->
        <header class="h-16 bg-white border-b border-slate-200 px-6 flex items-center justify-between shadow-sm z-30">
            <!-- Mobile Toggle -->
            <div class="flex items-center gap-4">
                <button @click="mobileMenuOpen = true" class="text-slate-600 hover:text-slate-900 lg:hidden p-1.5 rounded-xl border border-slate-200 transition active:scale-[0.98]">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"></path></svg>
                </button>
                
                <!-- School Meta info -->
                @php
                    $navActiveYear = \App\Models\SchoolYear::where('is_active', true)->first();
                    $navActiveSemester = $navActiveYear ? \App\Models\Semester::where('school_year_id', $navActiveYear->id)->where('is_active', true)->first() : null;
                @endphp
                <div class="hidden md:flex items-center gap-2">
                    <span class="text-xs font-semibold text-slate-700 bg-slate-100 px-3 py-1 rounded-full flex items-center gap-1.5 border border-slate-200">
                        <span class="w-1.5 h-1.5 rounded-full bg-green-500"></span>
                        @if($navActiveYear && $navActiveSemester)
                            Semester {{ $navActiveSemester->semester }} {{ $navActiveYear->name }}
                        @else
                            Periode Tidak Aktif
                        @endif
                    </span>
                    <span class="text-xs text-slate-400 font-medium">{{ Carbon\Carbon::now()->translatedFormat('l, d F Y') }}</span>
                </div>
            </div>

            <!-- Profile Info & Logout Header -->
            <div class="flex items-center gap-3">
                <div class="flex flex-col text-right hidden sm:flex">
                    <span class="text-xs font-bold text-slate-900">{{ auth()->user()->name }}</span>
                    <span class="text-[9px] font-bold text-slate-400 uppercase tracking-wide self-end bg-slate-50 border border-slate-200 px-2 py-0.5 rounded-full mt-0.5">{{ str_replace('_', ' ', auth()->user()->role) }}</span>
                </div>
                
                <div class="w-9 h-9 rounded-full bg-slate-900 border border-slate-800 text-white flex items-center justify-center font-bold text-xs select-none">
                    {{ substr(auth()->user()->name, 0, 2) }}
                </div>
                
                <form method="POST" action="{{ route('logout') }}" class="inline-block border-l border-slate-200 pl-3">
                    @csrf
                    <button type="submit" title="Logout dari Aplikasi" class="text-slate-400 hover:text-red-600 p-1.5 rounded-xl hover:bg-red-50 transition active:scale-[0.98]">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"></path></svg>
                    </button>
                </form>
            </div>
        </header>

        <!-- Main Inner Content -->
        <main class="flex-1 p-6 lg:p-8 overflow-y-auto max-w-7xl w-full mx-auto">
            @if(session('success'))
                <div class="mb-6 p-4 rounded-2xl bg-green-50 border border-green-200 text-xs font-bold text-green-700 flex items-center gap-2 shadow-sm">
                    <svg class="w-5 h-5 text-green-600 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                    {{ session('success') }}
                </div>
            @endif

            @if(session('error'))
                <div class="mb-6 p-4 rounded-2xl bg-red-50 border border-red-200 text-xs font-bold text-red-700 flex items-center gap-2 shadow-sm">
                    <svg class="w-5 h-5 text-red-600 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                    {{ session('error') }}
                </div>
            @endif

            @yield('content')
        </main>
    </div>

    <!-- Custom Confirm Modal -->
    <div id="custom-confirm-modal" class="fixed inset-0 z-[100] flex items-center justify-center bg-slate-900/60 backdrop-blur-sm opacity-0 pointer-events-none transition-all duration-200" style="display: none;">
        <div class="bg-white rounded-2xl border border-slate-200 shadow-xl max-w-md w-full mx-4 overflow-hidden transform scale-95 transition-all duration-200" id="custom-confirm-content">
            <!-- Header -->
            <div class="p-6 border-b border-slate-100 flex items-start gap-4">
                <div id="custom-confirm-icon-danger" class="w-10 h-10 rounded-full bg-red-50 text-red-600 flex items-center justify-center flex-shrink-0">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path></svg>
                </div>
                <div id="custom-confirm-icon-info" class="w-10 h-10 rounded-full bg-blue-50 text-blue-600 flex items-center justify-center flex-shrink-0 hidden">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                </div>
                <div class="flex-1">
                    <h3 class="text-sm font-bold text-slate-900" id="custom-confirm-title">Peringatan Konfirmasi</h3>
                    <p class="text-xs text-slate-500 font-medium mt-1.5 leading-relaxed" id="custom-confirm-message">Apakah Anda yakin ingin melanjutkan tindakan ini?</p>
                </div>
            </div>
            <!-- Actions -->
            <div class="px-6 py-4 bg-slate-50 flex justify-end gap-2">
                <button id="custom-confirm-cancel-btn" type="button" class="px-4 py-2.5 bg-white border border-slate-200 hover:bg-slate-50 text-slate-700 rounded-xl text-xs font-semibold transition active:scale-[0.98]">
                    Batal
                </button>
                <button id="custom-confirm-confirm-btn" type="button" class="px-5 py-2.5 text-white rounded-xl text-xs font-bold transition shadow-sm active:scale-[0.98]">
                    Konfirmasi
                </button>
            </div>
        </div>
    </div>

    <script>
        window.confirmAction = function(options, onConfirm) {
            const modal = document.getElementById('custom-confirm-modal');
            const content = document.getElementById('custom-confirm-content');
            const titleEl = document.getElementById('custom-confirm-title');
            const msgEl = document.getElementById('custom-confirm-message');
            const confirmBtn = document.getElementById('custom-confirm-confirm-btn');
            const cancelBtn = document.getElementById('custom-confirm-cancel-btn');
            
            const iconDanger = document.getElementById('custom-confirm-icon-danger');
            const iconInfo = document.getElementById('custom-confirm-icon-info');

            titleEl.textContent = options.title || 'Konfirmasi';
            msgEl.textContent = options.message || 'Apakah Anda yakin ingin melanjutkan?';
            
            // Adjust icons & themes
            if (options.type === 'danger') {
                iconDanger.classList.remove('hidden');
                iconInfo.classList.add('hidden');
                confirmBtn.className = 'px-5 py-2.5 bg-red-600 hover:bg-red-700 text-white rounded-xl text-xs font-bold transition shadow-sm active:scale-[0.98]';
            } else {
                iconDanger.classList.add('hidden');
                iconInfo.classList.remove('hidden');
                confirmBtn.className = 'px-5 py-2.5 bg-blue-600 hover:bg-blue-700 text-white rounded-xl text-xs font-bold transition shadow-sm active:scale-[0.98]';
            }
            confirmBtn.textContent = options.confirmText || 'Konfirmasi';

            // Show modal
            modal.style.display = 'flex';
            // Trigger transition on next animation frame
            requestAnimationFrame(() => {
                modal.classList.remove('opacity-0', 'pointer-events-none');
                content.classList.remove('scale-95');
                content.classList.add('scale-100');
            });

            const closeConfirm = () => {
                modal.classList.add('opacity-0', 'pointer-events-none');
                content.classList.remove('scale-100');
                content.classList.add('scale-95');
                setTimeout(() => {
                    modal.style.display = 'none';
                }, 200);
            };

            confirmBtn.onclick = () => {
                closeConfirm();
                if (typeof onConfirm === 'function') {
                    onConfirm();
                }
            };

            cancelBtn.onclick = () => {
                closeConfirm();
            };

            modal.onclick = (e) => {
                if (e.target === modal) {
                    closeConfirm();
                }
            };
        };
    </script>

    @yield('scripts')
</body>
</html>
