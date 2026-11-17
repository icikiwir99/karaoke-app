<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'HY KAROKE AND LOUNGE & Executive Lounge')</title>
    <!-- Tailwind CSS -->
    <script src="https://cdn.tailwindcss.com"></script>
    <!-- Google Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <!-- SweetAlert2 -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <script>
        tailwind.config = {
            theme: {
                extend: {
                    fontFamily: {
                        sans: ['"Plus Jakarta Sans"', 'sans-serif'],
                    },
                    colors: {
                        brand: {
                            50: '#f5f3ff',
                            100: '#ede9fe',
                            500: '#8b5cf6',
                            600: '#7c3aed',
                            700: '#6d28d9',
                            800: '#5b21b6',
                            900: '#4c1d95',
                        },
                        gold: {
                            400: '#fbbf24',
                            500: '#f59e0b',
                            600: '#d97706',
                        }
                    }
                }
            }
        }
    </script>
    <style>
        body {
            background-color: #090d16;
            color: #f1f5f9;
        }
        .neon-glow {
            text-shadow: 0 0 10px rgba(139, 92, 246, 0.5), 0 0 20px rgba(139, 92, 246, 0.3);
        }
        .gold-glow {
            text-shadow: 0 0 10px rgba(245, 158, 11, 0.5);
        }
    </style>
    @stack('styles')
</head>
<body class="bg-[#090d16] text-slate-100 flex flex-col min-h-screen antialiased selection:bg-purple-600 selection:text-white">

    <!-- Top Notification Banner -->
    <div class="bg-gradient-to-r from-purple-900 via-indigo-900 to-slate-900 text-xs py-2 px-4 border-b border-purple-500/20 text-center flex items-center justify-center space-x-2">
        <span class="bg-amber-500/20 text-amber-300 px-2 py-0.5 rounded text-[10px] font-bold tracking-wide uppercase border border-amber-500/30">HAPPY HOUR</span>
        <span>Diskon 50% Room Setiap Senin - Kamis (13.00 - 18.00 WIB)</span>
        <a href="{{ route('customer.promos') }}" class="underline hover:text-white font-medium ml-1">Lihat Promo &rarr;</a>
    </div>

    <!-- Main Navigation Bar -->
    <nav class="sticky top-0 z-50 bg-[#0c1222]/90 backdrop-blur-md border-b border-slate-800/80">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex items-center justify-between h-20">
                <!-- Brand Logo -->
                <a href="{{ route('home') }}" class="flex items-center space-x-3 group">
                    <div class="w-11 h-11 rounded-xl bg-gradient-to-tr from-purple-600 via-violet-500 to-amber-400 p-[2px] shadow-lg shadow-purple-600/30 group-hover:scale-105 transition-all">
                        <div class="w-full h-full bg-[#0c1222] rounded-[10px] flex items-center justify-center">
                            <i class="fa-solid fa-microphone-lines text-amber-400 text-xl"></i>
                        </div>
                    </div>
                    <div>
                        <span class="text-xl font-extrabold tracking-tight bg-gradient-to-r from-white via-slate-100 to-amber-300 bg-clip-text text-transparent">
                            HY KAROKE AND LOUNGE
                        </span>
                        <span class="block text-[10px] tracking-widest uppercase font-semibold text-purple-400">
                            & Executive Lounge
                        </span>
                    </div>
                </a>

                <!-- Desktop Navigation Links -->
                <div class="hidden md:flex items-center space-x-1 lg:space-x-2">
                    <a href="{{ route('home') }}" class="px-3.5 py-2 rounded-lg text-sm font-medium transition-all {{ request()->routeIs('home') ? 'bg-purple-600/20 text-purple-300 border border-purple-500/30' : 'text-slate-300 hover:text-white hover:bg-slate-800/60' }}">
                        <i class="fa-solid fa-house mr-1.5 text-xs opacity-70"></i> Beranda
                    </a>
                    <a href="{{ route('customer.rooms') }}" class="px-3.5 py-2 rounded-lg text-sm font-medium transition-all {{ request()->routeIs('customer.rooms') ? 'bg-purple-600/20 text-purple-300 border border-purple-500/30' : 'text-slate-300 hover:text-white hover:bg-slate-800/60' }}">
                        <i class="fa-solid fa-door-open mr-1.5 text-xs opacity-70"></i> Katalog Kamar
                    </a>
                    <a href="{{ route('customer.menu') }}" class="px-3.5 py-2 rounded-lg text-sm font-medium transition-all {{ request()->routeIs('customer.menu') ? 'bg-purple-600/20 text-purple-300 border border-purple-500/30' : 'text-slate-300 hover:text-white hover:bg-slate-800/60' }}">
                        <i class="fa-solid fa-wine-glass mr-1.5 text-xs opacity-70"></i> Menu & Miras
                    </a>
                    <a href="{{ route('customer.songs') }}" class="px-3.5 py-2 rounded-lg text-sm font-medium transition-all {{ request()->routeIs('customer.songs') ? 'bg-purple-600/20 text-purple-300 border border-purple-500/30' : 'text-slate-300 hover:text-white hover:bg-slate-800/60' }}">
                        <i class="fa-solid fa-music mr-1.5 text-xs opacity-70"></i> Song Finder
                    </a>
                    <a href="{{ route('customer.promos') }}" class="px-3.5 py-2 rounded-lg text-sm font-medium transition-all {{ request()->routeIs('customer.promos') ? 'bg-purple-600/20 text-purple-300 border border-purple-500/30' : 'text-slate-300 hover:text-white hover:bg-slate-800/60' }}">
                        <i class="fa-solid fa-fire text-amber-400 mr-1.5 text-xs"></i> Promo & FAQ
                    </a>
                </div>

                <!-- Action Button & Auth -->
                <div class="hidden md:flex items-center space-x-3">
                    <a href="{{ route('customer.reservation') }}" class="inline-flex items-center px-4 py-2.5 rounded-xl bg-gradient-to-r from-purple-600 to-indigo-600 hover:from-purple-500 hover:to-indigo-500 text-white text-sm font-semibold shadow-lg shadow-purple-600/25 transition-all hover:scale-[1.02] border border-purple-400/30">
                        <i class="fa-solid fa-calendar-check mr-2"></i> Reservasi Online
                    </a>

                    @auth
                        <div class="relative group">
                            <button class="flex items-center space-x-2 bg-slate-800/80 hover:bg-slate-700/80 border border-slate-700 px-3 py-2 rounded-xl text-sm transition-all">
                                <span class="w-7 h-7 rounded-lg bg-purple-600 flex items-center justify-center text-xs font-bold uppercase">
                                    {{ substr(auth()->user()->name, 0, 1) }}
                                </span>
                                <span class="max-w-[100px] truncate text-slate-200">{{ auth()->user()->name }}</span>
                                <i class="fa-solid fa-chevron-down text-xs text-slate-400"></i>
                            </button>
                            <div class="absolute right-0 mt-2 w-48 bg-[#0f172a] border border-slate-800 rounded-xl shadow-2xl py-1 opacity-0 invisible group-hover:opacity-100 group-hover:visible transition-all duration-150 z-50">
                                @if(auth()->user()->isOwner())
                                    <a href="{{ route('owner.dashboard') }}" class="block px-4 py-2 text-sm text-amber-400 hover:bg-slate-800 font-semibold">
                                        <i class="fa-solid fa-crown mr-2"></i> Area Owner
                                    </a>
                                @endif
                                @if(auth()->user()->isKasir() || auth()->user()->isOwner())
                                    <a href="{{ route('kasir.dashboard') }}" class="block px-4 py-2 text-sm text-purple-400 hover:bg-slate-800 font-semibold">
                                        <i class="fa-solid fa-cash-register mr-2"></i> Area Kasir
                                    </a>
                                @endif
                                <form method="POST" action="{{ route('logout') }}">
                                    @csrf
                                    <button type="submit" class="w-full text-left px-4 py-2 text-sm text-rose-400 hover:bg-slate-800">
                                        <i class="fa-solid fa-right-from-bracket mr-2"></i> Logout
                                    </button>
                                </form>
                            </div>
                        </div>
                    @else
                        <a href="{{ route('login') }}" class="inline-flex items-center px-3.5 py-2 rounded-xl bg-slate-800/80 hover:bg-slate-700/80 border border-slate-700 text-slate-200 text-sm font-medium transition-all">
                            <i class="fa-solid fa-user-lock mr-2 text-xs text-purple-400"></i> Login Pegawai
                        </a>
                    @endauth
                </div>

                <!-- Mobile Menu Button -->
                <div class="flex md:hidden items-center space-x-2">
                    <a href="{{ route('customer.reservation') }}" class="p-2.5 rounded-lg bg-purple-600 text-white text-xs font-semibold">
                        <i class="fa-solid fa-calendar-check"></i>
                    </a>
                    <button id="mobile-menu-btn" class="p-2 rounded-lg bg-slate-800 text-slate-300 hover:text-white">
                        <i class="fa-solid fa-bars text-lg"></i>
                    </button>
                </div>
            </div>
        </div>

        <!-- Mobile Drawer Menu -->
        <div id="mobile-menu" class="hidden md:hidden bg-[#0c1222] border-b border-slate-800 px-4 pt-2 pb-4 space-y-1">
            <a href="{{ route('home') }}" class="block px-3 py-2 rounded-md text-base font-medium text-slate-200 hover:bg-slate-800">Beranda</a>
            <a href="{{ route('customer.rooms') }}" class="block px-3 py-2 rounded-md text-base font-medium text-slate-200 hover:bg-slate-800">Katalog Kamar</a>
            <a href="{{ route('customer.menu') }}" class="block px-3 py-2 rounded-md text-base font-medium text-slate-200 hover:bg-slate-800">Menu & Miras</a>
            <a href="{{ route('customer.songs') }}" class="block px-3 py-2 rounded-md text-base font-medium text-slate-200 hover:bg-slate-800">Song Finder</a>
            <a href="{{ route('customer.promos') }}" class="block px-3 py-2 rounded-md text-base font-medium text-slate-200 hover:bg-slate-800">Promo & Aturan</a>
            <div class="pt-2 border-t border-slate-800">
                @auth
                    @if(auth()->user()->isOwner())
                        <a href="{{ route('owner.dashboard') }}" class="block px-3 py-2 rounded-md text-base font-semibold text-amber-400">Area Owner</a>
                    @endif
                    @if(auth()->user()->isKasir() || auth()->user()->isOwner())
                        <a href="{{ route('kasir.dashboard') }}" class="block px-3 py-2 rounded-md text-base font-semibold text-purple-400">Area Kasir</a>
                    @endif
                    <form method="POST" action="{{ route('logout') }}">
                        @csrf
                        <button type="submit" class="w-full text-left px-3 py-2 text-rose-400">Logout</button>
                    </form>
                @else
                    <a href="{{ route('login') }}" class="block px-3 py-2 text-slate-300">Login Pegawai / Member</a>
                @endauth
            </div>
        </div>
    </nav>

    <!-- Main Content Body -->
    <main class="flex-grow">
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
                        title: 'Oops!',
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

    <!-- Footer -->
    <footer class="bg-[#070b14] border-t border-slate-800/80 mt-16 pt-12 pb-8">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="grid grid-cols-1 md:grid-cols-4 gap-8 mb-8">
                <div class="md:col-span-2">
                    <div class="flex items-center space-x-3 mb-4">
                        <div class="w-9 h-9 rounded-lg bg-gradient-to-tr from-purple-600 to-amber-400 p-[2px]">
                            <div class="w-full h-full bg-[#070b14] rounded-[6px] flex items-center justify-center">
                                <i class="fa-solid fa-microphone-lines text-amber-400"></i>
                            </div>
                        </div>
                        <span class="text-lg font-bold text-white tracking-wide">HY KAROKE AND LOUNGE & EXECUTIVE LOUNGE</span>
                    </div>
                    <p class="text-sm text-slate-400 leading-relaxed max-w-md mb-4">
                        Destinasi hiburan karaoke premium dengan sound system mutakhir, koleksi minuman & spirits terlengkap, pemandu lagu profesional, dan pelayanan bintang lima.
                    </p>
                    <div class="flex space-x-3 text-slate-400">
                        <a href="#" class="w-9 h-9 rounded-lg bg-slate-800/80 flex items-center justify-center hover:bg-purple-600 hover:text-white transition-all"><i class="fa-brands fa-instagram"></i></a>
                        <a href="#" class="w-9 h-9 rounded-lg bg-slate-800/80 flex items-center justify-center hover:bg-purple-600 hover:text-white transition-all"><i class="fa-brands fa-whatsapp"></i></a>
                        <a href="#" class="w-9 h-9 rounded-lg bg-slate-800/80 flex items-center justify-center hover:bg-purple-600 hover:text-white transition-all"><i class="fa-brands fa-tiktok"></i></a>
                    </div>
                </div>
                <div>
                    <h4 class="text-sm font-bold text-slate-200 uppercase tracking-wider mb-4">Menu Cepat</h4>
                    <ul class="space-y-2 text-sm text-slate-400">
                        <li><a href="{{ route('customer.rooms') }}" class="hover:text-purple-400 transition-colors">Katalog Tipe Kamar</a></li>
                        <li><a href="{{ route('customer.menu') }}" class="hover:text-purple-400 transition-colors">Daftar F&B & Minuman Miras</a></li>
                        <li><a href="{{ route('customer.songs') }}" class="hover:text-purple-400 transition-colors">Song Finder Online</a></li>
                        <li><a href="{{ route('customer.promos') }}" class="hover:text-purple-400 transition-colors">Promo & Happy Hour</a></li>
                        <li><a href="{{ route('customer.reservation') }}" class="hover:text-purple-400 transition-colors">Formulir Reservasi</a></li>
                    </ul>
                </div>
                <div>
                    <h4 class="text-sm font-bold text-slate-200 uppercase tracking-wider mb-4">Kontak & Lokasi</h4>
                    <p class="text-sm text-slate-400 mb-2"><i class="fa-solid fa-location-dot text-purple-400 mr-2"></i> Jl. Boulevard Utama No. 88, Golden City Area</p>
                    <p class="text-sm text-slate-400 mb-2"><i class="fa-solid fa-clock text-purple-400 mr-2"></i> Buka Setiap Hari: 13.00 - 03.00 WIB</p>
                    <p class="text-sm text-slate-400"><i class="fa-brands fa-whatsapp text-emerald-400 mr-2"></i> WhatsApp: +62 812-3456-7890</p>
                </div>
            </div>
            <div class="border-t border-slate-800/60 pt-6 text-center text-xs text-slate-500">
                &copy; {{ date('Y') }} HY KAROKE AND LOUNGE. Seluruh hak cipta dilindungi undang-undang.
            </div>
        </div>
    </footer>

    <script>
        document.getElementById('mobile-menu-btn')?.addEventListener('click', function() {
            const menu = document.getElementById('mobile-menu');
            menu.classList.toggle('hidden');
        });
    </script>
    @stack('scripts')
</body>
</html>

