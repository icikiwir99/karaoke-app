@extends('layouts.app')

@section('title', 'HY KAROKE AND LOUNGE & Executive Lounge - Premium Entertainment')

@section('content')
<!-- Hero Section -->
<div class="relative overflow-hidden pt-12 pb-24 lg:pt-20 lg:pb-32">
    <!-- Ambient Background Lighting -->
    <div class="absolute top-1/4 left-1/2 -translate-x-1/2 -translate-y-1/2 w-[600px] h-[400px] bg-gradient-to-tr from-purple-700/25 via-indigo-600/20 to-amber-500/15 rounded-full blur-[130px] pointer-events-none"></div>

    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 relative z-10 text-center">
        <!-- Top Badge -->
        <div class="inline-flex items-center space-x-2 px-3.5 py-1.5 rounded-full bg-purple-900/40 border border-purple-500/30 text-purple-300 text-xs font-semibold uppercase tracking-wider mb-6 backdrop-blur-sm">
            <span class="w-2 h-2 rounded-full bg-amber-400 animate-ping"></span>
            <span>The Ultimate Executive Karaoke Experience</span>
        </div>

        <h1 class="text-4xl sm:text-6xl lg:text-7xl font-black tracking-tight text-white leading-tight max-w-4xl mx-auto mb-6">
            Nyanyikan Lagu Favoritmu dengan <span class="bg-gradient-to-r from-purple-400 via-violet-300 to-amber-300 bg-clip-text text-transparent">Kualitas Studio</span> & Kemewahan Lounge.
        </h1>

        <p class="text-base sm:text-lg text-slate-300 max-w-2xl mx-auto mb-10 leading-relaxed">
            Dilengkapi sound system studio-grade, pencahayaan laser atmosferik, koleksi spirits & minuman premium terlengkap, serta pemandu lagu (LC) berkelas.
        </p>

        <!-- CTA Buttons -->
        <div class="flex flex-col sm:flex-row items-center justify-center gap-4 max-w-md mx-auto">
            <a href="{{ route('customer.reservation') }}" class="w-full sm:w-auto inline-flex items-center justify-center px-8 py-4 rounded-xl bg-gradient-to-r from-purple-600 to-indigo-600 hover:from-purple-500 hover:to-indigo-500 text-white font-bold text-base shadow-2xl shadow-purple-600/40 transition-all hover:scale-105 border border-purple-400/40">
                <i class="fa-solid fa-calendar-check mr-2.5"></i> Reservasi Kamar Online
            </a>
            <a href="{{ route('customer.menu') }}" class="w-full sm:w-auto inline-flex items-center justify-center px-6 py-4 rounded-xl bg-slate-900/80 hover:bg-slate-800 border border-slate-700 text-slate-200 font-semibold text-base transition-all hover:border-slate-600">
                <i class="fa-solid fa-wine-glass text-amber-400 mr-2.5"></i> Katalog Minuman & Spirits
            </a>
        </div>

        <!-- Quick Metrics -->
        <div class="grid grid-cols-2 md:grid-cols-4 gap-6 mt-16 max-w-4xl mx-auto pt-8 border-t border-slate-800/80">
            <div>
                <span class="block text-3xl font-extrabold text-white">10+</span>
                <span class="text-xs text-slate-400 uppercase tracking-wider font-medium">Kamar Eksekutif & VIP</span>
            </div>
            <div>
                <span class="block text-3xl font-extrabold text-amber-400">10.000+</span>
                <span class="text-xs text-slate-400 uppercase tracking-wider font-medium">Database Lagu Terupdate</span>
            </div>
            <div>
                <span class="block text-3xl font-extrabold text-purple-400">Meyer & JBL</span>
                <span class="text-xs text-slate-400 uppercase tracking-wider font-medium">Studio Audio Sound</span>
            </div>
            <div>
                <span class="block text-3xl font-extrabold text-emerald-400">100%</span>
                <span class="text-xs text-slate-400 uppercase tracking-wider font-medium">Authentic Spirits & Miras</span>
            </div>
        </div>
    </div>
</div>

<!-- Special Promos Banner Section -->
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 -mt-8 mb-20 relative z-20">
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
        @foreach($promos as $promo)
            <div class="bg-gradient-to-b from-[#121a2d] to-[#0c1222] border border-purple-500/20 hover:border-purple-500/50 rounded-2xl p-6 shadow-xl transition-all hover:-translate-y-1 group">
                <div class="flex items-center justify-between mb-4">
                    <span class="px-2.5 py-1 rounded-md text-[10px] font-black uppercase tracking-wider bg-amber-500/20 text-amber-300 border border-amber-500/30">
                        {{ $promo->badge ?? 'PROMO KHUSUS' }}
                    </span>
                    <span class="text-xs font-bold text-purple-400 font-mono">{{ $promo->discount_text }}</span>
                </div>
                <h3 class="text-lg font-bold text-white mb-2 group-hover:text-amber-300 transition-colors">{{ $promo->title }}</h3>
                <p class="text-xs text-slate-400 line-clamp-3 leading-relaxed mb-4">{{ $promo->description }}</p>
                <a href="{{ route('customer.reservation') }}" class="inline-flex items-center text-xs font-semibold text-purple-400 hover:text-white transition-colors">
                    Ambil Promo Ini &rarr;
                </a>
            </div>
        @endforeach
    </div>
</div>

<!-- Room Categories Showcase -->
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 mb-24">
    <div class="flex flex-col md:flex-row md:items-end justify-between mb-12">
        <div>
            <span class="text-xs font-bold uppercase tracking-wider text-purple-400">Pilihan Ruangan</span>
            <h2 class="text-3xl font-black text-white mt-1">Kategori Kamar Karaoke</h2>
            <p class="text-sm text-slate-400 mt-2">Pilih ukuran kamar yang tepat sesuai jumlah rombongan dan kebutuhan Anda.</p>
        </div>
        <a href="{{ route('customer.rooms') }}" class="mt-4 md:mt-0 text-sm font-semibold text-purple-400 hover:text-white flex items-center">
            Lihat Semua Kamar & Fasilitas <i class="fa-solid fa-arrow-right ml-2 text-xs"></i>
        </a>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
        @php
            $types = [
                'small' => ['name' => 'Small Room', 'cap' => '2-4 Orang', 'icon' => 'fa-users', 'color' => 'from-blue-600 to-indigo-600', 'price' => 'Rp 85.000 / Jam'],
                'medium' => ['name' => 'Medium Room', 'cap' => '6-8 Orang', 'icon' => 'fa-people-group', 'color' => 'from-purple-600 to-violet-600', 'price' => 'Rp 140.000 / Jam'],
                'large' => ['name' => 'Large Room', 'cap' => '12-16 Orang', 'icon' => 'fa-champagne-glasses', 'color' => 'from-pink-600 to-rose-600', 'price' => 'Rp 240.000 / Jam'],
                'vip' => ['name' => 'Imperial VIP Suite', 'cap' => '20-25 Orang', 'icon' => 'fa-crown', 'color' => 'from-amber-500 to-amber-700', 'price' => 'Rp 450.000 / Jam'],
            ];
        @endphp

        @foreach($types as $typeKey => $t)
            <div class="bg-[#0c1222] border border-slate-800 rounded-2xl overflow-hidden hover:border-purple-500/40 transition-all hover:shadow-2xl hover:shadow-purple-900/20 flex flex-col justify-between group">
                <div class="p-6">
                    <div class="w-12 h-12 rounded-xl bg-gradient-to-tr {{ $t['color'] }} flex items-center justify-center text-white mb-4 shadow-lg">
                        <i class="fa-solid {{ $t['icon'] }} text-lg"></i>
                    </div>
                    <span class="text-[10px] font-bold uppercase tracking-widest text-slate-400">Kapasitas {{ $t['cap'] }}</span>
                    <h3 class="text-xl font-bold text-white mt-1 group-hover:text-amber-300 transition-colors">{{ $t['name'] }}</h3>
                    <p class="text-xs text-slate-400 mt-2 leading-relaxed">
                        @if($typeKey === 'vip')
                            Dilengkapi Private Bar Counter, Wine Chiller, 6 Gold Mics, dan Dedicated Waiter.
                        @elseif($typeKey === 'large')
                            Dilengkapi Mini Stage Podium, Dual 75" Screen, dan Smoke Machine Konser.
                        @elseif($typeKey === 'medium')
                            Sound system JBL Karaoke, 4 Mics, dan Laser Dynamic Lighting.
                        @else
                            Akustik ruang intim, sofa kulit empuk, dan 2 mic nirkabel profesional.
                        @endif
                    </p>
                </div>
                <div class="p-6 pt-0 border-t border-slate-800/80 mt-4 bg-slate-900/30">
                    <div class="flex items-center justify-between py-4">
                        <span class="text-xs text-slate-400">Mulai dari</span>
                        <span class="text-sm font-extrabold text-amber-400 font-mono">{{ $t['price'] }}</span>
                    </div>
                    <a href="{{ route('customer.reservation') }}?room_type={{ $typeKey }}" class="w-full py-2.5 px-4 rounded-xl bg-slate-800 hover:bg-purple-600 text-slate-200 hover:text-white text-xs font-bold text-center block transition-all">
                        Pesan Kamar Ini
                    </a>
                </div>
            </div>
        @endforeach
    </div>
</div>

<!-- Bundling Packages Section -->
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 mb-24">
    <div class="bg-gradient-to-r from-purple-950/70 via-slate-900 to-indigo-950/70 border border-purple-500/30 rounded-3xl p-8 lg:p-12 relative overflow-hidden">
        <div class="max-w-2xl">
            <span class="px-3 py-1 rounded-full bg-amber-500/20 text-amber-300 text-xs font-bold uppercase tracking-wide border border-amber-500/30">
                HEMAT HINGGA 30%
            </span>
            <h2 class="text-3xl sm:text-4xl font-black text-white mt-3">Paket Bundling Karaoke & Minuman</h2>
            <p class="text-sm text-slate-300 mt-2">
                Pilihan paket hemat all-in-one yang sudah menggabungkan sewa kamar, layanan pemandu lagu (LC), serta paket minuman khusus & snack.
            </p>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mt-8">
            @foreach($packages as $pkg)
                <div class="bg-[#0c1222]/90 border border-slate-700/80 rounded-2xl p-6 flex flex-col justify-between hover:border-amber-400/50 transition-all">
                    <div>
                        <span class="text-[10px] font-bold uppercase tracking-wider text-purple-400">Tipe {{ strtoupper($pkg->room_type) }} ROOM</span>
                        <h4 class="text-lg font-bold text-white mt-1">{{ $pkg->name }}</h4>
                        <div class="my-4 pb-4 border-b border-slate-800">
                            <span class="text-2xl font-black text-amber-400 font-mono">{{ $pkg->formatted_price }}</span>
                            <span class="text-xs text-slate-400 block mt-0.5">Sudah termasuk sewa {{ $pkg->room_hours }} jam</span>
                        </div>
                        <ul class="space-y-2 text-xs text-slate-300 mb-6">
                            <li class="flex items-center"><i class="fa-solid fa-check text-emerald-400 mr-2"></i> Sewa Kamar {{ strtoupper($pkg->room_type) }} x {{ $pkg->room_hours }} Jam</li>
                            @if($pkg->include_lc_qty > 0)
                                <li class="flex items-center"><i class="fa-solid fa-check text-emerald-400 mr-2"></i> {{ $pkg->include_lc_qty }}x Pemandu Lagu (LC) x {{ $pkg->include_lc_hours }} Jam</li>
                            @endif
                            @foreach($pkg->items as $it)
                                <li class="flex items-center"><i class="fa-solid fa-check text-emerald-400 mr-2"></i> {{ $it->quantity }}x {{ $it->product->name }}</li>
                            @endforeach
                        </ul>
                    </div>
                    <a href="{{ route('customer.reservation') }}?package_id={{ $pkg->id }}" class="w-full py-2.5 px-4 rounded-xl bg-gradient-to-r from-amber-500 to-amber-600 hover:from-amber-400 hover:to-amber-500 text-slate-950 font-extrabold text-xs text-center transition-all shadow-lg shadow-amber-500/20">
                        Pilih Paket Ini
                    </a>
                </div>
            @endforeach
        </div>
    </div>
</div>

<!-- Spirits & Alcohol Highlights -->
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 mb-24">
    <div class="flex flex-col md:flex-row md:items-end justify-between mb-10">
        <div>
            <span class="text-xs font-bold uppercase tracking-wider text-amber-400">Koleksi Spirits & Miras</span>
            <h2 class="text-3xl font-black text-white mt-1">Minuman Khusus & Spirits 100% Original</h2>
            <p class="text-sm text-slate-400 mt-2">Disediakan langsung oleh bartender berlisensi lengkap dengan ice bucket dan gelas rocks.</p>
        </div>
        <a href="{{ route('customer.menu') }}?category=alcohol" class="mt-4 md:mt-0 text-sm font-semibold text-amber-400 hover:text-white flex items-center">
            Lihat Menu Lengkap Minuman <i class="fa-solid fa-arrow-right ml-2 text-xs"></i>
        </a>
    </div>

    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6">
        @foreach($featuredAlcohol as $alc)
            <div class="bg-[#0c1222] border border-slate-800 rounded-2xl p-5 hover:border-amber-500/40 transition-all flex flex-col justify-between group">
                <div>
                    <div class="w-full h-36 rounded-xl bg-gradient-to-b from-slate-800 to-slate-900 flex items-center justify-center mb-4 relative overflow-hidden">
                        <i class="fa-solid fa-wine-bottle text-5xl text-amber-400/40 group-hover:scale-110 group-hover:text-amber-400 transition-all"></i>
                        @if($alc->alcohol_content)
                            <span class="absolute top-2 right-2 bg-slate-950/80 border border-slate-700 text-[10px] text-amber-300 px-2 py-0.5 rounded font-mono font-bold">
                                {{ $alc->alcohol_content }}
                            </span>
                        @endif
                    </div>
                    <span class="text-[10px] uppercase font-bold text-slate-400">{{ ucfirst($alc->unit) }}</span>
                    <h4 class="text-base font-bold text-white mt-1 group-hover:text-amber-300 transition-colors">{{ $alc->name }}</h4>
                    <p class="text-xs text-slate-400 mt-1 line-clamp-2">{{ $alc->description }}</p>
                </div>
                <div class="pt-4 mt-4 border-t border-slate-800/80 flex items-center justify-between">
                    <span class="text-sm font-extrabold text-amber-400 font-mono">{{ $alc->formatted_price }}</span>
                    <a href="{{ route('customer.reservation') }}" class="p-2 rounded-lg bg-slate-800 hover:bg-amber-500 hover:text-slate-950 text-slate-300 transition-all text-xs" title="Pre-order saat reservasi">
                        <i class="fa-solid fa-plus"></i> Pre-Order
                    </a>
                </div>
            </div>
        @endforeach
    </div>
</div>

<!-- Interactive Song Finder Preview -->
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 mb-24">
    <div class="bg-[#0c1222] border border-slate-800 rounded-3xl p-8 lg:p-12 text-center relative overflow-hidden">
        <div class="max-w-xl mx-auto">
            <div class="w-12 h-12 rounded-xl bg-purple-600/20 border border-purple-500/30 flex items-center justify-center text-purple-400 mx-auto mb-4">
                <i class="fa-solid fa-music text-xl"></i>
            </div>
            <h2 class="text-2xl sm:text-3xl font-black text-white">Cari Lagu Favorit Anda</h2>
            <p class="text-xs sm:text-sm text-slate-400 mt-2 mb-6">
                Pastikan lagu kesukaan Anda tersedia dalam koleksi 10.000+ library musik kami: Pop Indonesia, Western, Dangdut Koplo, Mandarin, hingga K-Pop.
            </p>
            <form action="{{ route('customer.songs') }}" method="GET" class="flex max-w-md mx-auto">
                <input type="text" name="q" placeholder="Cari judul lagu atau nama artis..."
                    class="flex-grow px-4 py-3 bg-slate-900 border border-slate-700 rounded-l-xl text-sm text-white focus:outline-none focus:border-purple-500">
                <button type="submit" class="px-6 py-3 bg-purple-600 hover:bg-purple-500 text-white font-bold text-sm rounded-r-xl transition-all">
                    <i class="fa-solid fa-magnifying-glass"></i> Cari
                </button>
            </form>
        </div>
    </div>
</div>
@endsection

