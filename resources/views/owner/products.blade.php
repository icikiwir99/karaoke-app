@extends('layouts.admin')

@section('title', 'Master Data F&B & Minuman Khusus (Miras) - Owner Area')
@section('header_title', 'Master Data F&B & Minuman Khusus (Miras)')
@section('header_subtitle', 'Kontrol stok, batas minimum persediaan, harga modal (COGS), dan margin jual')

@section('content')
<div class="space-y-6">
    <!-- Action Bar & Category Filters -->
    <div class="bg-[#0c1222] border border-slate-800 rounded-2xl p-4 flex flex-wrap items-center justify-between gap-4">
        <!-- Filter Buttons -->
        <div class="flex flex-wrap items-center gap-2">
            <a href="{{ route('owner.products', ['category' => 'all']) }}"
                class="px-3.5 py-1.5 rounded-xl text-xs font-bold transition-all {{ $category === 'all' && !request()->query('low_stock') ? 'bg-purple-600 text-white shadow-lg shadow-purple-600/30' : 'bg-slate-900 text-slate-300 hover:bg-slate-800 border border-slate-800' }}">
                Semua Produk
            </a>
            <a href="{{ route('owner.products', ['category' => 'alcohol']) }}"
                class="px-3.5 py-1.5 rounded-xl text-xs font-bold transition-all {{ $category === 'alcohol' ? 'bg-amber-500 text-slate-950 font-black shadow-lg shadow-amber-500/30' : 'bg-slate-900 text-amber-300 hover:bg-slate-800 border border-slate-800' }}">
                <i class="fa-solid fa-wine-bottle mr-1 text-[11px]"></i> Miras / Spirits
            </a>
            <a href="{{ route('owner.products', ['category' => 'food']) }}"
                class="px-3.5 py-1.5 rounded-xl text-xs font-bold transition-all {{ $category === 'food' ? 'bg-purple-600 text-white shadow-lg shadow-purple-600/30' : 'bg-slate-900 text-purple-300 hover:bg-slate-800 border border-slate-800' }}">
                Makanan & Snack
            </a>
            <a href="{{ route('owner.products', ['category' => 'softdrink']) }}"
                class="px-3.5 py-1.5 rounded-xl text-xs font-bold transition-all {{ $category === 'softdrink' ? 'bg-purple-600 text-white shadow-lg shadow-purple-600/30' : 'bg-slate-900 text-cyan-300 hover:bg-slate-800 border border-slate-800' }}">
                Softdrinks
            </a>
            <a href="{{ route('owner.products', ['low_stock' => '1']) }}"
                class="px-3.5 py-1.5 rounded-xl text-xs font-bold transition-all {{ request()->query('low_stock') === '1' ? 'bg-rose-600 text-white shadow-lg shadow-rose-600/30 font-black' : 'bg-slate-900 text-rose-300 hover:bg-slate-800 border border-slate-800' }}">
                <i class="fa-solid fa-triangle-exclamation mr-1 text-[11px]"></i> Stok Menipis ({{ $lowStockCount }})
            </a>
        </div>

        <!-- Add Product Button -->
        <button type="button" onclick="openModal('addProductModal')"
            class="px-4 py-2 bg-gradient-to-r from-purple-600 to-indigo-600 hover:from-purple-500 hover:to-indigo-500 text-white text-xs font-black rounded-xl shadow-lg shadow-purple-600/25 transition-all">
            <i class="fa-solid fa-plus mr-1.5"></i> Tambah Menu / Produk Baru
        </button>
    </div>

    <!-- Products Table -->
    <div class="bg-[#0c1222] border border-slate-800 rounded-2xl p-6 shadow-xl">
        <div class="overflow-x-auto">
            <table class="w-full text-left text-xs text-slate-300">
                <thead class="bg-slate-900/80 text-[10px] uppercase font-bold text-slate-400 border-b border-slate-800">
                    <tr>
                        <th class="py-3 px-4">Nama Produk</th>
                        <th class="py-3 px-4">Kategori</th>
                        <th class="py-3 px-4">Satuan</th>
                        <th class="py-3 px-4 text-right">Harga Beli (Modal)</th>
                        <th class="py-3 px-4 text-right">Harga Jual</th>
                        <th class="py-3 px-4 text-right">Profit Margin</th>
                        <th class="py-3 px-4 text-center">Stok Fisik</th>
                        <th class="py-3 px-4 text-center">Batas Min</th>
                        <th class="py-3 px-4 text-center">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-800/60">
                    @forelse($products as $p)
                        <tr class="hover:bg-slate-900/40 transition-colors">
                            <td class="py-3.5 px-4 font-bold text-white">
                                <span class="block text-sm">{{ $p->name }}</span>
                                @if($p->alcohol_content)
                                    <span class="text-[10px] text-amber-400 font-mono font-semibold">{{ $p->alcohol_content }}</span>
                                @endif
                            </td>
                            <td class="py-3.5 px-4">
                                @if($p->category === 'alcohol')
                                    <span class="px-2 py-0.5 rounded text-[10px] uppercase font-bold bg-amber-500/20 text-amber-300 border border-amber-500/30">Miras</span>
                                @elseif($p->category === 'food')
                                    <span class="px-2 py-0.5 rounded text-[10px] uppercase font-bold bg-purple-500/20 text-purple-300 border border-purple-500/30">Food</span>
                                @else
                                    <span class="px-2 py-0.5 rounded text-[10px] uppercase font-bold bg-cyan-500/20 text-cyan-300 border border-cyan-500/30">Softdrink</span>
                                @endif
                            </td>
                            <td class="py-3.5 px-4 uppercase text-[11px] font-semibold text-slate-400">{{ $p->unit }}</td>
                            <td class="py-3.5 px-4 text-right font-mono font-semibold text-slate-400">
                                {{ $p->formatted_purchase_price }}
                            </td>
                            <td class="py-3.5 px-4 text-right font-mono font-bold text-white">
                                {{ $p->formatted_price }}
                            </td>
                            <td class="py-3.5 px-4 text-right font-mono font-bold text-emerald-400">
                                + Rp {{ number_format($p->margin, 0, ',', '.') }}
                                <span class="text-[9px] text-amber-400 block font-sans">({{ $p->margin_percentage }}%)</span>
                            </td>
                            <td class="py-3.5 px-4 text-center">
                                @if($p->isLowStock())
                                    <span class="px-2.5 py-1 rounded-md text-xs font-black font-mono bg-rose-500/20 text-rose-400 border border-rose-500/40 animate-pulse">
                                        {{ $p->stock }} (Menipis!)
                                    </span>
                                @else
                                    <span class="font-mono font-bold text-slate-200 text-xs">{{ $p->stock }}</span>
                                @endif
                            </td>
                            <td class="py-3.5 px-4 text-center font-mono text-slate-400">{{ $p->min_stock }}</td>
                            <td class="py-3.5 px-4 text-center">
                                <div class="flex items-center justify-center space-x-1.5">
                                    <button type="button" onclick="openEditModal({{ json_encode($p) }})"
                                        class="p-1.5 bg-slate-800 hover:bg-slate-700 text-purple-400 rounded-lg text-xs transition-all" title="Edit Data">
                                        <i class="fa-solid fa-pen-to-square"></i>
                                    </button>
                                    <form method="POST" action="{{ route('owner.products.delete', $p->id) }}" onsubmit="return confirm('Yakin hapus produk ini?')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="p-1.5 bg-slate-800 hover:bg-rose-900/60 text-slate-400 hover:text-rose-300 rounded-lg text-xs transition-all" title="Hapus">
                                            <i class="fa-solid fa-trash"></i>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="9" class="py-8 text-center text-slate-500">Tidak ada produk yang terdaftar pada filter ini.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="mt-6">
            {{ $products->links() }}
        </div>
    </div>
</div>

<!-- ==================== PRODUCT MODALS ==================== -->

<!-- 1. Add Product Modal -->
<div id="addProductModal" class="fixed inset-0 bg-slate-950/80 backdrop-blur-sm z-50 hidden flex items-center justify-center p-4">
    <div class="bg-[#0c1222] border border-slate-800 rounded-3xl max-w-lg w-full p-6 sm:p-8 shadow-2xl relative max-h-[90vh] overflow-y-auto">
        <button type="button" onclick="closeModal('addProductModal')" class="absolute top-5 right-5 text-slate-400 hover:text-white">
            <i class="fa-solid fa-xmark text-lg"></i>
        </button>

        <div class="mb-5">
            <span class="text-[10px] font-bold uppercase tracking-wider text-purple-400">MASTER DATA PRODUK</span>
            <h3 class="text-xl font-black text-white mt-0.5">Tambah Produk F&B / Miras Baru</h3>
        </div>

        <form method="POST" action="{{ route('owner.products.store') }}" class="space-y-4">
            @csrf
            <div>
                <label class="block text-xs font-semibold text-slate-300 uppercase mb-1">Nama Produk *</label>
                <input type="text" name="name" required placeholder="Contoh: Chivas Regal 12 YO (750ml)"
                    class="w-full px-3.5 py-2.5 bg-slate-900 border border-slate-700 rounded-xl text-sm text-white focus:outline-none focus:border-purple-500">
            </div>

            <div class="grid grid-cols-2 gap-3">
                <div>
                    <label class="block text-xs font-semibold text-slate-300 uppercase mb-1">Kategori *</label>
                    <select name="category" required class="w-full px-3.5 py-2.5 bg-slate-900 border border-slate-700 rounded-xl text-xs text-white focus:outline-none focus:border-purple-500">
                        <option value="alcohol">Minuman Khusus / Miras</option>
                        <option value="food">Makanan / Snack</option>
                        <option value="softdrink">Softdrink / Minuman Ringan</option>
                    </select>
                </div>
                <div>
                    <label class="block text-xs font-semibold text-slate-300 uppercase mb-1">Satuan Kemasan *</label>
                    <select name="unit" required class="w-full px-3.5 py-2.5 bg-slate-900 border border-slate-700 rounded-xl text-xs text-white focus:outline-none focus:border-purple-500">
                        <option value="bottle">Bottle (Botol)</option>
                        <option value="bucket">Bucket (Ember Bir)</option>
                        <option value="tower">Tower (3 Liter)</option>
                        <option value="can">Can (Kaleng)</option>
                        <option value="glass">Glass (Gelas / Shot)</option>
                        <option value="portion">Portion (Porsi Makanan)</option>
                    </select>
                </div>
            </div>

            <div class="grid grid-cols-2 gap-3">
                <div>
                    <label class="block text-xs font-semibold text-slate-300 uppercase mb-1">Harga Beli / Modal (Rp) *</label>
                    <input type="number" name="purchase_price" required min="0" step="500" placeholder="Contoh: 650000"
                        class="w-full px-3.5 py-2.5 bg-slate-900 border border-slate-700 rounded-xl text-xs text-white font-mono focus:outline-none focus:border-purple-500">
                </div>
                <div>
                    <label class="block text-xs font-semibold text-slate-300 uppercase mb-1">Harga Jual Konsumen (Rp) *</label>
                    <input type="number" name="selling_price" required min="0" step="500" placeholder="Contoh: 1450000"
                        class="w-full px-3.5 py-2.5 bg-slate-900 border border-slate-700 rounded-xl text-xs text-amber-400 font-mono font-bold focus:outline-none focus:border-purple-500">
                </div>
            </div>

            <div class="grid grid-cols-2 gap-3">
                <div>
                    <label class="block text-xs font-semibold text-slate-300 uppercase mb-1">Jumlah Stok Awal *</label>
                    <input type="number" data-stepper name="stock" required min="0" value="10"
                        class="w-full px-3.5 py-2.5 bg-slate-900 border border-slate-700 rounded-xl text-xs text-white font-mono focus:outline-none focus:border-purple-500">
                </div>
                <div>
                    <label class="block text-xs font-semibold text-rose-300 uppercase mb-1">Batas Minimum Alert (Stok Min) *</label>
                    <input type="number" data-stepper name="min_stock" required min="0" value="5"
                        class="w-full px-3.5 py-2.5 bg-slate-900 border border-rose-500/40 rounded-xl text-xs text-rose-300 font-mono font-bold focus:outline-none focus:border-rose-400">
                </div>
            </div>

            <div>
                <label class="block text-xs font-semibold text-slate-300 uppercase mb-1">Kadar Alkohol (Khusus Miras, Opsional)</label>
                <input type="text" name="alcohol_content" placeholder="Contoh: 40% ABV"
                    class="w-full px-3.5 py-2.5 bg-slate-900 border border-slate-700 rounded-xl text-xs text-white focus:outline-none focus:border-purple-500">
            </div>

            <div>
                <label class="block text-xs font-semibold text-slate-300 uppercase mb-1">Deskripsi Produk</label>
                <textarea name="description" rows="2" placeholder="Keterangan rasa, pairing, atau penyajian..."
                    class="w-full px-3.5 py-2.5 bg-slate-900 border border-slate-700 rounded-xl text-xs text-white focus:outline-none focus:border-purple-500"></textarea>
            </div>

            <div class="pt-2">
                <button type="submit" class="w-full py-3 px-4 rounded-xl bg-gradient-to-r from-purple-600 to-indigo-600 hover:from-purple-500 hover:to-indigo-500 text-white font-extrabold text-sm shadow-xl shadow-purple-600/30 transition-all">
                    Simpan Produk Baru
                </button>
            </div>
        </form>
    </div>
</div>

<!-- 2. Edit Product Modal -->
<div id="editProductModal" class="fixed inset-0 bg-slate-950/80 backdrop-blur-sm z-50 hidden flex items-center justify-center p-4">
    <div class="bg-[#0c1222] border border-slate-800 rounded-3xl max-w-lg w-full p-6 sm:p-8 shadow-2xl relative max-h-[90vh] overflow-y-auto">
        <button type="button" onclick="closeModal('editProductModal')" class="absolute top-5 right-5 text-slate-400 hover:text-white">
            <i class="fa-solid fa-xmark text-lg"></i>
        </button>

        <div class="mb-5">
            <span class="text-[10px] font-bold uppercase tracking-wider text-amber-400">EDIT MASTER PRODUK</span>
            <h3 class="text-xl font-black text-white mt-0.5">Perbarui Data Produk</h3>
        </div>

        <form method="POST" id="editProductForm" class="space-y-4">
            @csrf
            @method('PUT')

            <div>
                <label class="block text-xs font-semibold text-slate-300 uppercase mb-1">Nama Produk *</label>
                <input type="text" name="name" id="edit_name" required
                    class="w-full px-3.5 py-2.5 bg-slate-900 border border-slate-700 rounded-xl text-sm text-white focus:outline-none focus:border-purple-500">
            </div>

            <div class="grid grid-cols-2 gap-3">
                <div>
                    <label class="block text-xs font-semibold text-slate-300 uppercase mb-1">Kategori *</label>
                    <select name="category" id="edit_category" required class="w-full px-3.5 py-2.5 bg-slate-900 border border-slate-700 rounded-xl text-xs text-white focus:outline-none focus:border-purple-500">
                        <option value="alcohol">Minuman Khusus / Miras</option>
                        <option value="food">Makanan / Snack</option>
                        <option value="softdrink">Softdrink / Minuman Ringan</option>
                    </select>
                </div>
                <div>
                    <label class="block text-xs font-semibold text-slate-300 uppercase mb-1">Satuan Kemasan *</label>
                    <select name="unit" id="edit_unit" required class="w-full px-3.5 py-2.5 bg-slate-900 border border-slate-700 rounded-xl text-xs text-white focus:outline-none focus:border-purple-500">
                        <option value="bottle">Bottle (Botol)</option>
                        <option value="bucket">Bucket (Ember Bir)</option>
                        <option value="tower">Tower (3 Liter)</option>
                        <option value="can">Can (Kaleng)</option>
                        <option value="glass">Glass (Gelas / Shot)</option>
                        <option value="portion">Portion (Porsi Makanan)</option>
                    </select>
                </div>
            </div>

            <div class="grid grid-cols-2 gap-3">
                <div>
                    <label class="block text-xs font-semibold text-slate-300 uppercase mb-1">Harga Beli / Modal (Rp) *</label>
                    <input type="number" name="purchase_price" id="edit_purchase_price" required min="0" step="500"
                        class="w-full px-3.5 py-2.5 bg-slate-900 border border-slate-700 rounded-xl text-xs text-white font-mono focus:outline-none focus:border-purple-500">
                </div>
                <div>
                    <label class="block text-xs font-semibold text-slate-300 uppercase mb-1">Harga Jual (Rp) *</label>
                    <input type="number" name="selling_price" id="edit_selling_price" required min="0" step="500"
                        class="w-full px-3.5 py-2.5 bg-slate-900 border border-slate-700 rounded-xl text-xs text-amber-400 font-mono font-bold focus:outline-none focus:border-purple-500">
                </div>
            </div>

            <div class="grid grid-cols-2 gap-3">
                <div>
                    <label class="block text-xs font-semibold text-slate-300 uppercase mb-1">Stok Fisik Saat Ini *</label>
                    <input type="number" data-stepper name="stock" id="edit_stock" required min="0"
                        class="w-full px-3.5 py-2.5 bg-slate-900 border border-slate-700 rounded-xl text-xs text-white font-mono focus:outline-none focus:border-purple-500">
                </div>
                <div>
                    <label class="block text-xs font-semibold text-rose-300 uppercase mb-1">Batas Minimum Alert (Stok Min) *</label>
                    <input type="number" data-stepper name="min_stock" id="edit_min_stock" required min="0"
                        class="w-full px-3.5 py-2.5 bg-slate-900 border border-rose-500/40 rounded-xl text-xs text-rose-300 font-mono font-bold focus:outline-none focus:border-rose-400">
                </div>
            </div>

            <div>
                <label class="block text-xs font-semibold text-slate-300 uppercase mb-1">Kadar Alkohol (Khusus Miras)</label>
                <input type="text" name="alcohol_content" id="edit_alcohol_content"
                    class="w-full px-3.5 py-2.5 bg-slate-900 border border-slate-700 rounded-xl text-xs text-white focus:outline-none focus:border-purple-500">
            </div>

            <div>
                <label class="block text-xs font-semibold text-slate-300 uppercase mb-1">Deskripsi Produk</label>
                <textarea name="description" id="edit_description" rows="2"
                    class="w-full px-3.5 py-2.5 bg-slate-900 border border-slate-700 rounded-xl text-xs text-white focus:outline-none focus:border-purple-500"></textarea>
            </div>

            <div class="pt-2">
                <button type="submit" class="w-full py-3 px-4 rounded-xl bg-gradient-to-r from-amber-500 to-amber-600 hover:from-amber-400 hover:to-amber-500 text-slate-950 font-black text-sm shadow-xl shadow-amber-500/20 transition-all">
                    Perbarui Data Produk
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

    function openEditModal(prod) {
        document.getElementById('editProductForm').action = "/owner/products/" + prod.id;
        document.getElementById('edit_name').value = prod.name;
        document.getElementById('edit_category').value = prod.category;
        document.getElementById('edit_unit').value = prod.unit;
        document.getElementById('edit_purchase_price').value = prod.purchase_price;
        document.getElementById('edit_selling_price').value = prod.selling_price;
        document.getElementById('edit_stock').value = prod.stock;
        document.getElementById('edit_min_stock').value = prod.min_stock;
        document.getElementById('edit_alcohol_content').value = prod.alcohol_content || '';
        document.getElementById('edit_description').value = prod.description || '';
        openModal('editProductModal');
    }
</script>
@endsection
