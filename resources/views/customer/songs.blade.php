@extends('layouts.app')

@section('title', 'Song Finder - Cari Ketersediaan Lagu Favorit - Grand Karaoke')

@section('content')
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-12">
    <!-- Header -->
    <div class="text-center max-w-3xl mx-auto mb-10">
        <span class="text-xs font-bold uppercase tracking-wider text-purple-400">DATABASE MUSIK TERLENGKAP</span>
        <h1 class="text-3xl sm:text-5xl font-black text-white mt-1">Katalog Song Finder</h1>
        <p class="text-sm text-slate-400 mt-2">
            Periksa dan temukan ketersediaan lagu favorit Anda sebelum berkunjung. Lebih dari 10.000+ lagu terupdate setiap minggunya!
        </p>
    </div>

    <!-- Search & Filter Card -->
    <div class="bg-[#0c1222] border border-slate-800 rounded-2xl p-6 mb-10 shadow-xl">
        <form action="{{ route('customer.songs') }}" method="GET" class="grid grid-cols-1 md:grid-cols-4 gap-4">
            <div class="md:col-span-2">
                <label class="block text-xs font-semibold text-slate-300 uppercase tracking-wider mb-2">Judul Lagu atau Nama Artis</label>
                <div class="relative">
                    <input type="text" name="q" value="{{ $search }}" placeholder="Ketik judul lagu / penyanyi (misal: Sheila on 7, Bruno Mars)..."
                        class="w-full pl-10 pr-4 py-2.5 bg-slate-900 border border-slate-700/80 rounded-xl text-sm text-white focus:outline-none focus:border-purple-500">
                    <span class="absolute inset-y-0 left-0 flex items-center pl-3.5 text-slate-500">
                        <i class="fa-solid fa-magnifying-glass text-xs"></i>
                    </span>
                </div>
            </div>

            <div>
                <label class="block text-xs font-semibold text-slate-300 uppercase tracking-wider mb-2">Genre Musik</label>
                <select name="genre" class="w-full px-3.5 py-2.5 bg-slate-900 border border-slate-700/80 rounded-xl text-sm text-white focus:outline-none focus:border-purple-500">
                    <option value="">Semua Genre</option>
                    @foreach($genres as $g)
                        <option value="{{ $g }}" {{ $genre === $g ? 'selected' : '' }}>{{ $g }}</option>
                    @endforeach
                </select>
            </div>

            <div>
                <label class="block text-xs font-semibold text-slate-300 uppercase tracking-wider mb-2">Bahasa</label>
                <div class="flex space-x-2">
                    <select name="language" class="w-full px-3.5 py-2.5 bg-slate-900 border border-slate-700/80 rounded-xl text-sm text-white focus:outline-none focus:border-purple-500">
                        <option value="">Semua Bahasa</option>
                        @foreach($languages as $l)
                            <option value="{{ $l }}" {{ $language === $l ? 'selected' : '' }}>{{ $l }}</option>
                        @endforeach
                    </select>
                    <button type="submit" class="px-5 py-2.5 bg-purple-600 hover:bg-purple-500 text-white font-bold text-sm rounded-xl transition-all shrink-0">
                        Filter
                    </button>
                </div>
            </div>
        </form>
    </div>

    <!-- Song List Grid -->
    @if($songs->isEmpty())
        <div class="text-center py-20 bg-[#0c1222] border border-slate-800 rounded-3xl">
            <i class="fa-solid fa-music text-4xl text-slate-600 mb-3"></i>
            <h3 class="text-lg font-bold text-slate-300">Lagu tidak ditemukan</h3>
            <p class="text-xs text-slate-500 mt-1">Coba gunakan kata kunci lain atau kosongkan filter untuk melihat semua lagu.</p>
        </div>
    @else
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4 mb-8">
            @foreach($songs as $song)
                <div class="bg-[#0c1222] border border-slate-800 hover:border-purple-500/40 rounded-xl p-4 transition-all flex items-start space-x-3.5 group">
                    <div class="w-12 h-12 rounded-xl bg-gradient-to-tr from-purple-900 to-indigo-900 border border-purple-500/30 flex items-center justify-center text-purple-300 shrink-0 group-hover:scale-105 transition-transform">
                        <i class="fa-solid fa-music text-base"></i>
                    </div>

                    <div class="flex-grow overflow-hidden">
                        <div class="flex items-center justify-between">
                            <span class="text-[10px] font-bold text-purple-400 uppercase tracking-wider">{{ $song->genre }}</span>
                            @if($song->is_favorite)
                                <span class="text-amber-400 text-xs" title="Lagu Terfavorit"><i class="fa-solid fa-star"></i> Top Hits</span>
                            @endif
                        </div>
                        <h4 class="text-sm font-bold text-white truncate mt-0.5 group-hover:text-amber-300 transition-colors">{{ $song->title }}</h4>
                        <p class="text-xs text-slate-400 truncate">{{ $song->artist }}</p>

                        @if($song->lyrics_snippet)
                            <p class="text-[11px] text-slate-500 italic mt-2 line-clamp-1 border-l-2 border-purple-500/40 pl-2">
                                "{{ $song->lyrics_snippet }}"
                            </p>
                        @endif

                        <div class="flex items-center justify-between mt-3 pt-2 border-t border-slate-800/80 text-[10px] text-slate-500">
                            <span><i class="fa-solid fa-globe mr-1"></i> {{ $song->language }}</span>
                            <span><i class="fa-solid fa-play mr-1"></i> {{ number_format($song->views_count) }}x Dinyanyikan</span>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>

        <!-- Pagination -->
        <div class="mt-8">
            {{ $songs->links() }}
        </div>
    @endif
</div>
@endsection
