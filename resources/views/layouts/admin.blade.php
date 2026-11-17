<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Admin Panel') - Grand Karaoke POS & Management</title>
    <!-- Tailwind CSS -->
    <script src="https://cdn.tailwindcss.com"></script>
    <!-- Google Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700;800&family=JetBrains+Mono:wght@400;600&display=swap" rel="stylesheet">
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <!-- Chart.js -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <!-- SweetAlert2 -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <script>
        tailwind.config = {
            theme: {
                extend: {
                    fontFamily: {
                        sans: ['"Plus Jakarta Sans"', 'sans-serif'],
                        mono: ['"JetBrains Mono"', 'monospace'],
                    },
                    colors: {
                        dark: {
                            800: '#1e293b',
                            900: '#0f172a',
                            950: '#090d16',
                        }
                    }
                }
            }
        }
    </script>
    <style>
        body {
            background-color: #090d16;
            color: #e2e8f0;
        }
        /* Custom scrollbar */
        ::-webkit-scrollbar {
            width: 6px;
            height: 6px;
        }
        ::-webkit-scrollbar-track {
            background: #0f172a;
        }
        ::-webkit-scrollbar-thumb {
            background: #334155;
            border-radius: 3px;
        }
        ::-webkit-scrollbar-thumb:hover {
            background: #475569;
        }
    </style>
    @stack('styles')
</head>
<body class="bg-[#090d16] text-slate-100 flex h-screen overflow-hidden antialiased font-sans">

    <!-- Sidebar Navigation -->
    <aside class="w-64 bg-[#0c1222] border-r border-slate-800/80 flex flex-col justify-between shrink-0 z-30">
        <div>
            <!-- Sidebar Header / Logo -->
            <div class="h-20 flex items-center px-6 border-b border-slate-800/80 bg-[#090d16]/50">
                <a href="{{ route('home') }}" class="flex items-center space-x-3">
                    <div class="w-10 h-10 rounded-xl bg-gradient-to-tr from-purple-600 to-amber-400 p-[2px]">
                        <div class="w-full h-full bg-[#0c1222] rounded-[10px] flex items-center justify-center">
                            <i class="fa-solid fa-microphone-lines text-amber-400 text-base"></i>
                        </div>
                    </div>
                    <div>
                        <h1 class="text-sm font-black tracking-wide bg-gradient-to-r from-white via-slate-100 to-amber-300 bg-clip-text text-transparent">
                            GRAND KARAOKE
                        </h1>
                        <span class="text-[10px] uppercase font-bold tracking-widest text-purple-400">
                            {{ auth()->user()->isOwner() ? 'Executive Suite' : 'Cashier Station' }}
                        </span>
                    </div>
                </a>
            </div>

            <!-- Navigation Links -->
            <div class="p-4 space-y-6 overflow-y-auto max-h-[calc(100vh-170px)]">
                <!-- Kasir & Operasional Section -->
                <div>
                    <span class="text-[10px] font-bold uppercase tracking-wider text-slate-500 px-3">Operasional Kasir</span>
                    <nav class="mt-2 space-y-1">
                        <a href="{{ route('kasir.dashboard') }}" class="flex items-center px-3 py-2.5 text-xs font-semibold rounded-xl transition-all {{ request()->routeIs('kasir.dashboard') ? 'bg-purple-600 text-white shadow-lg shadow-purple-600/30' : 'text-slate-300 hover:bg-slate-800/70 hover:text-white' }}">
                            <i class="fa-solid fa-gauge-high w-6 text-sm {{ request()->routeIs('kasir.dashboard') ? 'text-white' : 'text-purple-400' }}"></i>
                            <span>Dashboard Kasir</span>
                        </a>
                        <a href="{{ route('kasir.rooms') }}" class="flex items-center px-3 py-2.5 text-xs font-semibold rounded-xl transition-all {{ request()->routeIs('kasir.rooms*') ? 'bg-purple-600 text-white shadow-lg shadow-purple-600/30' : 'text-slate-300 hover:bg-slate-800/70 hover:text-white' }}">
                            <i class="fa-solid fa-table-cells-large w-6 text-sm {{ request()->routeIs('kasir.rooms*') ? 'text-white' : 'text-emerald-400' }}"></i>
                            <span class="flex-grow">Monitoring Kamar</span>
                            <span class="bg-emerald-500/20 text-emerald-300 text-[10px] px-1.5 py-0.5 rounded font-bold border border-emerald-500/30">GRID</span>
                        </a>
                        <a href="{{ route('kasir.companions') }}" class="flex items-center px-3 py-2.5 text-xs font-semibold rounded-xl transition-all {{ request()->routeIs('kasir.companions*') ? 'bg-purple-600 text-white shadow-lg shadow-purple-600/30' : 'text-slate-300 hover:bg-slate-800/70 hover:text-white' }}">
                            <i class="fa-solid fa-person-dress w-6 text-sm {{ request()->routeIs('kasir.companions*') ? 'text-white' : 'text-pink-400' }}"></i>
                            <span>Live LC Tracker</span>
                        </a>
                        <a href="{{ route('kasir.reservations') }}" class="flex items-center px-3 py-2.5 text-xs font-semibold rounded-xl transition-all {{ request()->routeIs('kasir.reservations*') ? 'bg-purple-600 text-white shadow-lg shadow-purple-600/30' : 'text-slate-300 hover:bg-slate-800/70 hover:text-white' }}">
                            <i class="fa-solid fa-calendar-days w-6 text-sm {{ request()->routeIs('kasir.reservations*') ? 'text-white' : 'text-cyan-400' }}"></i>
                            <span class="flex-grow">Kelola Reservasi</span>
                            @php
                                $pendingCount = \App\Models\Booking::where('status', 'pending')->count();
                            @endphp
                            @if($pendingCount > 0)
                                <span class="bg-rose-500 text-white text-[10px] px-1.5 py-0.5 rounded-full font-bold animate-pulse">{{ $pendingCount }}</span>
                            @endif
                        </a>
                        <a href="{{ route('kasir.pos') }}" class="flex items-center px-3 py-2.5 text-xs font-semibold rounded-xl transition-all {{ request()->routeIs('kasir.pos*') ? 'bg-purple-600 text-white shadow-lg shadow-purple-600/30' : 'text-slate-300 hover:bg-slate-800/70 hover:text-white' }}">
                            <i class="fa-solid fa-cash-register w-6 text-sm {{ request()->routeIs('kasir.pos*') ? 'text-white' : 'text-amber-400' }}"></i>
                            <span class="flex-grow font-bold text-amber-300">POS & Kasir Transaksi</span>
                            <span class="bg-amber-500/20 text-amber-300 text-[9px] px-1 py-0.5 rounded font-black border border-amber-500/30">F2</span>
                        </a>
                        <a href="{{ route('kasir.shifts') }}" class="flex items-center px-3 py-2.5 text-xs font-semibold rounded-xl transition-all {{ request()->routeIs('kasir.shifts*') ? 'bg-purple-600 text-white shadow-lg shadow-purple-600/30' : 'text-slate-300 hover:bg-slate-800/70 hover:text-white' }}">
                            <i class="fa-solid fa-clock-rotate-left w-6 text-sm {{ request()->routeIs('kasir.shifts*') ? 'text-white' : 'text-blue-400' }}"></i>
                            <span>Manajemen Shift Kasir</span>
                        </a>
                    </nav>
                </div>

                <!-- Owner / Manager Section -->
                @if(auth()->user()->isOwner())
                    <div class="pt-2 border-t border-slate-800/60">
                        <span class="text-[10px] font-bold uppercase tracking-wider text-amber-400 px-3 flex items-center">
                            <i class="fa-solid fa-crown text-[10px] mr-1.5"></i> Owner & Executive
                        </span>
                        <nav class="mt-2 space-y-1">
                            <a href="{{ route('owner.dashboard') }}" class="flex items-center px-3 py-2.5 text-xs font-semibold rounded-xl transition-all {{ request()->routeIs('owner.dashboard') ? 'bg-amber-500 text-slate-950 font-bold shadow-lg shadow-amber-500/25' : 'text-slate-300 hover:bg-slate-800/70 hover:text-white' }}">
                                <i class="fa-solid fa-chart-line w-6 text-sm {{ request()->routeIs('owner.dashboard') ? 'text-slate-950' : 'text-amber-400' }}"></i>
                                <span>Dashboard Eksekutif</span>
                            </a>
                            <a href="{{ route('owner.products') }}" class="flex items-center px-3 py-2.5 text-xs font-semibold rounded-xl transition-all {{ request()->routeIs('owner.products*') ? 'bg-amber-500 text-slate-950 font-bold shadow-lg shadow-amber-500/25' : 'text-slate-300 hover:bg-slate-800/70 hover:text-white' }}">
                                <i class="fa-solid fa-wine-bottle w-6 text-sm {{ request()->routeIs('owner.products*') ? 'text-slate-950' : 'text-amber-400' }}"></i>
                                <span class="flex-grow">Master F&B & Miras</span>
                                @php
                                    $lowCount = \App\Models\Product::whereColumn('stock', '<=', 'min_stock')->count();
                                @endphp
                                @if($lowCount > 0)
                                    <span class="bg-rose-500/30 text-rose-300 text-[10px] px-1.5 py-0.2 rounded font-bold border border-rose-500/40">! {{ $lowCount }}</span>
                                @endif
                            </a>
                            <a href="{{ route('owner.rooms') }}" class="flex items-center px-3 py-2.5 text-xs font-semibold rounded-xl transition-all {{ request()->routeIs('owner.rooms*') ? 'bg-amber-500 text-slate-950 font-bold shadow-lg shadow-amber-500/25' : 'text-slate-300 hover:bg-slate-800/70 hover:text-white' }}">
                                <i class="fa-solid fa-door-closed w-6 text-sm {{ request()->routeIs('owner.rooms*') ? 'text-slate-950' : 'text-amber-400' }}"></i>
                                <span>Master Kamar & Hold</span>
                            </a>
                            <a href="{{ route('owner.packages') }}" class="flex items-center px-3 py-2.5 text-xs font-semibold rounded-xl transition-all {{ request()->routeIs('owner.packages*') ? 'bg-amber-500 text-slate-950 font-bold shadow-lg shadow-amber-500/25' : 'text-slate-300 hover:bg-slate-800/70 hover:text-white' }}">
                                <i class="fa-solid fa-box-open w-6 text-sm {{ request()->routeIs('owner.packages*') ? 'text-slate-950' : 'text-amber-400' }}"></i>
                                <span>Paket Bundling VIP</span>
                            </a>
                            <a href="{{ route('owner.companions') }}" class="flex items-center px-3 py-2.5 text-xs font-semibold rounded-xl transition-all {{ request()->routeIs('owner.companions*') ? 'bg-amber-500 text-slate-950 font-bold shadow-lg shadow-amber-500/25' : 'text-slate-300 hover:bg-slate-800/70 hover:text-white' }}">
                                <i class="fa-solid fa-clipboard-user w-6 text-sm {{ request()->routeIs('owner.companions*') ? 'text-slate-950' : 'text-amber-400' }}"></i>
                                <span>Kinerja & Komisi LC</span>
                            </a>
                            <a href="{{ route('owner.reports') }}" class="flex items-center px-3 py-2.5 text-xs font-semibold rounded-xl transition-all {{ request()->routeIs('owner.reports*') ? 'bg-amber-500 text-slate-950 font-bold shadow-lg shadow-amber-500/25' : 'text-slate-300 hover:bg-slate-800/70 hover:text-white' }}">
                                <i class="fa-solid fa-file-invoice-dollar w-6 text-sm {{ request()->routeIs('owner.reports*') ? 'text-slate-950' : 'text-amber-400' }}"></i>
                                <span>Laporan Omset & Margin</span>
                            </a>
                            <a href="{{ route('owner.users') }}" class="flex items-center px-3 py-2.5 text-xs font-semibold rounded-xl transition-all {{ request()->routeIs('owner.users*') ? 'bg-amber-500 text-slate-950 font-bold shadow-lg shadow-amber-500/25' : 'text-slate-300 hover:bg-slate-800/70 hover:text-white' }}">
                                <i class="fa-solid fa-users-gear w-6 text-sm {{ request()->routeIs('owner.users*') ? 'text-slate-950' : 'text-amber-400' }}"></i>
                                <span>Akun Login Pegawai</span>
                            </a>
                        </nav>
                    </div>
                @endif
            </div>
        </div>

        <!-- Sidebar Footer / User Profile -->
        <div class="p-4 border-t border-slate-800/80 bg-[#090d16]/80 flex items-center justify-between">
            <div class="flex items-center space-x-3 overflow-hidden">
                <div class="w-9 h-9 rounded-xl bg-purple-600 flex items-center justify-center font-bold text-white uppercase shrink-0">
                    {{ substr(auth()->user()->name, 0, 1) }}
                </div>
                <div class="overflow-hidden">
                    <p class="text-xs font-bold text-slate-200 truncate">{{ auth()->user()->name }}</p>
                    <span class="inline-block text-[10px] font-semibold text-purple-400 uppercase tracking-wider">
                        {{ auth()->user()->role }}
                    </span>
                </div>
            </div>
            <form method="POST" action="{{ route('logout') }}">
                @csrf
                <button type="submit" title="Logout" class="w-8 h-8 rounded-lg bg-slate-800 hover:bg-rose-900/60 text-slate-400 hover:text-rose-300 flex items-center justify-center transition-all">
                    <i class="fa-solid fa-power-off text-xs"></i>
                </button>
            </form>
        </div>
    </aside>

    <!-- Main Content Wrapper -->
    <div class="flex-grow flex flex-col h-screen overflow-hidden">
        <!-- Top App Bar -->
        <header class="h-20 bg-[#0c1222] border-b border-slate-800/80 px-8 flex items-center justify-between shrink-0">
            <!-- Left Info & Status -->
            <div class="flex items-center space-x-6">
                <div>
                    <h2 class="text-lg font-bold text-white flex items-center">
                        @yield('header_title', 'Operational System')
                    </h2>
                    <p class="text-xs text-slate-400">@yield('header_subtitle', 'Sistem Manajemen Karaoke & Kasir Real-Time')</p>
                </div>

                <!-- Shift Status Pill -->
                @php
                    $userShift = \App\Models\CashierShift::where('status', 'open')->latest()->first();
                @endphp
                @if($userShift)
                    <div class="hidden lg:flex items-center space-x-2 bg-emerald-950/60 border border-emerald-500/30 px-3 py-1.5 rounded-xl text-xs text-emerald-300">
                        <span class="w-2 h-2 rounded-full bg-emerald-400 animate-pulse"></span>
                        <span class="font-bold">Shift Open:</span>
                        <span>{{ $userShift->shift_name }} ({{ $userShift->user->name }})</span>
                        <a href="{{ route('kasir.shifts') }}" class="underline text-emerald-400 text-[10px] ml-1">Detail</a>
                    </div>
                @else
                    <div class="hidden lg:flex items-center space-x-2 bg-rose-950/60 border border-rose-500/30 px-3 py-1.5 rounded-xl text-xs text-rose-300">
                        <span class="w-2 h-2 rounded-full bg-rose-400"></span>
                        <span class="font-bold">Shift Tutup / Belum Buka</span>
                        <a href="{{ route('kasir.shifts') }}" class="underline text-rose-400 text-[10px] ml-1">Buka Shift</a>
                    </div>
                @endif
            </div>

            <!-- Right Fast Controls -->
            <div class="flex items-center space-x-4">
                <!-- Live Digital Clock -->
                <div class="hidden sm:flex flex-col items-end">
                    <span id="live-clock" class="text-sm font-mono font-bold text-amber-400 tracking-wider">00:00:00 WIB</span>
                    <span class="text-[10px] text-slate-400">{{ date('l, d F Y') }}</span>
                </div>

                <!-- Fast POS Shortcut Button -->
                <a href="{{ route('kasir.pos') }}" class="inline-flex items-center px-4 py-2 bg-gradient-to-r from-amber-500 to-amber-600 hover:from-amber-400 hover:to-amber-500 text-slate-950 font-extrabold text-xs rounded-xl shadow-lg shadow-amber-500/20 transition-all hover:scale-105">
                    <i class="fa-solid fa-cash-register mr-2"></i> Buka POS Kasir
                </a>

                <!-- Back to Public Web Link -->
                <a href="{{ route('home') }}" target="_blank" class="p-2 rounded-xl bg-slate-800/80 hover:bg-slate-700 text-slate-300 hover:text-white text-xs flex items-center justify-center transition-all" title="Buka Halaman Pelanggan">
                    <i class="fa-solid fa-arrow-up-right-from-square"></i>
                </a>
            </div>
        </header>

        <!-- Scrollable Main Container -->
        <main class="flex-grow overflow-y-auto p-6 lg:p-8 bg-[#090d16]">
            @if(session('success'))
                <script>
                    document.addEventListener('DOMContentLoaded', function() {
                        Swal.fire({
                            icon: 'success',
                            title: 'Berhasil!',
                            text: "{{ session('success') }}",
                            background: '#0f172a',
                            color: '#f8fafc',
                            confirmButtonColor: '#7c3aed'
                        });
                    });
                </script>
            @endif

            @if(session('error'))
                <script>
                    document.addEventListener('DOMContentLoaded', function() {
                        Swal.fire({
                            icon: 'error',
                            title: 'Terjadi Kesalahan!',
                            text: "{{ session('error') }}",
                            background: '#0f172a',
                            color: '#f8fafc',
                            confirmButtonColor: '#e11d48'
                        });
                    });
                </script>
            @endif

            @yield('content')
        </main>
    </div>

    <!-- Live Clock Script -->
    <script>
        function updateClock() {
            const now = new Date();
            const hours = String(now.getHours()).padStart(2, '0');
            const minutes = String(now.getMinutes()).padStart(2, '0');
            const seconds = String(now.getSeconds()).padStart(2, '0');
            const clockEl = document.getElementById('live-clock');
            if (clockEl) {
                clockEl.textContent = `${hours}:${minutes}:${seconds} WIB`;
            }
        }
        setInterval(updateClock, 1000);
        updateClock();

        // Keyboard Shortcut: Press F2 to jump to POS
        window.addEventListener('keydown', function(e) {
            if (e.key === 'F2') {
                e.preventDefault();
                window.location.href = "{{ route('kasir.pos') }}";
            }
        });
    </script>

    @stack('scripts')
</body>
</html>
