@extends('layouts.app')

@section('title', 'Katalog F&B & Minuman Khusus (Miras) - HY KAROKE AND LOUNGE')

@section('content')
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-12">
    <!-- Header -->
    <div class="text-center max-w-3xl mx-auto mb-10">
        <span class="text-xs font-bold uppercase tracking-wider text-amber-400">ALA CARTE MENU & SPIRITS</span>
        <h1 class="text-3xl sm:text-5xl font-black text-white mt-1">Katalog F&B & Minuman Khusus</h1>
        <p class="text-sm text-slate-400 mt-2">
            Pilihan hidangan lezat dan minuman beralkohol original berkualitas tinggi untuk melengkapi sesi bernyanyi Anda.
        </p>
    </div>

    <!-- Category Filters & Search Bar -->
    <div class="bg-[#0c1222] border border-slate-800 rounded-2xl p-4 mb-10 flex flex-col md:flex-row items-center justify-between gap-4">
        <!-- Filter Tabs -->
        <div class="flex flex-wrap items-center gap-2 w-full md:w-auto">
            <a href="{{ route('customer.menu', ['category' => 'all', 'q' => $search]) }}"
                class="px-4 py-2 rounded-xl text-xs font-bold transition-all {{ $category === 'all' ? 'bg-purple-600 text-white shadow-lg shadow-purple-600/30' : 'bg-slate-900 text-slate-300 hover:bg-slate-800 hover:text-white border border-slate-800' }}">
                <i class="fa-solid fa-list mr-1.5"></i> Semua Menu
            </a>
            <a href="{{ route('customer.menu', ['category' => 'alcohol', 'q' => $search]) }}"
                class="px-4 py-2 rounded-xl text-xs font-bold transition-all {{ $category === 'alcohol' ? 'bg-amber-500 text-slate-950 shadow-lg shadow-amber-500/30 font-black' : 'bg-slate-900 text-amber-300 hover:bg-slate-800 border border-slate-800' }}">
                <i class="fa-solid fa-wine-bottle mr-1.5"></i> Minuman Khusus & Miras
            </a>
            <a href="{{ route('customer.menu', ['category' => 'food', 'q' => $search]) }}"
                class="px-4 py-2 rounded-xl text-xs font-bold transition-all {{ $category === 'food' ? 'bg-purple-600 text-white shadow-lg shadow-purple-600/30' : 'bg-slate-900 text-slate-300 hover:bg-slate-800 hover:text-white border border-slate-800' }}">
                <i class="fa-solid fa-utensils mr-1.5"></i> Makanan & Snack
            </a>
            <a href="{{ route('customer.menu', ['category' => 'softdrink', 'q' => $search]) }}"
                class="px-4 py-2 rounded-xl text-xs font-bold transition-all {{ $category === 'softdrink' ? 'bg-purple-600 text-white shadow-lg shadow-purple-600/30' : 'bg-slate-900 text-slate-300 hover:bg-slate-800 hover:text-white border border-slate-800' }}">
                <i class="fa-solid fa-mug-hot mr-1.5"></i> Softdrinks & Tea
            </a>
        </div>

        <!-- Search Input -->
        <form action="{{ route('customer.menu') }}" method="GET" class="w-full md:w-72">
            <input type="hidden" name="category" value="{{ $category }}">
            <div class="relative">
                <input type="text" name="q" value="{{ $search }}" placeholder="Cari nama menu..."
                    class="w-full pl-9 pr-4 py-2 bg-slate-900 border border-slate-700/80 rounded-xl text-xs text-slate-100 placeholder-slate-500 focus:outline-none focus:border-purple-500">
                <span class="absolute inset-y-0 left-0 flex items-center pl-3 text-slate-500">
                    <i class="fa-solid fa-magnifying-glass text-xs"></i>
                </span>
            </div>
        </form>
    </div>

    <!-- Product Grid -->
    @if($products->isEmpty())
        <div class="text-center py-20 bg-[#0c1222] border border-slate-800 rounded-3xl">
            <i class="fa-solid fa-ban text-4xl text-slate-600 mb-3"></i>
            <h3 class="text-lg font-bold text-slate-300">Tidak ada produk yang sesuai</h3>
            <p class="text-xs text-slate-500 mt-1">Silakan coba kata kunci atau filter kategori lainnya.</p>
        </div>
    @else
        <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-6">
            @foreach($products as $product)
                <div class="bg-[#0c1222] border border-slate-800 rounded-2xl p-5 hover:border-purple-500/40 transition-all flex flex-col justify-between group shadow-xl">
                    <div>
                        <!-- Icon Card Header -->
                        <div class="w-full h-36 rounded-xl bg-gradient-to-tr from-slate-900 via-slate-800 to-slate-900 flex items-center justify-center mb-4 relative overflow-hidden">
                            @if($product->category === 'alcohol')
                                <i class="fa-solid fa-wine-bottle text-5xl text-amber-400/40 group-hover:scale-110 group-hover:text-amber-400 transition-all"></i>
                                @if($product->alcohol_content)
                                    <span class="absolute top-2 right-2 bg-amber-500/20 border border-amber-500/40 text-[10px] text-amber-300 px-2 py-0.5 rounded font-mono font-bold">
                                        {{ $product->alcohol_content }}
                                    </span>
                                @endif
                            @elseif($product->category === 'food')
                                <i class="fa-solid fa-burger text-5xl text-purple-400/40 group-hover:scale-110 group-hover:text-purple-400 transition-all"></i>
                            @else
                                <i class="fa-solid fa-glass-water text-5xl text-cyan-400/40 group-hover:scale-110 group-hover:text-cyan-400 transition-all"></i>
                            @endif

                            <span class="absolute bottom-2 left-2 bg-slate-950/80 border border-slate-800 text-[9px] uppercase tracking-wider font-bold text-slate-400 px-2 py-0.5 rounded">
                                Satuan: {{ ucfirst($product->unit) }}
                            </span>
                        </div>

                        <!-- Product Title -->
                        <div class="flex items-center space-x-1.5 mb-1">
                            @if($product->category === 'alcohol')
                                <span class="w-2 h-2 rounded-full bg-amber-400"></span>
                                <span class="text-[10px] uppercase font-bold text-amber-400">Spirits / Miras</span>
                            @elseif($product->category === 'food')
                                <span class="w-2 h-2 rounded-full bg-purple-400"></span>
                                <span class="text-[10px] uppercase font-bold text-purple-400">Makanan</span>
                            @else
                                <span class="w-2 h-2 rounded-full bg-cyan-400"></span>
                                <span class="text-[10px] uppercase font-bold text-cyan-400">Softdrink</span>
                            @endif
                        </div>

                        <h3 class="text-base font-bold text-white group-hover:text-amber-300 transition-colors line-clamp-1">{{ $product->name }}</h3>
                        <p class="text-xs text-slate-400 mt-1 line-clamp-2 leading-relaxed">{{ $product->description }}</p>
                    </div>

                    <!-- Price & Preorder CTA -->
                    <div class="pt-4 mt-4 border-t border-slate-800/80 flex items-center justify-between">
                        <div>
                            <span class="text-[10px] text-slate-500 block uppercase font-bold">Harga</span>
                            <span class="text-sm font-black text-amber-400 font-mono">{{ $product->formatted_price }}</span>
                        </div>

                        <a href="{{ route('customer.reservation') }}"
                            class="px-3 py-1.5 bg-slate-800 hover:bg-purple-600 hover:text-white text-slate-300 rounded-lg text-xs font-bold transition-all flex items-center"
                            title="Pre-order di Form Reservasi">
                            <i class="fa-solid fa-cart-plus mr-1"></i> Pre-Order
                        </a>
                    </div>
                </div>
            @endforeach
        </div>
    @endif
</div>
@endsection

