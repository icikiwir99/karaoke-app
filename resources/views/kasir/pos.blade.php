@extends('layouts.admin')

@section('title', 'POS Kasir & Transaksi Penjualan')
@section('header_title', 'Point of Sale (POS) Transaksi')
@section('header_subtitle', 'Pemrosesan billing terpadu (Kamar + F&B/Miras + Pemandu Lagu LC) secara real-time')

@section('content')
<div class="h-[calc(100vh-140px)] flex flex-col lg:flex-row gap-6 -m-2">
    <!-- LEFT PANEL: Catalog Selection (Tabs & Products) -->
    <div class="lg:w-7/12 flex flex-col h-full bg-[#0c1222] border border-slate-800 rounded-2xl overflow-hidden p-4 shadow-xl">
        <!-- Top Catalog Controls -->
        <div class="flex flex-col sm:flex-row items-center justify-between gap-3 mb-4 shrink-0">
            <!-- Category Tabs -->
            <div class="flex flex-wrap items-center gap-1.5 w-full sm:w-auto">
                <button type="button" onclick="filterCatalog('all')" class="cat-btn px-3 py-1.5 rounded-xl text-xs font-bold transition-all bg-purple-600 text-white" data-cat="all">
                    Semua
                </button>
                <button type="button" onclick="filterCatalog('alcohol')" class="cat-btn px-3 py-1.5 rounded-xl text-xs font-bold transition-all bg-slate-900 text-amber-300 hover:bg-slate-800 border border-slate-800" data-cat="alcohol">
                    <i class="fa-solid fa-wine-bottle mr-1 text-[11px]"></i> Miras / Spirits
                </button>
                <button type="button" onclick="filterCatalog('food')" class="cat-btn px-3 py-1.5 rounded-xl text-xs font-bold transition-all bg-slate-900 text-purple-300 hover:bg-slate-800 border border-slate-800" data-cat="food">
                    <i class="fa-solid fa-utensils mr-1 text-[11px]"></i> Makanan
                </button>
                <button type="button" onclick="filterCatalog('softdrink')" class="cat-btn px-3 py-1.5 rounded-xl text-xs font-bold transition-all bg-slate-900 text-cyan-300 hover:bg-slate-800 border border-slate-800" data-cat="softdrink">
                    <i class="fa-solid fa-glass-water mr-1 text-[11px]"></i> Minuman
                </button>
                <button type="button" onclick="filterCatalog('companion')" class="cat-btn px-3 py-1.5 rounded-xl text-xs font-bold transition-all bg-slate-900 text-pink-300 hover:bg-slate-800 border border-slate-800" data-cat="companion">
                    <i class="fa-solid fa-person-dress mr-1 text-[11px]"></i> Layanan LC
                </button>
            </div>

            <!-- Search Input -->
            <div class="relative w-full sm:w-48">
                <input type="text" id="search-input" onkeyup="searchCatalog()" placeholder="Cari menu / LC..."
                    class="w-full pl-8 pr-3 py-1.5 bg-slate-900 border border-slate-700 rounded-xl text-xs text-white placeholder-slate-500 focus:outline-none focus:border-purple-500">
                <span class="absolute inset-y-0 left-0 flex items-center pl-2.5 text-slate-500">
                    <i class="fa-solid fa-magnifying-glass text-xs"></i>
                </span>
            </div>
        </div>

        <!-- Catalog Items Grid -->
        <div id="catalog-container" class="flex-grow overflow-y-auto pr-1 grid grid-cols-2 sm:grid-cols-3 xl:grid-cols-4 gap-3 content-start">
            <!-- F&B & Alcohol Products -->
            @foreach($products as $prod)
                <div class="product-card bg-slate-900/80 border border-slate-800 hover:border-purple-500/50 rounded-xl p-3 flex flex-col justify-between cursor-pointer transition-all hover:scale-[1.02] group"
                    data-cat="{{ $prod->category }}"
                    data-name="{{ strtolower($prod->name) }}"
                    onclick="addToCart('fnb', {{ $prod->id }}, '{{ addslashes($prod->name) }}', {{ $prod->selling_price }}, {{ $prod->purchase_price }})">
                    <div>
                        <div class="flex items-center justify-between mb-1.5">
                            @if($prod->category === 'alcohol')
                                <span class="text-[9px] uppercase font-bold text-amber-400"><i class="fa-solid fa-wine-bottle mr-0.5"></i> Spirits</span>
                            @elseif($prod->category === 'food')
                                <span class="text-[9px] uppercase font-bold text-purple-400"><i class="fa-solid fa-burger mr-0.5"></i> Food</span>
                            @else
                                <span class="text-[9px] uppercase font-bold text-cyan-400"><i class="fa-solid fa-glass-water mr-0.5"></i> Softdrink</span>
                            @endif
                            <span class="text-[9px] text-slate-500 font-mono">Stok: {{ $prod->stock }}</span>
                        </div>
                        <h4 class="text-xs font-bold text-white group-hover:text-amber-300 transition-colors line-clamp-2 leading-tight">{{ $prod->name }}</h4>
                    </div>
                    <div class="mt-2 pt-2 border-t border-slate-800/80 flex items-center justify-between">
                        <span class="text-xs font-black text-amber-400 font-mono">{{ $prod->formatted_price }}</span>
                        <span class="w-6 h-6 rounded-lg bg-purple-600/20 text-purple-400 flex items-center justify-center text-xs group-hover:bg-purple-600 group-hover:text-white transition-all">
                            <i class="fa-solid fa-plus"></i>
                        </span>
                    </div>
                </div>
            @endforeach

            <!-- Companions (LC Services) -->
            @foreach($companions as $comp)
                <div class="product-card bg-slate-900/80 border border-pink-500/20 hover:border-pink-500/50 rounded-xl p-3 flex flex-col justify-between cursor-pointer transition-all hover:scale-[1.02] group"
                    data-cat="companion"
                    data-name="{{ strtolower($comp->stage_name) }} pemandu lagu lc"
                    onclick="addToCart('service', {{ $comp->id }}, 'Layanan LC: {{ addslashes($comp->stage_name) }}', {{ $comp->price_per_hour }}, {{ ($comp->price_per_hour * ($comp->commission_rate / 100)) }})">
                    <div>
                        <div class="flex items-center justify-between mb-1.5">
                            <span class="text-[9px] uppercase font-bold text-pink-400"><i class="fa-solid fa-person-dress mr-0.5"></i> Pemandu Lagu</span>
                            <span class="text-[9px] text-amber-400 font-bold"><i class="fa-solid fa-star text-[8px]"></i> {{ $comp->rating }}</span>
                        </div>
                        <h4 class="text-xs font-bold text-white group-hover:text-pink-300 transition-colors leading-tight">LC: {{ $comp->stage_name }}</h4>
                        <span class="text-[10px] text-slate-400 block mt-0.5">{{ $comp->skills ? Str::limit($comp->skills, 25) : 'Bilingual / Pop' }}</span>
                    </div>
                    <div class="mt-2 pt-2 border-t border-slate-800/80 flex items-center justify-between">
                        <span class="text-xs font-black text-pink-400 font-mono">{{ $comp->formatted_price }}/Jam</span>
                        <span class="w-6 h-6 rounded-lg bg-pink-600/20 text-pink-400 flex items-center justify-center text-xs group-hover:bg-pink-600 group-hover:text-white transition-all">
                            <i class="fa-solid fa-plus"></i>
                        </span>
                    </div>
                </div>
            @endforeach
        </div>
    </div>

    <!-- RIGHT PANEL: Billing Cart, Room Link, & Checkout Calculator -->
    <div class="lg:w-5/12 flex flex-col h-full bg-[#0c1222] border border-slate-800 rounded-2xl p-5 shadow-2xl overflow-hidden">
        <form method="POST" action="{{ route('kasir.checkout') }}" id="checkout-form" class="flex flex-col h-full">
            @csrf
            <input type="hidden" name="booking_id" id="hidden-booking-id" value="{{ $activeBooking ? $activeBooking->id : '' }}">
            <input type="hidden" name="room_id" id="hidden-room-id" value="{{ $activeBooking && $activeBooking->room ? $activeBooking->room->id : '' }}">

            <!-- Header & Active Room Selector -->
            <div class="pb-3 border-b border-slate-800 shrink-0">
                <div class="flex items-center justify-between mb-2">
                    <h3 class="text-sm font-black text-white flex items-center">
                        <i class="fa-solid fa-receipt text-purple-400 mr-2"></i> Rincian Tagihan Billing
                    </h3>
                    <button type="button" onclick="clearCart()" class="text-[10px] text-rose-400 hover:underline font-semibold">
                        <i class="fa-solid fa-trash mr-1"></i> Kosongkan
                    </button>
                </div>

                <!-- Load Room Session Dropdown -->
                <div class="grid grid-cols-2 gap-2 text-xs">
                    <div>
                        <label class="block text-[10px] font-bold text-slate-400 uppercase mb-0.5">Pilih Kamar Tamu</label>
                        <select id="room-picker" onchange="changeActiveRoom(this)" class="w-full px-2.5 py-1.5 bg-slate-900 border border-slate-700 rounded-lg text-xs text-white focus:outline-none focus:border-purple-500">
                            <option value="">-- Tamu Walk-in / Bar --</option>
                            @foreach($occupiedRooms as $occRoom)
                                <option value="{{ $occRoom->id }}" {{ ($activeBooking && $activeBooking->room_id === $occRoom->id) ? 'selected' : '' }}>
                                    Room {{ $occRoom->room_number }} (Occupied)
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label class="block text-[10px] font-bold text-slate-400 uppercase mb-0.5">Nama Customer *</label>
                        <input type="text" name="customer_name" id="customer_name" required value="{{ $activeBooking ? $activeBooking->customer_name : 'Tamu Walk-in' }}"
                            class="w-full px-2.5 py-1.5 bg-slate-900 border border-slate-700 rounded-lg text-xs text-white focus:outline-none focus:border-purple-500">
                    </div>
                </div>
            </div>

            <!-- Scrollable Cart Item List -->
            <div class="flex-grow overflow-y-auto py-3 space-y-2 pr-1" id="cart-container">
                <!-- Cart items rendered here via JS -->
            </div>

            <!-- Billing Totals Breakdown -->
            <div class="pt-3 border-t border-slate-800 shrink-0 space-y-2 text-xs">
                <div class="flex justify-between text-slate-400 text-[11px]">
                    <span>Total Sewa Room:</span>
                    <span id="label-total-room" class="font-mono text-slate-200">Rp 0</span>
                    <input type="hidden" name="total_room" id="input-total-room" value="0">
                </div>
                <div class="flex justify-between text-slate-400 text-[11px]">
                    <span>Total F&B & Minuman:</span>
                    <span id="label-total-fnb" class="font-mono text-slate-200">Rp 0</span>
                    <input type="hidden" name="total_fnb" id="input-total-fnb" value="0">
                </div>
                <div class="flex justify-between text-slate-400 text-[11px]">
                    <span>Total Layanan LC:</span>
                    <span id="label-total-service" class="font-mono text-slate-200">Rp 0</span>
                    <input type="hidden" name="total_service" id="input-total-service" value="0">
                </div>

                <!-- Discount Input -->
                <div class="flex items-center justify-between pt-1">
                    <span class="text-slate-400 text-[11px]">Potongan Diskon (Rp):</span>
                    <input type="number" name="discount" id="input-discount" value="0" min="0" oninput="recalcPayment()"
                        class="w-28 px-2 py-1 bg-slate-900 border border-slate-700 rounded text-right text-xs text-white font-mono focus:outline-none focus:border-purple-500">
                </div>

                <!-- Grand Total Display -->
                <div class="p-2.5 rounded-xl bg-purple-950/40 border border-purple-500/30 flex items-center justify-between">
                    <span class="text-xs font-extrabold text-white uppercase">Grand Total:</span>
                    <span id="label-grand-total" class="text-lg font-black text-amber-400 font-mono">Rp 0</span>
                    <input type="hidden" name="grand_total" id="input-grand-total" value="0">
                </div>

                <!-- Payment Method Selector -->
                <div>
                    <label class="block text-[10px] font-bold text-slate-400 uppercase mb-1">Metode Pembayaran</label>
                    <div class="grid grid-cols-4 gap-1.5">
                        <button type="button" onclick="setPayMethod('cash')" class="pay-btn p-2 rounded-lg bg-emerald-600 text-white font-bold text-[11px] text-center" data-method="cash">
                            <i class="fa-solid fa-money-bill-wave block text-xs mb-0.5"></i> Tunai
                        </button>
                        <button type="button" onclick="setPayMethod('qris')" class="pay-btn p-2 rounded-lg bg-slate-900 text-slate-300 font-bold text-[11px] text-center border border-slate-800" data-method="qris">
                            <i class="fa-solid fa-qrcode block text-xs mb-0.5"></i> QRIS
                        </button>
                        <button type="button" onclick="setPayMethod('debit')" class="pay-btn p-2 rounded-lg bg-slate-900 text-slate-300 font-bold text-[11px] text-center border border-slate-800" data-method="debit">
                            <i class="fa-solid fa-credit-card block text-xs mb-0.5"></i> EDC/Debit
                        </button>
                        <button type="button" onclick="setPayMethod('complimentary')" class="pay-btn p-2 rounded-lg bg-slate-900 text-amber-300 font-bold text-[10px] text-center border border-slate-800" data-method="complimentary">
                            <i class="fa-solid fa-crown block text-xs mb-0.5"></i> VIP Free
                        </button>
                    </div>
                    <input type="hidden" name="payment_method" id="input-pay-method" value="cash">
                    <input type="hidden" name="is_complimentary" id="input-is-complimentary" value="0">
                </div>

                <!-- Complimentary Reason if chosen -->
                <div id="complimentary-reason-box" class="hidden">
                    <label class="block text-[10px] font-bold text-amber-400 uppercase mb-1">Alasan Otorisasi Owner (100% Free)</label>
                    <input type="text" name="complimentary_reason" placeholder="Contoh: Tamu Relasi Bisnis Owner"
                        class="w-full px-2.5 py-1.5 bg-slate-900 border border-amber-500/40 rounded-lg text-xs text-amber-200 focus:outline-none">
                </div>

                <!-- Cash Paid & Change Calculator -->
                <div id="cash-calculator-box" class="space-y-1.5 pt-1">
                    <div class="flex items-center justify-between">
                        <span class="text-slate-400 text-[11px]">Uang Diterima:</span>
                        <input type="number" name="paid_amount" id="input-paid-amount" value="0" min="0" oninput="recalcPayment()"
                            class="w-32 px-2 py-1 bg-slate-900 border border-slate-700 rounded text-right text-xs text-white font-mono focus:outline-none focus:border-emerald-500 font-bold">
                    </div>

                    <!-- Quick Cash Buttons -->
                    <div class="flex items-center space-x-1 justify-end">
                        <button type="button" onclick="quickCashExact()" class="px-2 py-0.5 rounded bg-slate-800 text-[10px] text-slate-300 font-mono hover:bg-slate-700">Pas</button>
                        <button type="button" onclick="quickCash(50000)" class="px-2 py-0.5 rounded bg-slate-800 text-[10px] text-slate-300 font-mono hover:bg-slate-700">50k</button>
                        <button type="button" onclick="quickCash(100000)" class="px-2 py-0.5 rounded bg-slate-800 text-[10px] text-slate-300 font-mono hover:bg-slate-700">100k</button>
                        <button type="button" onclick="quickCash(200000)" class="px-2 py-0.5 rounded bg-slate-800 text-[10px] text-slate-300 font-mono hover:bg-slate-700">200k</button>
                        <button type="button" onclick="quickCash(500000)" class="px-2 py-0.5 rounded bg-slate-800 text-[10px] text-slate-300 font-mono hover:bg-slate-700">500k</button>
                    </div>

                    <div class="flex justify-between items-center pt-1 border-t border-slate-800 text-xs">
                        <span class="font-bold text-slate-300">Kembalian:</span>
                        <span id="label-change" class="font-mono font-black text-emerald-400 text-sm">Rp 0</span>
                        <input type="hidden" name="change_amount" id="input-change" value="0">
                    </div>
                </div>

                <!-- Checkout Button -->
                <button type="submit" id="submit-checkout-btn"
                    class="w-full mt-2 py-3 px-4 rounded-xl bg-gradient-to-r from-emerald-600 via-teal-600 to-emerald-600 hover:from-emerald-500 hover:to-teal-500 text-white font-black text-sm shadow-xl shadow-emerald-600/30 transition-all hover:scale-[1.01] border border-emerald-400/30 flex items-center justify-center">
                    <i class="fa-solid fa-print mr-2"></i> Proses Pembayaran & Cetak Struk
                </button>
            </div>
        </form>
    </div>
</div>

@push('scripts')
<script>
    let cart = [];

    // Prepopulate cart if loaded from an active booking
    @if($activeBooking)
        @if($activeBooking->package)
            cart.push({
                item_type: 'room',
                item_id: {{ $activeBooking->room->id }},
                item_name: 'Paket: {{ addslashes($activeBooking->package->name) }} (Room {{ $activeBooking->room->room_number }})',
                quantity: 1,
                price: {{ $activeBooking->package->price }},
                purchase_price: 0,
                subtotal: {{ $activeBooking->package->price }}
            });
        @else
            cart.push({
                item_type: 'room',
                item_id: {{ $activeBooking->room->id }},
                item_name: 'Sewa Room {{ $activeBooking->room->room_number }} ({{ ucfirst($activeBooking->room->type) }}) x {{ $activeBooking->duration_hours }} Jam',
                quantity: {{ $activeBooking->duration_hours }},
                price: {{ $activeBooking->room->price_per_hour }},
                purchase_price: 0,
                subtotal: {{ $activeBooking->room_charge }}
            });
        @endif

        // Add Active Booking Companions
        @foreach($activeBooking->companions as $bComp)
            @if($bComp->companion)
                cart.push({
                    item_type: 'service',
                    item_id: {{ $bComp->companion->id }},
                    item_name: 'Layanan LC: {{ addslashes($bComp->companion->stage_name) }} x {{ $bComp->hours }} Jam',
                    quantity: {{ $bComp->hours }},
                    price: {{ $bComp->price_per_hour }},
                    purchase_price: {{ ($bComp->price_per_hour * ($bComp->companion->commission_rate / 100)) }},
                    subtotal: {{ $bComp->subtotal }}
                });
            @endif
        @endforeach

        // Add Pre-orders
        @foreach($activeBooking->items as $bItem)
            @if($bItem->product)
                cart.push({
                    item_type: 'fnb',
                    item_id: {{ $bItem->product->id }},
                    item_name: '{{ addslashes($bItem->product->name) }}',
                    quantity: {{ $bItem->quantity }},
                    price: {{ $bItem->price }},
                    purchase_price: {{ $bItem->product->purchase_price }},
                    subtotal: {{ $bItem->subtotal }}
                });
            @endif
        @endforeach
    @endif

    function formatRp(val) {
        return 'Rp ' + Math.round(val).toLocaleString('id-ID');
    }

    function addToCart(type, id, name, price, purchasePrice = 0) {
        // If item exists in cart (for F&B or LC), increment quantity
        const existing = cart.find(i => i.item_type === type && i.item_id === id);
        if (existing) {
            existing.quantity += 1;
            existing.subtotal = existing.quantity * existing.price;
        } else {
            cart.push({
                item_type: type,
                item_id: id,
                item_name: name,
                quantity: 1,
                price: price,
                purchase_price: purchasePrice,
                subtotal: price
            });
        }
        renderCart();
    }

    function updateQty(index, change) {
        if (!cart[index]) return;
        cart[index].quantity += change;
        if (cart[index].quantity <= 0) {
            cart.splice(index, 1);
        } else {
            cart[index].subtotal = cart[index].quantity * cart[index].price;
        }
        renderCart();
    }

    function removeItem(index) {
        cart.splice(index, 1);
        renderCart();
    }

    function clearCart() {
        cart = [];
        renderCart();
    }

    function renderCart() {
        const container = document.getElementById('cart-container');
        container.innerHTML = '';

        if (cart.length === 0) {
            container.innerHTML = `
                <div class="h-full flex flex-col items-center justify-center text-slate-500 py-12">
                    <i class="fa-solid fa-cart-shopping text-3xl mb-2 opacity-40"></i>
                    <p class="text-xs">Belum ada item dalam tagihan.</p>
                    <span class="text-[10px] text-slate-600 mt-0.5">Pilih kamar atau klik menu di sebelah kiri.</span>
                </div>
            `;
        } else {
            cart.forEach((item, index) => {
                const el = document.createElement('div');
                el.className = 'bg-slate-900/90 border border-slate-800 p-2.5 rounded-xl flex items-center justify-between text-xs';
                el.innerHTML = `
                    <div class="overflow-hidden pr-2">
                        <span class="text-[9px] uppercase font-bold ${item.item_type === 'room' ? 'text-cyan-400' : (item.item_type === 'service' ? 'text-pink-400' : 'text-amber-400')} block">
                            ${item.item_type === 'room' ? 'Room Charge' : (item.item_type === 'service' ? 'Layanan LC' : 'F&B Menu')}
                        </span>
                        <p class="font-bold text-white truncate">${item.item_name}</p>
                        <span class="text-[10px] text-slate-400 font-mono">${formatRp(item.price)} x ${item.quantity}</span>

                        <!-- Hidden Inputs for Form Submission -->
                        <input type="hidden" name="items[${index}][item_type]" value="${item.item_type}">
                        <input type="hidden" name="items[${index}][item_id]" value="${item.item_id || ''}">
                        <input type="hidden" name="items[${index}][item_name]" value="${item.item_name}">
                        <input type="hidden" name="items[${index}][quantity]" value="${item.quantity}">
                        <input type="hidden" name="items[${index}][price]" value="${item.price}">
                        <input type="hidden" name="items[${index}][purchase_price]" value="${item.purchase_price || 0}">
                        <input type="hidden" name="items[${index}][subtotal]" value="${item.subtotal}">
                    </div>

                    <div class="flex items-center space-x-2 shrink-0">
                        <div class="flex items-center space-x-1 bg-slate-950 px-1 py-0.5 rounded border border-slate-700">
                            <button type="button" onclick="updateQty(${index}, -1)" class="w-5 h-5 flex items-center justify-center text-slate-400 hover:text-white">-</button>
                            <span class="w-6 text-center font-mono font-bold text-white text-[11px]">${item.quantity}</span>
                            <button type="button" onclick="updateQty(${index}, 1)" class="w-5 h-5 flex items-center justify-center text-slate-400 hover:text-white">+</button>
                        </div>
                        <span class="font-mono font-bold text-amber-400 text-xs w-20 text-right">${formatRp(item.subtotal)}</span>
                        <button type="button" onclick="removeItem(${index})" class="text-slate-500 hover:text-rose-400 px-1">
                            <i class="fa-solid fa-xmark text-xs"></i>
                        </button>
                    </div>
                `;
                container.appendChild(el);
            });
        }

        recalcPayment();
    }

    function recalcPayment() {
        let totalRoom = 0;
        let totalFnb = 0;
        let totalService = 0;

        cart.forEach(item => {
            if (item.item_type === 'room') totalRoom += item.subtotal;
            else if (item.item_type === 'service') totalService += item.subtotal;
            else totalFnb += item.subtotal;
        });

        const discount = parseFloat(document.getElementById('input-discount').value) || 0;
        let grandTotal = Math.max(0, (totalRoom + totalFnb + totalService) - discount);

        const isComplimentary = document.getElementById('input-is-complimentary').value === '1';
        if (isComplimentary) {
            grandTotal = 0;
        }

        document.getElementById('label-total-room').textContent = formatRp(totalRoom);
        document.getElementById('label-total-fnb').textContent = formatRp(totalFnb);
        document.getElementById('label-total-service').textContent = formatRp(totalService);

        document.getElementById('input-total-room').value = totalRoom;
        document.getElementById('input-total-fnb').value = totalFnb;
        document.getElementById('input-total-service').value = totalService;

        document.getElementById('label-grand-total').textContent = formatRp(grandTotal);
        document.getElementById('input-grand-total').value = grandTotal;

        // Calculate Change
        const paidAmount = parseFloat(document.getElementById('input-paid-amount').value) || 0;
        const change = Math.max(0, paidAmount - grandTotal);
        document.getElementById('label-change').textContent = formatRp(change);
        document.getElementById('input-change').value = change;
    }

    function setPayMethod(method) {
        document.getElementById('input-pay-method').value = method;
        document.querySelectorAll('.pay-btn').forEach(btn => {
            if (btn.getAttribute('data-method') === method) {
                btn.className = 'pay-btn p-2 rounded-lg bg-emerald-600 text-white font-bold text-[11px] text-center';
            } else {
                btn.className = 'pay-btn p-2 rounded-lg bg-slate-900 text-slate-300 font-bold text-[11px] text-center border border-slate-800';
            }
        });

        const isComplimentary = method === 'complimentary';
        document.getElementById('input-is-complimentary').value = isComplimentary ? '1' : '0';
        document.getElementById('complimentary-reason-box').classList.toggle('hidden', !isComplimentary);

        if (isComplimentary) {
            document.getElementById('input-discount').value = (parseFloat(document.getElementById('input-total-room').value) + parseFloat(document.getElementById('input-total-fnb').value) + parseFloat(document.getElementById('input-total-service').value));
            document.getElementById('input-paid-amount').value = 0;
        } else if (method === 'qris' || method === 'debit') {
            const grand = parseFloat(document.getElementById('input-grand-total').value) || 0;
            document.getElementById('input-paid-amount').value = grand;
        }

        recalcPayment();
    }

    function quickCashExact() {
        const grand = parseFloat(document.getElementById('input-grand-total').value) || 0;
        document.getElementById('input-paid-amount').value = grand;
        recalcPayment();
    }

    function quickCash(nominal) {
        document.getElementById('input-paid-amount').value = nominal;
        recalcPayment();
    }

    function filterCatalog(cat) {
        document.querySelectorAll('.cat-btn').forEach(b => {
            if (b.getAttribute('data-cat') === cat) {
                b.className = 'cat-btn px-3 py-1.5 rounded-xl text-xs font-bold transition-all bg-purple-600 text-white';
            } else {
                b.className = 'cat-btn px-3 py-1.5 rounded-xl text-xs font-bold transition-all bg-slate-900 text-slate-300 hover:bg-slate-800 border border-slate-800';
            }
        });

        document.querySelectorAll('.product-card').forEach(card => {
            if (cat === 'all' || card.getAttribute('data-cat') === cat) {
                card.classList.remove('hidden');
            } else {
                card.classList.add('hidden');
            }
        });
    }

    function searchCatalog() {
        const q = document.getElementById('search-input').value.toLowerCase();
        document.querySelectorAll('.product-card').forEach(card => {
            const name = card.getAttribute('data-name');
            if (name.includes(q)) {
                card.classList.remove('hidden');
            } else {
                card.classList.add('hidden');
            }
        });
    }

    function changeActiveRoom(select) {
        const roomId = select.value;
        if (roomId) {
            window.location.href = "{{ route('kasir.pos') }}?room_id=" + roomId;
        } else {
            window.location.href = "{{ route('kasir.pos') }}";
        }
    }

    // Initialize Cart
    renderCart();
</script>
@endpush
@endsection
