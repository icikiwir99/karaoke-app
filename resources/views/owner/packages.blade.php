@extends('layouts.admin')

@section('title', 'Master Paket Bundling Karaoke - Owner Area')
@section('header_title', 'Master Paket Bundling Karaoke')
@section('header_subtitle', 'Pengaturan skema paket hemat gabungan (Sewa Room + Pemandu Lagu LC + Minuman Khusus/F&B)')

@section('content')
<div class="space-y-6">
    <!-- Action Bar -->
    <div class="bg-[#0c1222] border border-slate-800 rounded-2xl p-4 flex flex-wrap items-center justify-between gap-4">
        <div>
            <span class="text-xs font-bold text-slate-300">Total Paket Bundling Aktif: <span class="text-amber-400 font-mono font-bold">{{ $packages->count() }} Paket</span></span>
            <p class="text-[11px] text-slate-500">Paket bundling otomatis tampil di halaman reservasi pelanggan online.</p>
        </div>

        <button type="button" onclick="openModal('addPackageModal')"
            class="px-4 py-2 bg-gradient-to-r from-purple-600 to-indigo-600 hover:from-purple-500 hover:to-indigo-500 text-white text-xs font-black rounded-xl shadow-lg shadow-purple-600/25 transition-all">
            <i class="fa-solid fa-plus mr-1.5"></i> Buat Paket Bundling Baru
        </button>
    </div>

    <!-- Packages Cards Grid -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
        @foreach($packages as $pkg)
            <div class="bg-[#0c1222] border border-slate-800 hover:border-purple-500/40 rounded-2xl p-6 shadow-xl flex flex-col justify-between transition-all group">
                <div>
                    <div class="flex items-center justify-between mb-3">
                        <span class="px-2.5 py-0.5 rounded text-[10px] font-black uppercase tracking-wider bg-purple-500/20 text-purple-300 border border-purple-500/30">
                            Tipe {{ strtoupper($pkg->room_type) }}
                        </span>
                        <form method="POST" action="{{ route('owner.packages.delete', $pkg->id) }}" onsubmit="return confirm('Hapus paket bundling ini?')">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="text-slate-500 hover:text-rose-400 p-1" title="Hapus Paket">
                                <i class="fa-solid fa-trash text-xs"></i>
                            </button>
                        </form>
                    </div>

                    <h3 class="text-lg font-bold text-white group-hover:text-amber-300 transition-colors">{{ $pkg->name }}</h3>
                    <p class="text-xs text-slate-400 mt-1 leading-relaxed">{{ $pkg->description }}</p>

                    <div class="my-4 pb-4 border-b border-slate-800">
                        <span class="text-[10px] text-slate-500 uppercase font-bold block">Harga Paket</span>
                        <span class="text-2xl font-black text-amber-400 font-mono">{{ $pkg->formatted_price }}</span>
                    </div>

                    <!-- Items Included -->
                    <div class="space-y-2 text-xs text-slate-300">
                        <span class="text-[10px] font-bold text-slate-500 uppercase block tracking-wider">Komponen Yang Termasuk:</span>
                        <div class="p-3 bg-slate-900/80 border border-slate-800 rounded-xl space-y-1.5">
                            <div class="flex items-center text-slate-300">
                                <i class="fa-solid fa-door-open text-purple-400 w-5"></i>
                                <span>Sewa Kamar {{ strtoupper($pkg->room_type) }} x {{ $pkg->room_hours }} Jam</span>
                            </div>
                            @if($pkg->include_lc_qty > 0)
                                <div class="flex items-center text-pink-300">
                                    <i class="fa-solid fa-person-dress text-pink-400 w-5"></i>
                                    <span>{{ $pkg->include_lc_qty }}x Pemandu Lagu (LC) x {{ $pkg->include_lc_hours }} Jam</span>
                                </div>
                            @endif
                            @foreach($pkg->items as $item)
                                <div class="flex items-center text-amber-300">
                                    <i class="fa-solid fa-wine-glass text-amber-400 w-5"></i>
                                    <span>{{ $item->quantity }}x {{ $item->product ? $item->product->name : 'Item' }}</span>
                                </div>
                            @endforeach
                        </div>
                    </div>
                </div>
            </div>
        @endforeach
    </div>
</div>

<!-- Add Package Modal -->
<div id="addPackageModal" class="fixed inset-0 bg-slate-950/80 backdrop-blur-sm z-50 hidden flex items-center justify-center p-4">
    <div class="bg-[#0c1222] border border-slate-800 rounded-3xl max-w-lg w-full p-6 sm:p-8 shadow-2xl relative max-h-[90vh] overflow-y-auto">
        <button type="button" onclick="closeModal('addPackageModal')" class="absolute top-5 right-5 text-slate-400 hover:text-white">
            <i class="fa-solid fa-xmark text-lg"></i>
        </button>

        <div class="mb-5">
            <span class="text-[10px] font-bold uppercase tracking-wider text-purple-400">PAKET BUNDLING BARU</span>
            <h3 class="text-xl font-black text-white mt-0.5">Desain Paket Hemat</h3>
        </div>

        <form method="POST" action="{{ route('owner.packages.store') }}" class="space-y-4">
            @csrf
            <div>
                <label class="block text-xs font-semibold text-slate-300 uppercase mb-1">Nama Paket Bundling *</label>
                <input type="text" name="name" required placeholder="Contoh: Paket Weekend Party VIP"
                    class="w-full px-3.5 py-2.5 bg-slate-900 border border-slate-700 rounded-xl text-sm text-white focus:outline-none focus:border-purple-500">
            </div>

            <div class="grid grid-cols-2 gap-3">
                <div>
                    <label class="block text-xs font-semibold text-slate-300 uppercase mb-1">Tipe Kamar Yang Termasuk *</label>
                    <select name="room_type" required class="w-full px-3.5 py-2.5 bg-slate-900 border border-slate-700 rounded-xl text-xs text-white focus:outline-none focus:border-purple-500">
                        <option value="small">Small Room</option>
                        <option value="medium" selected>Medium Room</option>
                        <option value="large">Large Room</option>
                        <option value="vip">VIP Suite</option>
                    </select>
                </div>
                <div>
                    <label class="block text-xs font-semibold text-slate-300 uppercase mb-1">Durasi Kamar (Jam) *</label>
                    <input type="number" name="room_hours" required min="1" value="3"
                        class="w-full px-3.5 py-2.5 bg-slate-900 border border-slate-700 rounded-xl text-xs text-white font-mono focus:outline-none focus:border-purple-500">
                </div>
            </div>

            <div class="grid grid-cols-2 gap-3">
                <div>
                    <label class="block text-xs font-semibold text-pink-300 uppercase mb-1">Include Jumlah LC (Orang)</label>
                    <input type="number" name="include_lc_qty" min="0" value="1"
                        class="w-full px-3.5 py-2.5 bg-slate-900 border border-slate-700 rounded-xl text-xs text-white font-mono focus:outline-none focus:border-pink-500">
                </div>
                <div>
                    <label class="block text-xs font-semibold text-pink-300 uppercase mb-1">Include Durasi LC (Jam)</label>
                    <input type="number" name="include_lc_hours" min="0" value="2"
                        class="w-full px-3.5 py-2.5 bg-slate-900 border border-slate-700 rounded-xl text-xs text-white font-mono focus:outline-none focus:border-pink-500">
                </div>
            </div>

            <div>
                <label class="block text-xs font-semibold text-amber-400 uppercase mb-1">Total Harga Paket (Rp) *</label>
                <input type="number" name="price" required min="0" step="10000" placeholder="Contoh: 1850000"
                    class="w-full px-3.5 py-2.5 bg-slate-900 border border-slate-700 rounded-xl text-sm text-amber-400 font-mono font-bold focus:outline-none focus:border-amber-400">
            </div>

            <div>
                <label class="block text-xs font-semibold text-slate-300 uppercase mb-1">Pilih Produk Minuman / Makanan Yang Include:</label>
                <div class="space-y-2 max-h-40 overflow-y-auto pr-1">
                    @foreach($products as $prod)
                        <div class="flex items-center justify-between p-2 rounded-lg bg-slate-900/60 border border-slate-800 text-xs">
                            <span class="text-slate-300 truncate pr-2">{{ $prod->name }}</span>
                            <div class="flex items-center space-x-1 shrink-0">
                                <input type="hidden" name="items[{{ $prod->id }}][product_id]" value="{{ $prod->id }}">
                                <input type="number" name="items[{{ $prod->id }}][quantity]" min="0" max="10" value="0"
                                    class="w-14 px-2 py-1 bg-slate-950 border border-slate-700 rounded text-center text-xs text-white font-mono">
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>

            <div>
                <label class="block text-xs font-semibold text-slate-300 uppercase mb-1">Deskripsi & Syarat Paket</label>
                <textarea name="description" rows="2" placeholder="Keterangan isi paket..."
                    class="w-full px-3.5 py-2.5 bg-slate-900 border border-slate-700 rounded-xl text-xs text-white focus:outline-none focus:border-purple-500"></textarea>
            </div>

            <div class="pt-2">
                <button type="submit" class="w-full py-3 px-4 rounded-xl bg-gradient-to-r from-purple-600 to-indigo-600 hover:from-purple-500 hover:to-indigo-500 text-white font-extrabold text-sm shadow-xl shadow-purple-600/30 transition-all">
                    Simpan Paket Bundling
                </button>
            </div>
        </form>
    </div>
</div>

<script>
    function openModal(id) {
        document.getElementById(id).classList.remove('hidden');
    }
    function closeModal(id) {
        document.getElementById(id).classList.add('hidden');
    }
</script>
@endsection
