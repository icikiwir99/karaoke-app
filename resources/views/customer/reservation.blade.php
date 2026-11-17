@extends('layouts.app')

@section('title', 'Form Reservasi Online - Grand Karaoke')

@section('content')
<div class="max-w-5xl mx-auto px-4 sm:px-6 lg:px-8 py-12">
    <!-- Header -->
    <div class="text-center max-w-2xl mx-auto mb-10">
        <span class="text-xs font-bold uppercase tracking-wider text-purple-400">BOOKING ONLINE CEPAT & MUDAH</span>
        <h1 class="text-3xl sm:text-4xl font-black text-white mt-1">Formulir Reservasi Kamar Karaoke</h1>
        <p class="text-xs sm:text-sm text-slate-400 mt-2">
            Pesan kamar impian Anda, tentukan add-on pemandu lagu (LC), dan pre-order minuman sebelum tiba di lokasi.
        </p>
    </div>

    @if ($errors->any())
        <div class="mb-8 p-4 rounded-2xl bg-rose-500/10 border border-rose-500/30 text-rose-300 text-xs">
            <h4 class="font-bold mb-1">Perhatian! Mohon lengkapi formulir dengan benar:</h4>
            <ul class="list-disc list-inside space-y-1">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form method="POST" action="{{ route('customer.reservation.store') }}" id="reservation-form">
        @csrf

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
            <!-- Left & Center: Form Steps (2 Columns) -->
            <div class="lg:col-span-2 space-y-8">
                <!-- STEP 1: Data Pemesan -->
                <div class="bg-[#0c1222] border border-slate-800 rounded-2xl p-6 shadow-xl">
                    <h3 class="text-base font-bold text-white flex items-center mb-4">
                        <span class="w-7 h-7 rounded-lg bg-purple-600/30 border border-purple-500/40 text-purple-400 flex items-center justify-center text-xs font-black mr-2.5">1</span>
                        Informasi Kontak Tamu
                    </h3>
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-xs font-semibold text-slate-300 uppercase tracking-wider mb-1.5">Nama Lengkap Pemesan *</label>
                            <input type="text" name="customer_name" required value="{{ old('customer_name', auth()->user() ? auth()->user()->name : '') }}"
                                placeholder="Contoh: Hendra Kusuma"
                                class="w-full px-4 py-2.5 bg-slate-900 border border-slate-700/80 rounded-xl text-sm text-white focus:outline-none focus:border-purple-500">
                        </div>
                        <div>
                            <label class="block text-xs font-semibold text-slate-300 uppercase tracking-wider mb-1.5">No. WhatsApp Aktif *</label>
                            <input type="text" name="customer_phone" required value="{{ old('customer_phone', auth()->user() ? auth()->user()->phone : '') }}"
                                placeholder="Contoh: 081234567890"
                                class="w-full px-4 py-2.5 bg-slate-900 border border-slate-700/80 rounded-xl text-sm text-white focus:outline-none focus:border-purple-500">
                            <span class="text-[10px] text-slate-500 mt-1 block">Konfirmasi dan link WhatsApp otomatis akan dikirim ke nomor ini.</span>
                        </div>
                    </div>
                </div>

                <!-- STEP 2: Pilih Kamar & Waktu -->
                <div class="bg-[#0c1222] border border-slate-800 rounded-2xl p-6 shadow-xl">
                    <h3 class="text-base font-bold text-white flex items-center mb-4">
                        <span class="w-7 h-7 rounded-lg bg-purple-600/30 border border-purple-500/40 text-purple-400 flex items-center justify-center text-xs font-black mr-2.5">2</span>
                        Pilih Kamar & Jadwal
                    </h3>

                    <!-- Optional Bundling Package Selector -->
                    <div class="mb-5">
                        <label class="block text-xs font-semibold text-amber-400 uppercase tracking-wider mb-1.5">
                            <i class="fa-solid fa-gift mr-1"></i> Pilihan Paket Bundling (Opsional)
                        </label>
                        <select name="package_id" id="package-selector"
                            class="w-full px-4 py-2.5 bg-slate-900 border border-amber-500/40 rounded-xl text-sm text-amber-200 focus:outline-none focus:border-amber-400">
                            <option value="" data-price="0" data-room-type="">-- Tanpa Paket (Sewa Kamar Reguler) --</option>
                            @foreach($packages as $pkg)
                                <option value="{{ $pkg->id }}" data-price="{{ $pkg->price }}" data-room-type="{{ $pkg->room_type }}" {{ (string)$selectedPackageId === (string)$pkg->id ? 'selected' : '' }}>
                                    {{ $pkg->name }} - {{ $pkg->formatted_price }} (Termasuk Room {{ strtoupper($pkg->room_type) }} {{ $pkg->room_hours }} Jam {{ $pkg->include_lc_qty > 0 ? '+ ' . $pkg->include_lc_qty . ' LC' : '' }})
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <!-- Room Selection -->
                    <div class="mb-5">
                        <label class="block text-xs font-semibold text-slate-300 uppercase tracking-wider mb-1.5">Pilih Kamar Karaoke *</label>
                        <select name="room_id" id="room-selector" required
                            class="w-full px-4 py-2.5 bg-slate-900 border border-slate-700/80 rounded-xl text-sm text-white focus:outline-none focus:border-purple-500">
                            <option value="" data-price="0">-- Pilih Ruangan Kamar --</option>
                            @foreach($rooms as $room)
                                <option value="{{ $room->id }}" data-price="{{ $room->price_per_hour }}" data-type="{{ $room->type }}" {{ (string)$selectedRoomId === (string)$room->id ? 'selected' : '' }}>
                                    {{ $room->room_number }} - {{ $room->name }} ({{ ucfirst($room->type) }}, Max {{ $room->capacity }} Org) - {{ $room->formatted_price }}/Jam
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <!-- Date, Time & Duration -->
                    <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                        <div>
                            <label class="block text-xs font-semibold text-slate-300 uppercase tracking-wider mb-1.5">Tanggal Booking *</label>
                            <input type="date" name="start_date" id="start_date" required min="{{ date('Y-m-d') }}" value="{{ date('Y-m-d') }}"
                                class="w-full px-3.5 py-2.5 bg-slate-900 border border-slate-700/80 rounded-xl text-sm text-white focus:outline-none focus:border-purple-500">
                        </div>
                        <div>
                            <label class="block text-xs font-semibold text-slate-300 uppercase tracking-wider mb-1.5">Jam Mulai *</label>
                            <select name="start_hour" id="start_hour" required
                                class="w-full px-3.5 py-2.5 bg-slate-900 border border-slate-700/80 rounded-xl text-sm text-white focus:outline-none focus:border-purple-500">
                                @for($h = 13; $h <= 27; $h++)
                                    @php
                                        $displayH = $h >= 24 ? $h - 24 : $h;
                                        $timeStr = sprintf('%02d:00', $displayH);
                                    @endphp
                                    <option value="{{ $timeStr }}" {{ $timeStr === '19:00' ? 'selected' : '' }}>{{ $timeStr }} WIB</option>
                                    @php
                                        $timeStrHalf = sprintf('%02d:30', $displayH);
                                    @endphp
                                    <option value="{{ $timeStrHalf }}">{{ $timeStrHalf }} WIB</option>
                                @endfor
                            </select>
                        </div>
                        <div>
                            <label class="block text-xs font-semibold text-slate-300 uppercase tracking-wider mb-1.5">Durasi Sewa *</label>
                            <select name="duration_hours" id="duration-selector" required
                                class="w-full px-3.5 py-2.5 bg-slate-900 border border-slate-700/80 rounded-xl text-sm text-white focus:outline-none focus:border-purple-500">
                                <option value="1">1 Jam</option>
                                <option value="2" selected>2 Jam</option>
                                <option value="3">3 Jam</option>
                                <option value="4">4 Jam</option>
                                <option value="5">5 Jam</option>
                                <option value="6">6 Jam</option>
                            </select>
                        </div>
                    </div>
                </div>

                <!-- STEP 3: Add-on Pemandu Lagu (LC) -->
                <div class="bg-[#0c1222] border border-slate-800 rounded-2xl p-6 shadow-xl">
                    <div class="flex items-center justify-between mb-4">
                        <h3 class="text-base font-bold text-white flex items-center">
                            <span class="w-7 h-7 rounded-lg bg-pink-600/30 border border-pink-500/40 text-pink-400 flex items-center justify-center text-xs font-black mr-2.5">3</span>
                            Opsi Add-on Pemandu Lagu (LC)
                        </h3>
                        <label class="inline-flex items-center cursor-pointer">
                            <input type="checkbox" name="include_lc" id="include-lc-toggle" value="1" class="sr-only peer">
                            <div class="w-11 h-6 bg-slate-800 peer-focus:outline-none rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-pink-600"></div>
                            <span class="ml-2.5 text-xs font-bold text-pink-300">Gunakan LC</span>
                        </label>
                    </div>

                    <div id="lc-options-container" class="hidden space-y-4 pt-4 border-t border-slate-800">
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                            <div>
                                <label class="block text-xs font-semibold text-slate-300 uppercase tracking-wider mb-1.5">Jumlah Pemandu Lagu (Orang)</label>
                                <select name="lc_qty" id="lc-qty" class="w-full px-3.5 py-2.5 bg-slate-900 border border-slate-700 rounded-xl text-sm text-white focus:outline-none focus:border-pink-500">
                                    <option value="1" selected>1 Orang</option>
                                    <option value="2">2 Orang</option>
                                    <option value="3">3 Orang</option>
                                    <option value="4">4 Orang</option>
                                </select>
                            </div>
                            <div>
                                <label class="block text-xs font-semibold text-slate-300 uppercase tracking-wider mb-1.5">Durasi Pemandu Lagu (Jam)</label>
                                <select name="lc_hours" id="lc-hours" class="w-full px-3.5 py-2.5 bg-slate-900 border border-slate-700 rounded-xl text-sm text-white focus:outline-none focus:border-pink-500">
                                    <option value="1">1 Jam</option>
                                    <option value="2" selected>2 Jam</option>
                                    <option value="3">3 Jam</option>
                                    <option value="4">4 Jam</option>
                                </select>
                            </div>
                        </div>

                        <div>
                            <span class="text-xs font-semibold text-slate-300 uppercase tracking-wider block mb-2">Request Personel LC Favorit (Opsional):</span>
                            <div class="grid grid-cols-2 sm:grid-cols-4 gap-2.5">
                                @foreach($companions as $comp)
                                    <label class="p-2.5 rounded-xl border border-slate-800 bg-slate-900/60 hover:border-pink-500/40 cursor-pointer flex items-center space-x-2 text-xs">
                                        <input type="checkbox" name="selected_companions[]" value="{{ $comp->id }}" class="rounded bg-slate-800 border-slate-700 text-pink-600 focus:ring-0">
                                        <div>
                                            <span class="font-bold text-white block">{{ $comp->stage_name }}</span>
                                            <span class="text-[10px] text-pink-400 font-mono">Rp {{ number_format($comp->price_per_hour, 0, ',', '.') }}/Jam</span>
                                        </div>
                                    </label>
                                @endforeach
                            </div>
                        </div>
                    </div>
                </div>

                <!-- STEP 4: Pre-Order Minuman Khusus & F&B -->
                <div class="bg-[#0c1222] border border-slate-800 rounded-2xl p-6 shadow-xl">
                    <h3 class="text-base font-bold text-white flex items-center mb-2">
                        <span class="w-7 h-7 rounded-lg bg-amber-600/30 border border-amber-500/40 text-amber-400 flex items-center justify-center text-xs font-black mr-2.5">4</span>
                        Pre-Order Minuman / Miras & F&B (Opsional)
                    </h3>
                    <p class="text-xs text-slate-400 mb-4 leading-relaxed">
                        Pesan minuman dan snack di muka agar dapat disiapkan lebih awal dan siap santap begitu Anda tiba di room.
                    </p>

                    <div class="space-y-4 max-h-72 overflow-y-auto pr-2">
                        @if(isset($products['alcohol']))
                            <div>
                                <span class="text-[10px] font-bold uppercase tracking-wider text-amber-400 block mb-2">Minuman Khusus & Miras:</span>
                                <div class="grid grid-cols-1 sm:grid-cols-2 gap-2.5">
                                    @foreach($products['alcohol'] as $prod)
                                        <div class="p-2.5 bg-slate-900/80 border border-slate-800 rounded-xl flex items-center justify-between text-xs">
                                            <div class="overflow-hidden pr-2">
                                                <p class="font-bold text-slate-200 truncate">{{ $prod->name }}</p>
                                                <span class="text-[10px] text-amber-400 font-mono font-semibold">{{ $prod->formatted_price }}</span>
                                            </div>
                                            <div class="flex items-center space-x-1 shrink-0">
                                                <input type="number" min="0" max="20" name="preorder_products[{{ $prod->id }}]" value="0"
                                                    data-price="{{ $prod->selling_price }}"
                                                    class="preorder-input w-14 px-2 py-1 bg-slate-950 border border-slate-700 rounded text-center text-xs text-white focus:outline-none focus:border-amber-400 font-mono">
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                            </div>
                        @endif

                        @if(isset($products['food']))
                            <div class="pt-2">
                                <span class="text-[10px] font-bold uppercase tracking-wider text-purple-400 block mb-2">Makanan & Snack:</span>
                                <div class="grid grid-cols-1 sm:grid-cols-2 gap-2.5">
                                    @foreach($products['food'] as $prod)
                                        <div class="p-2.5 bg-slate-900/80 border border-slate-800 rounded-xl flex items-center justify-between text-xs">
                                            <div class="overflow-hidden pr-2">
                                                <p class="font-bold text-slate-200 truncate">{{ $prod->name }}</p>
                                                <span class="text-[10px] text-purple-400 font-mono font-semibold">{{ $prod->formatted_price }}</span>
                                            </div>
                                            <div class="flex items-center space-x-1 shrink-0">
                                                <input type="number" min="0" max="20" name="preorder_products[{{ $prod->id }}]" value="0"
                                                    data-price="{{ $prod->selling_price }}"
                                                    class="preorder-input w-14 px-2 py-1 bg-slate-950 border border-slate-700 rounded text-center text-xs text-white focus:outline-none focus:border-purple-400 font-mono">
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                            </div>
                        @endif
                    </div>
                </div>

                <!-- STEP 5: Catatan Tambahan -->
                <div class="bg-[#0c1222] border border-slate-800 rounded-2xl p-6 shadow-xl">
                    <label class="block text-xs font-semibold text-slate-300 uppercase tracking-wider mb-2">Catatan Khusus / Request Tambahan</label>
                    <textarea name="notes" rows="2" placeholder="Contoh: Tolong siapkan gelas es ekstra dan 2 mic cadangan..."
                        class="w-full px-4 py-2.5 bg-slate-900 border border-slate-700/80 rounded-xl text-xs text-white focus:outline-none focus:border-purple-500"></textarea>
                </div>
            </div>

            <!-- Right Column: Sticky Summary & Checkout Card -->
            <div class="lg:col-span-1">
                <div class="sticky top-28 bg-[#0c1222] border border-purple-500/30 rounded-2xl p-6 shadow-2xl">
                    <h3 class="text-base font-bold text-white mb-4 pb-3 border-b border-slate-800 flex items-center justify-between">
                        <span>Ringkasan Estimasi</span>
                        <i class="fa-solid fa-receipt text-purple-400"></i>
                    </h3>

                    <div class="space-y-3 text-xs mb-6">
                        <div class="flex items-center justify-between text-slate-300">
                            <span>Sewa Kamar / Paket</span>
                            <span id="summary-room-cost" class="font-mono font-bold text-white">Rp 0</span>
                        </div>
                        <div class="flex items-center justify-between text-slate-300">
                            <span>Add-on Pemandu Lagu (LC)</span>
                            <span id="summary-lc-cost" class="font-mono font-bold text-pink-400">Rp 0</span>
                        </div>
                        <div class="flex items-center justify-between text-slate-300">
                            <span>Pre-Order Minuman & F&B</span>
                            <span id="summary-fnb-cost" class="font-mono font-bold text-amber-400">Rp 0</span>
                        </div>

                        <div class="pt-4 border-t border-slate-800 flex items-center justify-between">
                            <span class="text-sm font-extrabold text-white">Total Estimasi</span>
                            <span id="summary-grand-total" class="text-xl font-black text-amber-400 font-mono">Rp 0</span>
                        </div>
                    </div>

                    <div class="p-3.5 rounded-xl bg-purple-950/40 border border-purple-500/20 text-[11px] text-purple-300 mb-6 space-y-1">
                        <p class="font-bold flex items-center"><i class="fa-brands fa-whatsapp text-emerald-400 text-sm mr-1.5"></i> Integrasi Otomatis WhatsApp</p>
                        <p class="leading-relaxed">Setelah menekan tombol di bawah, rincian booking Anda akan terformat rapi dan siap dikirimkan ke kasir via WhatsApp!</p>
                    </div>

                    <button type="submit" class="w-full py-3.5 px-4 rounded-xl bg-gradient-to-r from-purple-600 to-indigo-600 hover:from-purple-500 hover:to-indigo-500 text-white font-black text-sm shadow-xl shadow-purple-600/30 transition-all hover:scale-[1.02] border border-purple-400/40">
                        <i class="fa-solid fa-paper-plane mr-2"></i> Kirim Reservasi Sekarang
                    </button>

                    <span class="block text-center text-[10px] text-slate-500 mt-3">Tidak dipungut biaya booking online. Pembayaran dilakukan di kasir saat tiba.</span>
                </div>
            </div>
        </div>
    </form>
</div>

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const packageSelector = document.getElementById('package-selector');
        const roomSelector = document.getElementById('room-selector');
        const durationSelector = document.getElementById('duration-selector');
        const lcToggle = document.getElementById('include-lc-toggle');
        const lcContainer = document.getElementById('lc-options-container');
        const lcQty = document.getElementById('lc-qty');
        const lcHours = document.getElementById('lc-hours');
        const preorderInputs = document.querySelectorAll('.preorder-input');

        const summaryRoom = document.getElementById('summary-room-cost');
        const summaryLc = document.getElementById('summary-lc-cost');
        const summaryFnb = document.getElementById('summary-fnb-cost');
        const summaryGrand = document.getElementById('summary-grand-total');

        function formatRp(val) {
            return 'Rp ' + Math.round(val).toLocaleString('id-ID');
        }

        function calculateTotal() {
            let roomCost = 0;
            const packageId = packageSelector.value;
            const duration = parseInt(durationSelector.value) || 2;

            if (packageId) {
                const opt = packageSelector.options[packageSelector.selectedIndex];
                roomCost = parseFloat(opt.getAttribute('data-price')) || 0;
            } else {
                const opt = roomSelector.options[roomSelector.selectedIndex];
                const pricePerHour = parseFloat(opt.getAttribute('data-price')) || 0;
                roomCost = pricePerHour * duration;
            }

            // LC cost calculation
            let lcCost = 0;
            if (lcToggle.checked && !packageId) {
                const qty = parseInt(lcQty.value) || 1;
                const hours = parseInt(lcHours.value) || duration;
                // Average LC rate Rp 175.000 / hr
                lcCost = 175000 * qty * hours;
            }

            // Preorder F&B calculation
            let fnbCost = 0;
            preorderInputs.forEach(input => {
                const qty = parseInt(input.value) || 0;
                const price = parseFloat(input.getAttribute('data-price')) || 0;
                fnbCost += (qty * price);
            });

            const grandTotal = roomCost + lcCost + fnbCost;

            summaryRoom.textContent = formatRp(roomCost);
            summaryLc.textContent = formatRp(lcCost);
            summaryFnb.textContent = formatRp(fnbCost);
            summaryGrand.textContent = formatRp(grandTotal);
        }

        // Toggle LC Container
        lcToggle.addEventListener('change', function() {
            if (this.checked) {
                lcContainer.classList.remove('hidden');
            } else {
                lcContainer.classList.add('hidden');
            }
            calculateTotal();
        });

        packageSelector.addEventListener('change', function() {
            const opt = this.options[this.selectedIndex];
            const roomType = opt.getAttribute('data-room-type');
            if (roomType) {
                // Auto-select corresponding room
                for (let i = 0; i < roomSelector.options.length; i++) {
                    if (roomSelector.options[i].getAttribute('data-type') === roomType) {
                        roomSelector.selectedIndex = i;
                        break;
                    }
                }
            }
            calculateTotal();
        });

        roomSelector.addEventListener('change', calculateTotal);
        durationSelector.addEventListener('change', calculateTotal);
        lcQty.addEventListener('change', calculateTotal);
        lcHours.addEventListener('change', calculateTotal);
        preorderInputs.forEach(inp => inp.addEventListener('input', calculateTotal));

        // Initial Calculation
        calculateTotal();
    });
</script>
@endpush
@endsection
