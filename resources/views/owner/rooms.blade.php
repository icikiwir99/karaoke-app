@extends('layouts.admin')

@section('title', 'Master Data Kamar & Owner Manual Override - Owner Area')
@section('header_title', 'Master Data Kamar & Manual Override')
@section('header_subtitle', 'Pengaturan tarif sewa per jam, fasilitas kamar, dan fitur blokir (Hold Room) oleh Owner')

@section('content')
<div class="space-y-6">
    <!-- Action Bar -->
    <div class="bg-[#0c1222] border border-slate-800 rounded-2xl p-4 flex flex-wrap items-center justify-between gap-4">
        <div>
            <span class="text-xs font-bold text-slate-300">Total Kamar Terdaftar: <span class="text-amber-400 font-mono font-bold">{{ $rooms->count() }} Unit</span></span>
            <p class="text-[11px] text-slate-500">Owner memiliki privilese memblokir kamar untuk acara privat maupun maintenance.</p>
        </div>

        <button type="button" onclick="openModal('addRoomModal')"
            class="px-4 py-2 bg-gradient-to-r from-amber-500 to-amber-600 hover:from-amber-400 hover:to-amber-500 text-slate-950 text-xs font-black rounded-xl shadow-lg shadow-amber-500/20 transition-all">
            <i class="fa-solid fa-plus mr-1.5"></i> Tambah Kamar Baru
        </button>
    </div>

    <!-- Rooms Table -->
    <div class="bg-[#0c1222] border border-slate-800 rounded-2xl p-6 shadow-xl">
        <div class="overflow-x-auto">
            <table class="w-full text-left text-xs text-slate-300">
                <thead class="bg-slate-900/80 text-[10px] uppercase font-bold text-slate-400 border-b border-slate-800">
                    <tr>
                        <th class="py-3 px-4">No. Kamar</th>
                        <th class="py-3 px-4">Nama Kamar</th>
                        <th class="py-3 px-4">Tipe & Kapasitas</th>
                        <th class="py-3 px-4 text-right">Tarif / Jam</th>
                        <th class="py-3 px-4">Fasilitas Utama</th>
                        <th class="py-3 px-4 text-center">Status Kamar</th>
                        <th class="py-3 px-4 text-center">Aksi / Override Owner</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-800/60">
                    @foreach($rooms as $room)
                        <tr class="hover:bg-slate-900/40 transition-colors">
                            <td class="py-3.5 px-4 font-mono font-black text-amber-400 text-sm">{{ $room->room_number }}</td>
                            <td class="py-3.5 px-4 font-bold text-white">{{ $room->name }}</td>
                            <td class="py-3.5 px-4">
                                <span class="px-2 py-0.5 rounded text-[10px] uppercase font-bold bg-purple-500/20 text-purple-300 border border-purple-500/30">
                                    {{ $room->type }}
                                </span>
                                <span class="text-slate-400 text-[11px] block mt-0.5"><i class="fa-solid fa-users text-purple-400 mr-1"></i> Max {{ $room->capacity }} Orang</span>
                            </td>
                            <td class="py-3.5 px-4 text-right font-mono font-bold text-white">
                                {{ $room->formatted_price }}
                            </td>
                            <td class="py-3.5 px-4 max-w-xs">
                                <p class="text-[11px] text-slate-400 truncate">{{ $room->facilities }}</p>
                            </td>
                            <td class="py-3.5 px-4 text-center">
                                @if($room->status === 'available')
                                    <span class="px-2.5 py-1 rounded text-[10px] font-black uppercase tracking-wider bg-emerald-500/20 text-emerald-300 border border-emerald-500/30">
                                        Available
                                    </span>
                                @elseif($room->status === 'occupied')
                                    <span class="px-2.5 py-1 rounded text-[10px] font-black uppercase tracking-wider bg-rose-500/20 text-rose-300 border border-rose-500/30">
                                        Occupied
                                    </span>
                                @elseif($room->status === 'cleaning')
                                    <span class="px-2.5 py-1 rounded text-[10px] font-black uppercase tracking-wider bg-cyan-500/20 text-cyan-300 border border-cyan-500/30">
                                        Cleaning
                                    </span>
                                @else
                                    <span class="px-2.5 py-1 rounded text-[10px] font-black uppercase tracking-wider bg-slate-700/50 text-slate-300 border border-slate-600">
                                        Hold / Blocked
                                    </span>
                                @endif
                            </td>
                            <td class="py-3.5 px-4 text-center">
                                <div class="flex items-center justify-center space-x-2">
                                    <!-- Edit Button -->
                                    <button type="button" onclick="openEditRoomModal({{ json_encode($room) }})"
                                        class="p-1.5 bg-slate-800 hover:bg-slate-700 text-purple-400 rounded-lg text-xs transition-all" title="Edit Data Kamar">
                                        <i class="fa-solid fa-pen-to-square"></i>
                                    </button>

                                    <!-- Owner Privilege: Hold / Block Room Toggle -->
                                    @if($room->status !== 'blocked')
                                        <button type="button" onclick="openBlockModal({{ $room->id }}, '{{ $room->room_number }}')"
                                            class="px-2.5 py-1 bg-rose-950/60 hover:bg-rose-900 border border-rose-500/40 text-rose-300 rounded-lg text-[10px] font-black transition-all"
                                            title="Manual Override: Blokir / Tahan Kamar">
                                            <i class="fa-solid fa-lock mr-1"></i> Hold Room
                                        </button>
                                    @else
                                        <form method="POST" action="{{ route('owner.rooms.block', $room->id) }}">
                                            @csrf
                                            <input type="hidden" name="action" value="unblock">
                                            <button type="submit" class="px-2.5 py-1 bg-emerald-950/60 hover:bg-emerald-900 border border-emerald-500/40 text-emerald-300 rounded-lg text-[10px] font-black transition-all"
                                                title="Buka Kunci Kamar">
                                                <i class="fa-solid fa-lock-open mr-1"></i> Unblock
                                            </button>
                                        </form>
                                    @endif
                                </div>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- ==================== ROOM MODALS ==================== -->

<!-- 1. Add Room Modal -->
<div id="addRoomModal" class="fixed inset-0 bg-slate-950/80 backdrop-blur-sm z-50 hidden flex items-center justify-center p-4">
    <div class="bg-[#0c1222] border border-slate-800 rounded-3xl max-w-md w-full p-6 sm:p-8 shadow-2xl relative">
        <button type="button" onclick="closeModal('addRoomModal')" class="absolute top-5 right-5 text-slate-400 hover:text-white">
            <i class="fa-solid fa-xmark text-lg"></i>
        </button>

        <div class="mb-5">
            <span class="text-[10px] font-bold uppercase tracking-wider text-amber-400">MASTER KAMAR KARAOKE</span>
            <h3 class="text-xl font-black text-white mt-0.5">Tambah Kamar Baru</h3>
        </div>

        <form method="POST" action="{{ route('owner.rooms.store') }}" class="space-y-4">
            @csrf
            <div class="grid grid-cols-2 gap-3">
                <div>
                    <label class="block text-xs font-semibold text-slate-300 uppercase mb-1">Nomor Kamar *</label>
                    <input type="text" name="room_number" required placeholder="Contoh: VIP-03"
                        class="w-full px-3.5 py-2.5 bg-slate-900 border border-slate-700 rounded-xl text-xs text-white uppercase font-mono focus:outline-none focus:border-amber-400">
                </div>
                <div>
                    <label class="block text-xs font-semibold text-slate-300 uppercase mb-1">Nama Tema Kamar</label>
                    <input type="text" name="name" placeholder="Contoh: Golden Nebula"
                        class="w-full px-3.5 py-2.5 bg-slate-900 border border-slate-700 rounded-xl text-xs text-white focus:outline-none focus:border-amber-400">
                </div>
            </div>

            <div class="grid grid-cols-2 gap-3">
                <div>
                    <label class="block text-xs font-semibold text-slate-300 uppercase mb-1">Tipe Ruangan *</label>
                    <select name="type" required class="w-full px-3.5 py-2.5 bg-slate-900 border border-slate-700 rounded-xl text-xs text-white focus:outline-none focus:border-amber-400">
                        <option value="small">Small Room (2-4 org)</option>
                        <option value="medium">Medium Room (6-8 org)</option>
                        <option value="large">Large Room (12-16 org)</option>
                        <option value="vip">VIP Suite (20-25 org)</option>
                    </select>
                </div>
                <div>
                    <label class="block text-xs font-semibold text-slate-300 uppercase mb-1">Kapasitas (Orang) *</label>
                    <input type="number" data-stepper name="capacity" required min="1" value="8"
                        class="w-full px-3.5 py-2.5 bg-slate-900 border border-slate-700 rounded-xl text-xs text-white font-mono focus:outline-none focus:border-amber-400">
                </div>
            </div>

            <div>
                <label class="block text-xs font-semibold text-slate-300 uppercase mb-1">Tarif Sewa Per Jam (Rp) *</label>
                <input type="number" name="price_per_hour" required min="0" step="1000" placeholder="Contoh: 150000"
                    class="w-full px-3.5 py-2.5 bg-slate-900 border border-slate-700 rounded-xl text-xs text-amber-400 font-mono font-bold focus:outline-none focus:border-amber-400">
            </div>

            <div>
                <label class="block text-xs font-semibold text-slate-300 uppercase mb-1">Fasilitas (Pisahkan Koma)</label>
                <input type="text" name="facilities" placeholder="Contoh: 4K Smart TV, JBL Audio, 4 Wireless Mic, Disco Lighting"
                    class="w-full px-3.5 py-2.5 bg-slate-900 border border-slate-700 rounded-xl text-xs text-white focus:outline-none focus:border-amber-400">
            </div>

            <div>
                <label class="block text-xs font-semibold text-slate-300 uppercase mb-1">Deskripsi Ruangan</label>
                <textarea name="description" rows="2" placeholder="Keterangan tema interior kamar..."
                    class="w-full px-3.5 py-2.5 bg-slate-900 border border-slate-700 rounded-xl text-xs text-white focus:outline-none focus:border-amber-400"></textarea>
            </div>

            <div class="pt-2">
                <button type="submit" class="w-full py-3 px-4 rounded-xl bg-gradient-to-r from-amber-500 to-amber-600 hover:from-amber-400 hover:to-amber-500 text-slate-950 font-black text-sm shadow-xl shadow-amber-500/20 transition-all">
                    Simpan Kamar Baru
                </button>
            </div>
        </form>
    </div>
</div>

<!-- 2. Edit Room Modal -->
<div id="editRoomModal" class="fixed inset-0 bg-slate-950/80 backdrop-blur-sm z-50 hidden flex items-center justify-center p-4">
    <div class="bg-[#0c1222] border border-slate-800 rounded-3xl max-w-md w-full p-6 sm:p-8 shadow-2xl relative">
        <button type="button" onclick="closeModal('editRoomModal')" class="absolute top-5 right-5 text-slate-400 hover:text-white">
            <i class="fa-solid fa-xmark text-lg"></i>
        </button>

        <div class="mb-5">
            <span class="text-[10px] font-bold uppercase tracking-wider text-purple-400">EDIT MASTER KAMAR</span>
            <h3 class="text-xl font-black text-white mt-0.5">Perbarui Data Kamar</h3>
        </div>

        <form method="POST" id="editRoomForm" class="space-y-4">
            @csrf
            @method('PUT')

            <div class="grid grid-cols-2 gap-3">
                <div>
                    <label class="block text-xs font-semibold text-slate-300 uppercase mb-1">Nomor Kamar *</label>
                    <input type="text" name="room_number" id="edit_room_number" required
                        class="w-full px-3.5 py-2.5 bg-slate-900 border border-slate-700 rounded-xl text-xs text-white uppercase font-mono focus:outline-none focus:border-purple-500">
                </div>
                <div>
                    <label class="block text-xs font-semibold text-slate-300 uppercase mb-1">Nama Tema Kamar</label>
                    <input type="text" name="name" id="edit_room_name"
                        class="w-full px-3.5 py-2.5 bg-slate-900 border border-slate-700 rounded-xl text-xs text-white focus:outline-none focus:border-purple-500">
                </div>
            </div>

            <div class="grid grid-cols-2 gap-3">
                <div>
                    <label class="block text-xs font-semibold text-slate-300 uppercase mb-1">Tipe Ruangan *</label>
                    <select name="type" id="edit_room_type" required class="w-full px-3.5 py-2.5 bg-slate-900 border border-slate-700 rounded-xl text-xs text-white focus:outline-none focus:border-purple-500">
                        <option value="small">Small Room (2-4 org)</option>
                        <option value="medium">Medium Room (6-8 org)</option>
                        <option value="large">Large Room (12-16 org)</option>
                        <option value="vip">VIP Suite (20-25 org)</option>
                    </select>
                </div>
                <div>
                    <label class="block text-xs font-semibold text-slate-300 uppercase mb-1">Kapasitas (Orang) *</label>
                    <input type="number" data-stepper name="capacity" id="edit_room_capacity" required min="1"
                        class="w-full px-3.5 py-2.5 bg-slate-900 border border-slate-700 rounded-xl text-xs text-white font-mono focus:outline-none focus:border-purple-500">
                </div>
            </div>

            <div>
                <label class="block text-xs font-semibold text-slate-300 uppercase mb-1">Tarif Sewa Per Jam (Rp) *</label>
                <input type="number" name="price_per_hour" id="edit_room_price" required min="0" step="1000"
                    class="w-full px-3.5 py-2.5 bg-slate-900 border border-slate-700 rounded-xl text-xs text-amber-400 font-mono font-bold focus:outline-none focus:border-purple-500">
            </div>

            <div>
                <label class="block text-xs font-semibold text-slate-300 uppercase mb-1">Fasilitas (Pisahkan Koma)</label>
                <input type="text" name="facilities" id="edit_room_facilities"
                    class="w-full px-3.5 py-2.5 bg-slate-900 border border-slate-700 rounded-xl text-xs text-white focus:outline-none focus:border-purple-500">
            </div>

            <div>
                <label class="block text-xs font-semibold text-slate-300 uppercase mb-1">Deskripsi Ruangan</label>
                <textarea name="description" id="edit_room_description" rows="2"
                    class="w-full px-3.5 py-2.5 bg-slate-900 border border-slate-700 rounded-xl text-xs text-white focus:outline-none focus:border-purple-500"></textarea>
            </div>

            <div class="pt-2">
                <button type="submit" class="w-full py-3 px-4 rounded-xl bg-purple-600 hover:bg-purple-500 text-white font-extrabold text-sm shadow-xl shadow-purple-600/30 transition-all">
                    Perbarui Data Kamar
                </button>
            </div>
        </form>
    </div>
</div>

<!-- 3. Owner Privilege: Hold / Block Room Modal -->
<div id="blockModal" class="fixed inset-0 bg-slate-950/80 backdrop-blur-sm z-50 hidden flex items-center justify-center p-4">
    <div class="bg-[#0c1222] border border-slate-800 rounded-3xl max-w-md w-full p-6 sm:p-8 shadow-2xl relative">
        <button type="button" onclick="closeModal('blockModal')" class="absolute top-5 right-5 text-slate-400 hover:text-white">
            <i class="fa-solid fa-xmark text-lg"></i>
        </button>

        <div class="mb-5">
            <span class="text-[10px] font-bold uppercase tracking-wider text-rose-400">MANUAL OVERRIDE: OWNER PRIVILEGE</span>
            <h3 class="text-xl font-black text-white mt-0.5">Blokir / Tahan Kamar <span id="blockRoomNumber" class="text-amber-400 font-mono"></span></h3>
            <p class="text-xs text-slate-400 mt-1">Kamar yang di-hold tidak akan bisa di-booking oleh tamu online maupun kasir sampai dibuka kembali oleh Owner.</p>
        </div>

        <form method="POST" id="blockRoomForm" class="space-y-4">
            @csrf
            <input type="hidden" name="action" value="block">

            <div>
                <label class="block text-xs font-semibold text-slate-300 uppercase mb-1">Alasan Penahanan (Hold Reason) *</label>
                <textarea name="block_reason" rows="3" required placeholder="Contoh: Perawatan audio mixer & mic, atau Kamar dipesan khusus untuk acara pribadi Owner..."
                    class="w-full px-3.5 py-2.5 bg-slate-900 border border-slate-700 rounded-xl text-xs text-white focus:outline-none focus:border-rose-500"></textarea>
            </div>

            <div class="pt-2">
                <button type="submit" class="w-full py-3 px-4 rounded-xl bg-rose-600 hover:bg-rose-500 text-white font-extrabold text-sm shadow-xl shadow-rose-600/30 transition-all">
                    Konfirmasi Blokir Kamar
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

    function openEditRoomModal(room) {
        document.getElementById('editRoomForm').action = "/owner/rooms/" + room.id;
        document.getElementById('edit_room_number').value = room.room_number;
        document.getElementById('edit_room_name').value = room.name || '';
        document.getElementById('edit_room_type').value = room.type;
        document.getElementById('edit_room_capacity').value = room.capacity;
        document.getElementById('edit_room_price').value = room.price_per_hour;
        document.getElementById('edit_room_facilities').value = room.facilities || '';
        document.getElementById('edit_room_description').value = room.description || '';
        openModal('editRoomModal');
    }

    function openBlockModal(roomId, roomNumber) {
        document.getElementById('blockRoomForm').action = "/owner/rooms/" + roomId + "/block";
        document.getElementById('blockRoomNumber').textContent = roomNumber;
        openModal('blockModal');
    }
</script>
@endsection
