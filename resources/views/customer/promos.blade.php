@extends('layouts.app')

@section('title', 'Promo Menarik & Aturan Operasional - HY KAROKE AND LOUNGE')

@section('content')
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-12">
    <!-- Header -->
    <div class="text-center max-w-3xl mx-auto mb-12">
        <span class="text-xs font-bold uppercase tracking-wider text-amber-400">PENAWARAN SPESIAL & TATA TERTIB</span>
        <h1 class="text-3xl sm:text-5xl font-black text-white mt-1">Promo Khusus & Aturan Outlet</h1>
        <p class="text-sm text-slate-400 mt-2">
            Manfaatkan berbagai diskon paket hemat dan ketahui tata tertib berkunjung demi kenyamanan bersama.
        </p>
    </div>

    <!-- Active Promos Grid -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-8 mb-20">
        @foreach($promos as $promo)
            <div class="bg-[#0c1222] border border-purple-500/20 hover:border-purple-500/50 rounded-3xl p-8 flex flex-col justify-between shadow-2xl transition-all group">
                <div>
                    <div class="flex items-center justify-between mb-4">
                        <span class="px-3 py-1 rounded-md text-xs font-black uppercase tracking-wider bg-amber-500/20 text-amber-300 border border-amber-500/30">
                            {{ $promo->badge ?? 'PROMO EKSKLUSIF' }}
                        </span>
                        <span class="text-xs font-bold text-purple-400 font-mono">{{ $promo->discount_text }}</span>
                    </div>

                    <h3 class="text-xl font-bold text-white mb-3 group-hover:text-amber-300 transition-colors">{{ $promo->title }}</h3>
                    <p class="text-xs text-slate-300 leading-relaxed mb-6">{{ $promo->description }}</p>

                    @if($promo->terms)
                        <div class="p-4 rounded-xl bg-slate-900/80 border border-slate-800 text-[11px] text-slate-400 space-y-1">
                            <span class="font-bold text-slate-200 block mb-1">Syarat & Ketentuan:</span>
                            <p class="leading-relaxed whitespace-pre-line">{{ $promo->terms }}</p>
                        </div>
                    @endif
                </div>

                <div class="pt-6 mt-6 border-t border-slate-800">
                    <a href="{{ route('customer.reservation') }}"
                        class="w-full py-3 px-4 rounded-xl bg-gradient-to-r from-purple-600 to-indigo-600 hover:from-purple-500 hover:to-indigo-500 text-white font-bold text-xs text-center block transition-all shadow-lg shadow-purple-600/20">
                        Klaim Promo Ini Saat Reservasi &rarr;
                    </a>
                </div>
            </div>
        @endforeach
    </div>

    <!-- FAQ & Aturan Operasional Outlet -->
    <div class="bg-[#0c1222] border border-slate-800 rounded-3xl p-8 lg:p-12">
        <div class="max-w-3xl mb-8">
            <span class="text-xs font-bold uppercase tracking-wider text-purple-400">FAQ & POLICIES</span>
            <h2 class="text-2xl sm:text-3xl font-black text-white mt-1">Aturan Operasional Outlet Karaoke</h2>
            <p class="text-xs sm:text-sm text-slate-400 mt-2">
                Panduan keamanan dan kenyamanan tamu selama berada di area HY KAROKE AND LOUNGE.
            </p>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <div class="p-5 rounded-2xl bg-slate-900/60 border border-slate-800">
                <div class="flex items-start space-x-3">
                    <i class="fa-solid fa-clock text-purple-400 text-lg mt-0.5"></i>
                    <div>
                        <h4 class="text-sm font-bold text-white">Jam Operasional & Toleransi Reservasi</h4>
                        <p class="text-xs text-slate-400 mt-1 leading-relaxed">
                            Buka setiap hari pukul 13.00 s/d 03.00 WIB. Toleransi keterlambatan reservasi online adalah 20 menit dari jam booking yang dipilih sebelum kamar dilepaskan ke antrian tamu walk-in.
                        </p>
                    </div>
                </div>
            </div>

            <div class="p-5 rounded-2xl bg-slate-900/60 border border-slate-800">
                <div class="flex items-start space-x-3">
                    <i class="fa-solid fa-wine-glass text-amber-400 text-lg mt-0.5"></i>
                    <div>
                        <h4 class="text-sm font-bold text-white">Kebijakan Minuman & Corkage Fee</h4>
                        <p class="text-xs text-slate-400 mt-1 leading-relaxed">
                            Kami menyediakan ragam koleksi spirits & bir berlisensi 100% original. Tamu yang membawa minuman beralkohol dari luar dikenakan *corkage fee* sebesar Rp 250.000 per botol.
                        </p>
                    </div>
                </div>
            </div>

            <div class="p-5 rounded-2xl bg-slate-900/60 border border-slate-800">
                <div class="flex items-start space-x-3">
                    <i class="fa-solid fa-person-dress text-pink-400 text-lg mt-0.5"></i>
                    <div>
                        <h4 class="text-sm font-bold text-white">Layanan Pemandu Lagu (LC)</h4>
                        <p class="text-xs text-slate-400 mt-1 leading-relaxed">
                            Layanan pemandu lagu bersifat profesional untuk mendampingi bernyanyi dan mencairkan suasana. Tamu diwajibkan menjaga kesopanan dan etika terhadap staf maupun personel LC.
                        </p>
                    </div>
                </div>
            </div>

            <div class="p-5 rounded-2xl bg-slate-900/60 border border-slate-800">
                <div class="flex items-start space-x-3">
                    <i class="fa-solid fa-shield-halved text-emerald-400 text-lg mt-0.5"></i>
                    <div>
                        <h4 class="text-sm font-bold text-white">Keamanan & Larangan Narkoba/Sajam</h4>
                        <p class="text-xs text-slate-400 mt-1 leading-relaxed">
                            Dilarang keras membawa senjata tajam, senjata api, serta zat narkotika/obat terlarang ke dalam area lounge & kamar karaoke. Pelanggaran akan diserahkan ke pihak berwajib.
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

