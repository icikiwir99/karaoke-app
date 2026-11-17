@extends('layouts.admin')

@section('title', 'Monitoring Status Room (Real-Time Grid)')
@section('header_title', 'Monitoring Status Room')
@section('header_subtitle', 'Pantau kamar secara visual, lakukan check-in walk-in, tambah F&B, dan kontrol LC')

@section('content')
<div class="space-y-6">
    <!-- Status Filter Legend & Stats Bar -->
    <div class="bg-[#0c1222] border border-slate-800 rounded-2xl p-4 flex flex-wrap items-center justify-between gap-4">
        <!-- Legend Buttons -->
        <div class="flex flex-wrap items-center gap-3 text-xs">
            <span class="text-slate-400 font-bold uppercase tracking-wider text-[10px] mr-2">Indikator Kamar:</span>
            <div class="flex items-center space-x-1.5 bg-emerald-500/10 border border-emerald-500/30 px-3 py-1.5 rounded-xl text-emerald-300 font-bold">
                <span class="w-2.5 h-2.5 rounded-full bg-emerald-400"></span>
                <span>Available ({{ $rooms->where('status', 'available')->count() }})</span>
            </div>
            <div class="flex items-center space-x-1.5 bg-rose-500/10 border border-rose-500/30 px-3 py-1.5 rounded-xl text-rose-300 font-bold">
                <span class="w-2.5 h-2.5 rounded-full bg-rose-400 animate-pulse"></span>
                <span>Occupied / In-Use ({{ $rooms->where('status', 'occupied')->count() }})</span>
            </div>
            <div class="flex items-center space-x-1.5 bg-cyan-500/10 border border-cyan-500/30 px-3 py-1.5 rounded-xl text-cyan-300 font-bold">
                <span class="w-2.5 h-2.5 rounded-full bg-cyan-400"></span>
                <span>Cleaning ({{ $rooms->where('status', 'cleaning')->count() }})</span>
            </div>
            <div class="flex items-center space-x-1.5 bg-slate-500/10 border border-slate-500/30 px-3 py-1.5 rounded-xl text-slate-400 font-bold">
                <span class="w-2.5 h-2.5 rounded-full bg-slate-400"></span>
                <span>Blocked / Hold ({{ $rooms->where('status', 'blocked')->count() }})</span>
            </div>
        </div>

        <!-- Fast Actions -->
        <div class="flex items-center space-x-2">
            <button onclick="location.reload()" class="p-2 bg-slate-800 hover:bg-slate-700 text-slate-300 rounded-xl text-xs font-bold transition-all" title="Refresh Status">
                <i class="fa-solid fa-arrows-rotate mr-1"></i> Refresh Grid
            </button>
        </div>
    </div>

    <!-- Visual Grid of Room Cards -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-6">
        @foreach($rooms as $room)
            @php
                $statusColors = [
                    'available' => [
                        'border' => 'border-emerald-500/40 hover:border-emerald-400',
                        'header_bg' => 'bg-emerald-950/40',
                        'badge_bg' => 'bg-emerald-500/20 text-emerald-300 border-emerald-500/30',
                        'badge_label' => 'AVAILABLE',
                        'glow' => 'hover:shadow-emerald-900/20',
                    ],
                    'occupied' => [
                        'border' => 'border-rose-500/50 hover:border-rose-400',
                        'header_bg' => 'bg-rose-950/40',
                        'badge_bg' => 'bg-rose-500/20 text-rose-300 border-rose-500/30',
                        'badge_label' => 'IN-ROOM / OCCUPIED',
                        'glow' => 'hover:shadow-rose-900/30',
                    ],
                    'cleaning' => [
                        'border' => 'border-cyan-500/40 hover:border-cyan-400',
                        'header_bg' => 'bg-cyan-950/40',
                        'badge_bg' => 'bg-cyan-500/20 text-cyan-300 border-cyan-500/30',
                        'badge_label' => 'CLEANING',
                        'glow' => 'hover:shadow-cyan-900/20',
                    ],
                    'blocked' => [
                        'border' => 'border-slate-700 hover:border-slate-600',
                        'header_bg' => 'bg-slate-900/60',
                        'badge_bg' => 'bg-slate-700/40 text-slate-300 border-slate-600',
                        'badge_label' => 'HOLD / BLOCKED',
                        'glow' => '',
                    ],
                ][$room->status];

                $activeBooking = $room->activeBooking;
            @endphp

            <div class="bg-[#0c1222] border {{ $statusColors['border'] }} rounded-2xl overflow-hidden shadow-xl transition-all hover:-translate-y-1 {{ $statusColors['glow'] }} flex flex-col justify-between">
                <div>
                    <!-- Room Header Bar -->
                    <div class="p-4 {{ $statusColors['header_bg'] }} border-b border-slate-800 flex items-center justify-between">
                        <div class="flex items-center space-x-3">
                            <div class="w-10 h-10 rounded-xl bg-slate-900 border border-slate-700 flex items-center justify-center font-mono font-black text-lg text-white">
                                {{ $room->room_number }}
                            </div>
                            <div>
                                <h3 class="text-sm font-bold text-white">{{ $room->name }}</h3>
                                <span class="text-[10px] uppercase font-bold text-slate-400">{{ ucfirst($room->type) }} (Max {{ $room->capacity }} Org)</span>
                            </div>
                        </div>
                        <span class="px-2 py-0.5 rounded text-[9px] font-black uppercase tracking-wider {{ $statusColors['badge_bg'] }} border">
                            {{ $statusColors['badge_label'] }}
                        </span>
                    </div>

                    <!-- Room Body Details -->
                    <div class="p-4 space-y-3 text-xs">
                        <!-- Rate -->
                        <div class="flex items-center justify-between text-slate-400 pb-2 border-b border-slate-800/80">
                            <span>Tarif Kamar</span>
                            <span class="font-mono font-bold text-amber-400">{{ $room->formatted_price }} / Jam</span>
                        </div>

                        @if($room->status === 'occupied' && $activeBooking)
                            <!-- Occupied Active Session Info -->
                            <div class="bg-rose-950/20 border border-rose-500/20 rounded-xl p-3 space-y-1.5">
                                <div class="flex justify-between">
                                    <span class="text-slate-400">Tamu:</span>
                                    <span class="font-bold text-white">{{ $activeBooking->customer_name }}</span>
                                </div>
                                <div class="flex justify-between">
                                    <span class="text-slate-400">Durasi:</span>
                                    <span class="font-bold text-slate-200">{{ $activeBooking->duration_hours }} Jam (Mulai {{ $activeBooking->start_time->format('H:i') }})</span>
                                </div>

                                <!-- LC in Room -->
                                @if($room->companions->count() > 0)
                                    <div class="pt-1.5 border-t border-rose-500/20 flex items-center justify-between text-pink-300">
                                        <span><i class="fa-solid fa-person-dress mr-1"></i> Pemandu Lagu (LC):</span>
                                        <span class="font-bold font-mono">
                                            {{ $room->companions->pluck('stage_name')->join(', ') }}
                                        </span>
                                    </div>
                                @endif
                            </div>

                        @elseif($room->status === 'cleaning')
                            <div class="bg-cyan-950/20 border border-cyan-500/20 rounded-xl p-3 text-center text-cyan-300 text-xs">
                                <i class="fa-solid fa-broom text-xl block mb-1"></i>
                                Kamar sedang dibersihkan dan disiapkan.
                            </div>

                        @elseif($room->status === 'blocked')
                            <div class="bg-slate-900/60 border border-slate-800 rounded-xl p-3 text-slate-400 text-xs">
                                <span class="font-bold text-slate-300 block mb-1"><i class="fa-solid fa-lock mr-1"></i> Owner Hold:</span>
                                <p class="italic text-[11px]">{{ $room->block_reason ?? 'Kamar ditahan untuk perawatan berkala / reservasi privat VIP.' }}</p>
                            </div>

                        @else
                            <!-- Available -->
                            <div class="bg-emerald-950/20 border border-emerald-500/20 rounded-xl p-3 text-center text-emerald-300 text-xs">
                                <i class="fa-solid fa-circle-check text-xl block mb-1"></i>
                                Kamar bersih dan siap digunakan.
                            </div>
                        @endif
                    </div>
                </div>

                <!-- Room Action Footer -->
                <div class="p-4 pt-0">
                    @if($room->status === 'available')
                        <button type="button" onclick="openWalkInModal({{ $room->id }}, '{{ $room->room_number }}', '{{ $room->formatted_price }}')"
                            class="w-full py-2.5 px-3 rounded-xl bg-gradient-to-r from-emerald-600 to-teal-600 hover:from-emerald-500 hover:to-teal-500 text-white font-extrabold text-xs shadow-lg shadow-emerald-600/20 flex items-center justify-center transition-all">
                            <i class="fa-solid fa-user-plus mr-1.5"></i> Check-in Walk-in
                        </button>

                    @elseif($room->status === 'occupied')
                        <div class="space-y-2">
                            <div class="grid grid-cols-3 gap-1.5">
                                <button type="button" onclick="openOrderModal({{ $room->id }}, '{{ $room->room_number }}')"
                                    class="py-1.5 px-2 bg-slate-800 hover:bg-slate-700 text-amber-300 rounded-lg text-[10px] font-bold transition-all text-center" title="Tambah Pesanan F&B">
                                    <i class="fa-solid fa-utensils block mb-0.5"></i> + F&B
                                </button>
                                <button type="button" onclick="openAssignLcModal({{ $room->id }}, '{{ $room->room_number }}')"
                                    class="py-1.5 px-2 bg-slate-800 hover:bg-slate-700 text-pink-300 rounded-lg text-[10px] font-bold transition-all text-center" title="Tugaskan LC">
                                    <i class="fa-solid fa-person-dress block mb-0.5"></i> + LC
                                </button>
                                <button type="button" onclick="openExtendModal({{ $room->id }}, '{{ $room->room_number }}')"
                                    class="py-1.5 px-2 bg-slate-800 hover:bg-slate-700 text-cyan-300 rounded-lg text-[10px] font-bold transition-all text-center" title="Perpanjang Durasi">
                                    <i class="fa-solid fa-clock-rotate-left block mb-0.5"></i> + Jam
                                </button>
                            </div>

                            <!-- Direct Checkout Button -->
                            <a href="{{ route('kasir.pos', ['room_id' => $room->id]) }}"
                                class="w-full py-2 px-3 rounded-xl bg-gradient-to-r from-purple-600 to-indigo-600 hover:from-purple-500 hover:to-indigo-500 text-white font-black text-xs text-center block transition-all shadow-lg shadow-purple-600/20">
                                <i class="fa-solid fa-cash-register mr-1.5"></i> Checkout & Selesaikan Billing
                            </a>
                        </div>

                    @elseif($room->status === 'cleaning')
                        <form method="POST" action="{{ route('kasir.rooms.status', $room->id) }}">
                            @csrf
                            <input type="hidden" name="status" value="available">
                            <button type="submit" class="w-full py-2.5 px-3 rounded-xl bg-cyan-600 hover:bg-cyan-500 text-white font-black text-xs flex items-center justify-center transition-all shadow-lg shadow-cyan-600/20">
                                <i class="fa-solid fa-check-double mr-1.5"></i> Selesai Bersih & Ready
                            </button>
                        </form>

                    @else
                        <!-- Blocked -->
                        <form method="POST" action="{{ route('kasir.rooms.status', $room->id) }}">
                            @csrf
                            <input type="hidden" name="status" value="available">
                            <button type="submit" class="w-full py-2.5 px-3 rounded-xl bg-slate-800 hover:bg-slate-700 text-slate-300 font-bold text-xs flex items-center justify-center transition-all">
                                <i class="fa-solid fa-lock-open mr-1.5"></i> Buka Hold Kamar
                            </button>
                        </form>
                    @endif
                </div>
            </div>
        @endforeach
    </div>
</div>

<!-- ==================== MODALS ==================== -->

<!-- 1. Walk-in Check-in Modal -->
<div id="walkInModal" class="fixed inset-0 bg-slate-950/80 backdrop-blur-sm z-50 hidden flex items-center justify-center p-4">
    <div class="bg-[#0c1222] border border-slate-800 rounded-3xl max-w-lg w-full p-6 sm:p-8 shadow-2xl relative">
        <button type="button" onclick="closeModal('walkInModal')" class="absolute top-5 right-5 text-slate-400 hover:text-white">
            <i class="fa-solid fa-xmark text-lg"></i>
        </button>

        <div class="mb-5">
            <span class="text-[10px] font-bold uppercase tracking-wider text-emerald-400">WALK-IN CHECK-IN</span>
            <h3 class="text-xl font-black text-white mt-0.5">Check-in Kamar <span id="walkInRoomNumber" class="text-amber-400 font-mono"></span></h3>
        </div>

        <form method="POST" action="{{ route('kasir.rooms.checkin') }}" class="space-y-4">
            @csrf
            <input type="hidden" name="room_id" id="walkInRoomId">

            <div>
                <label class="block text-xs font-semibold text-slate-300 uppercase mb-1">Nama Tamu / Rombongan *</label>
                <input type="text" name="customer_name" required placeholder="Contoh: Pak Chandra & Rekan"
                    class="w-full px-3.5 py-2.5 bg-slate-900 border border-slate-700 rounded-xl text-sm text-white focus:outline-none focus:border-emerald-500">
            </div>

            <div class="grid grid-cols-2 gap-3">
                <div>
                    <label class="block text-xs font-semibold text-slate-300 uppercase mb-1">No. HP / WA (Opsional)</label>
                    <input type="text" name="customer_phone" placeholder="08123456789"
                        class="w-full px-3.5 py-2.5 bg-slate-900 border border-slate-700 rounded-xl text-sm text-white focus:outline-none focus:border-emerald-500">
                </div>
                <div>
                    <label class="block text-xs font-semibold text-slate-300 uppercase mb-1">Durasi Sewa *</label>
                    <select name="duration_hours" class="w-full px-3.5 py-2.5 bg-slate-900 border border-slate-700 rounded-xl text-sm text-white focus:outline-none focus:border-emerald-500">
                        <option value="1">1 Jam</option>
                        <option value="2" selected>2 Jam</option>
                        <option value="3">3 Jam</option>
                        <option value="4">4 Jam</option>
                    </select>
                </div>
            </div>

            <div>
                <label class="block text-xs font-semibold text-pink-400 uppercase mb-1">Tugaskan Pemandu Lagu (LC) Langsung (Opsional)</label>
                <select name="companion_ids[]" multiple class="w-full px-3 py-2 bg-slate-900 border border-pink-500/30 rounded-xl text-xs text-white focus:outline-none focus:border-pink-500 h-24">
                    @foreach($availableCompanions as $comp)
                        <option value="{{ $comp->id }}">{{ $comp->stage_name }} (Rp {{ number_format($comp->price_per_hour, 0, ',', '.') }}/Jam)</option>
                    @endforeach
                </select>
                <span class="text-[10px] text-slate-500 mt-1 block">Tahan Ctrl/Cmd untuk memilih lebih dari 1 LC.</span>
            </div>

            <div class="pt-2">
                <button type="submit" class="w-full py-3 px-4 rounded-xl bg-gradient-to-r from-emerald-600 to-teal-600 hover:from-emerald-500 hover:to-teal-500 text-white font-extrabold text-sm shadow-xl shadow-emerald-600/30 transition-all">
                    Konfirmasi Check-in Kamar
                </button>
            </div>
        </form>
    </div>
</div>

<!-- 2. Order F&B Modal -->
<div id="orderModal" class="fixed inset-0 bg-slate-950/80 backdrop-blur-sm z-50 hidden flex items-center justify-center p-4">
    <div class="bg-[#0c1222] border border-slate-800 rounded-3xl max-w-md w-full p-6 sm:p-8 shadow-2xl relative">
        <button type="button" onclick="closeModal('orderModal')" class="absolute top-5 right-5 text-slate-400 hover:text-white">
            <i class="fa-solid fa-xmark text-lg"></i>
        </button>

        <div class="mb-5">
            <span class="text-[10px] font-bold uppercase tracking-wider text-amber-400">TAMBAH PESANAN KE KAMAR</span>
            <h3 class="text-xl font-black text-white mt-0.5">Order Menu Kamar <span id="orderRoomNumber" class="text-amber-400 font-mono"></span></h3>
        </div>

        <form method="POST" action="{{ route('kasir.rooms.order') }}" class="space-y-4">
            @csrf
            <input type="hidden" name="room_id" id="orderRoomId">

            <div>
                <label class="block text-xs font-semibold text-slate-300 uppercase mb-1">Pilih Produk Minuman / Makanan *</label>
                <select name="product_id" required class="w-full px-3.5 py-2.5 bg-slate-900 border border-slate-700 rounded-xl text-xs text-white focus:outline-none focus:border-amber-400">
                    <option value="">-- Pilih Menu --</option>
                    @foreach($products as $category => $items)
                        <optgroup label="{{ strtoupper($category) }}">
                            @foreach($items as $item)
                                <option value="{{ $item->id }}">
                                    {{ $item->name }} - {{ $item->formatted_price }} (Stok: {{ $item->stock }})
                                </option>
                            @endforeach
                        </optgroup>
                    @endforeach
                </select>
            </div>

            <div>
                <label class="block text-xs font-semibold text-slate-300 uppercase mb-1">Jumlah Pesanan (Qty) *</label>
                <input type="number" name="quantity" value="1" min="1" max="50" required
                    class="w-full px-3.5 py-2.5 bg-slate-900 border border-slate-700 rounded-xl text-sm text-white focus:outline-none focus:border-amber-400 font-mono">
            </div>

            <div class="pt-2">
                <button type="submit" class="w-full py-3 px-4 rounded-xl bg-gradient-to-r from-amber-500 to-amber-600 hover:from-amber-400 hover:to-amber-500 text-slate-950 font-black text-sm shadow-xl shadow-amber-500/20 transition-all">
                    Kirim Pesanan ke Kamar
                </button>
            </div>
        </form>
    </div>
</div>

<!-- 3. Assign LC Modal -->
<div id="assignLcModal" class="fixed inset-0 bg-slate-950/80 backdrop-blur-sm z-50 hidden flex items-center justify-center p-4">
    <div class="bg-[#0c1222] border border-slate-800 rounded-3xl max-w-md w-full p-6 sm:p-8 shadow-2xl relative">
        <button type="button" onclick="closeModal('assignLcModal')" class="absolute top-5 right-5 text-slate-400 hover:text-white">
            <i class="fa-solid fa-xmark text-lg"></i>
        </button>

        <div class="mb-5">
            <span class="text-[10px] font-bold uppercase tracking-wider text-pink-400">TUGASKAN LC KE KAMAR</span>
            <h3 class="text-xl font-black text-white mt-0.5">Pemandu Lagu Kamar <span id="assignLcRoomNumber" class="text-amber-400 font-mono"></span></h3>
        </div>

        <form method="POST" action="{{ route('kasir.rooms.assignLc') }}" class="space-y-4">
            @csrf
            <input type="hidden" name="room_id" id="assignLcRoomId">

            <div>
                <label class="block text-xs font-semibold text-slate-300 uppercase mb-1">Pilih Personel LC (Status Standby) *</label>
                <select name="companion_id" required class="w-full px-3.5 py-2.5 bg-slate-900 border border-slate-700 rounded-xl text-xs text-white focus:outline-none focus:border-pink-500">
                    <option value="">-- Pilih Pemandu Lagu --</option>
                    @foreach($availableCompanions as $comp)
                        <option value="{{ $comp->id }}">
                            {{ $comp->stage_name }} - Rp {{ number_format($comp->price_per_hour, 0, ',', '.') }}/Jam (Rating: {{ $comp->rating }})
                        </option>
                    @endforeach
                </select>
            </div>

            <div>
                <label class="block text-xs font-semibold text-slate-300 uppercase mb-1">Durasi Bertugas (Jam) *</label>
                <select name="hours" class="w-full px-3.5 py-2.5 bg-slate-900 border border-slate-700 rounded-xl text-sm text-white focus:outline-none focus:border-pink-500">
                    <option value="1">1 Jam</option>
                    <option value="2" selected>2 Jam</option>
                    <option value="3">3 Jam</option>
                    <option value="4">4 Jam</option>
                </select>
            </div>

            <div class="pt-2">
                <button type="submit" class="w-full py-3 px-4 rounded-xl bg-gradient-to-r from-pink-600 to-rose-600 hover:from-pink-500 hover:to-rose-500 text-white font-black text-sm shadow-xl shadow-pink-600/20 transition-all">
                    Tugaskan LC Sekarang
                </button>
            </div>
        </form>
    </div>
</div>

<!-- 4. Extend Room Duration Modal -->
<div id="extendModal" class="fixed inset-0 bg-slate-950/80 backdrop-blur-sm z-50 hidden flex items-center justify-center p-4">
    <div class="bg-[#0c1222] border border-slate-800 rounded-3xl max-w-sm w-full p-6 shadow-2xl relative">
        <button type="button" onclick="closeModal('extendModal')" class="absolute top-5 right-5 text-slate-400 hover:text-white">
            <i class="fa-solid fa-xmark text-lg"></i>
        </button>

        <div class="mb-5">
            <span class="text-[10px] font-bold uppercase tracking-wider text-cyan-400">PERPANJANG WAKTU SEWA</span>
            <h3 class="text-xl font-black text-white mt-0.5">Tambah Jam Kamar <span id="extendRoomNumber" class="text-amber-400 font-mono"></span></h3>
        </div>

        <form method="POST" action="{{ route('kasir.rooms.extend') }}" class="space-y-4">
            @csrf
            <input type="hidden" name="room_id" id="extendRoomId">

            <div>
                <label class="block text-xs font-semibold text-slate-300 uppercase mb-1">Tambahan Durasi (Jam) *</label>
                <select name="extra_hours" class="w-full px-3.5 py-2.5 bg-slate-900 border border-slate-700 rounded-xl text-sm text-white focus:outline-none focus:border-cyan-500">
                    <option value="1" selected>+ 1 Jam</option>
                    <option value="2">+ 2 Jam</option>
                    <option value="3">+ 3 Jam</option>
                </select>
            </div>

            <div class="pt-2">
                <button type="submit" class="w-full py-3 px-4 rounded-xl bg-gradient-to-r from-cyan-600 to-blue-600 hover:from-cyan-500 hover:to-blue-500 text-white font-black text-sm shadow-xl shadow-cyan-600/20 transition-all">
                    Konfirmasi Tambah Waktu
                </button>
            </div>
        </form>
    </div>
</div>

<script>
    function openWalkInModal(roomId, roomNumber, price) {
        document.getElementById('walkInRoomId').value = roomId;
        document.getElementById('walkInRoomNumber').textContent = roomNumber;
        document.getElementById('walkInModal').classList.remove('hidden');
    }

    function openOrderModal(roomId, roomNumber) {
        document.getElementById('orderRoomId').value = roomId;
        document.getElementById('orderRoomNumber').textContent = roomNumber;
        document.getElementById('orderModal').classList.remove('hidden');
    }

    function openAssignLcModal(roomId, roomNumber) {
        document.getElementById('assignLcRoomId').value = roomId;
        document.getElementById('assignLcRoomNumber').textContent = roomNumber;
        document.getElementById('assignLcModal').classList.remove('hidden');
    }

    function openExtendModal(roomId, roomNumber) {
        document.getElementById('extendRoomId').value = roomId;
        document.getElementById('extendRoomNumber').textContent = roomNumber;
        document.getElementById('extendModal').classList.remove('hidden');
    }

    function closeModal(modalId) {
        document.getElementById(modalId).classList.add('hidden');
    }
</script>
@endsection
