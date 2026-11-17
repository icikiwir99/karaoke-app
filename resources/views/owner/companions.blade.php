@extends('layouts.admin')

@section('title', 'Manajemen & Laporan Kinerja LC (Pemandu Lagu) - Owner Area')
@section('header_title', 'Manajemen & Kinerja Pemandu Lagu (LC)')
@section('header_subtitle', 'Master data personel LC, rekapitulasi jam kerja, dan kalkulasi komisi / bagi hasil')

@section('content')
<div class="space-y-6">
    <!-- Action Bar & Performance Summary -->
    <div class="bg-[#0c1222] border border-slate-800 rounded-2xl p-6 shadow-xl flex flex-col md:flex-row md:items-center justify-between gap-4">
        <div>
            @php
                $totalHours = $companions->sum('total_hours_served');
                $totalGrossLc = $companions->sum(function($c) { return $c->total_hours_served * $c->price_per_hour; });
                $totalLcCommission = $companions->sum(function($c) { return $c->total_hours_served * $c->price_per_hour * ($c->commission_rate / 100); });
                $totalHouseCut = $totalGrossLc - $totalLcCommission;
            @endphp
            <span class="text-xs font-bold text-pink-400 uppercase tracking-wider">REKAPITULASI BAGI HASIL LC</span>
            <div class="flex flex-wrap items-center gap-6 mt-2">
                <div>
                    <span class="text-[10px] text-slate-500 uppercase font-bold block">Total Jam Kerja</span>
                    <span class="text-xl font-black text-white font-mono">{{ number_format($totalHours, 1) }} Jam</span>
                </div>
                <div>
                    <span class="text-[10px] text-slate-500 uppercase font-bold block">Gross Penjualan Jasa</span>
                    <span class="text-xl font-black text-amber-400 font-mono">Rp {{ number_format($totalGrossLc, 0, ',', '.') }}</span>
                </div>
                <div>
                    <span class="text-[10px] text-slate-500 uppercase font-bold block">Hak Komisi Personel (LC)</span>
                    <span class="text-xl font-black text-emerald-400 font-mono">Rp {{ number_format($totalLcCommission, 0, ',', '.') }}</span>
                </div>
                <div>
                    <span class="text-[10px] text-slate-500 uppercase font-bold block">Net Share Outlet (Karaoke)</span>
                    <span class="text-xl font-black text-purple-400 font-mono">Rp {{ number_format($totalHouseCut, 0, ',', '.') }}</span>
                </div>
            </div>
        </div>

        <button type="button" onclick="openModal('addCompanionModal')"
            class="px-4 py-2 bg-gradient-to-r from-pink-600 to-rose-600 hover:from-pink-500 hover:to-rose-500 text-white text-xs font-black rounded-xl shadow-lg shadow-pink-600/25 transition-all shrink-0">
            <i class="fa-solid fa-plus mr-1.5"></i> Tambah Personel LC Baru
        </button>
    </div>

    <!-- LC Performance Table -->
    <div class="bg-[#0c1222] border border-slate-800 rounded-2xl p-6 shadow-xl">
        <h3 class="text-base font-bold text-white mb-4">Daftar Personel & Rekapitulasi Komisi</h3>

        <div class="overflow-x-auto">
            <table class="w-full text-left text-xs text-slate-300">
                <thead class="bg-slate-900/80 text-[10px] uppercase font-bold text-slate-400 border-b border-slate-800">
                    <tr>
                        <th class="py-3 px-4">Nama Panggung</th>
                        <th class="py-3 px-4">Nama Asli / Telp</th>
                        <th class="py-3 px-4 text-center">Status</th>
                        <th class="py-3 px-4 text-right">Tarif / Jam</th>
                        <th class="py-3 px-4 text-center">Bagi Hasil %</th>
                        <th class="py-3 px-4 text-center">Total Jam</th>
                        <th class="py-3 px-4 text-right">Gross Jasa</th>
                        <th class="py-3 px-4 text-right">Komisi LC (Hak LC)</th>
                        <th class="py-3 px-4 text-right">Bagian Outlet</th>
                        <th class="py-3 px-4 text-center">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-800/60">
                    @foreach($companions as $c)
                        @php
                            $gross = $c->total_hours_served * $c->price_per_hour;
                            $comm = $gross * ($c->commission_rate / 100);
                            $house = $gross - $comm;
                        @endphp
                        <tr class="hover:bg-slate-900/40 transition-colors">
                            <td class="py-3.5 px-4 font-bold text-white">
                                <span class="text-sm block">{{ $c->stage_name }}</span>
                                <span class="text-[10px] text-amber-400"><i class="fa-solid fa-star text-[8px]"></i> {{ $c->rating }}</span>
                            </td>
                            <td class="py-3.5 px-4">
                                <span class="text-slate-200 block">{{ $c->real_name ?? '-' }}</span>
                                <span class="text-[10px] text-slate-500 font-mono">{{ $c->phone ?? '-' }}</span>
                            </td>
                            <td class="py-3.5 px-4 text-center">
                                @if($c->status === 'standby')
                                    <span class="px-2 py-0.5 rounded text-[10px] uppercase font-bold bg-emerald-500/20 text-emerald-300 border border-emerald-500/30">Standby</span>
                                @elseif($c->status === 'in_room')
                                    <span class="px-2 py-0.5 rounded text-[10px] uppercase font-bold bg-pink-500/20 text-pink-300 border border-pink-500/30">In-Room</span>
                                @else
                                    <span class="px-2 py-0.5 rounded text-[10px] uppercase font-bold bg-slate-800 text-slate-400 border border-slate-700">Off</span>
                                @endif
                            </td>
                            <td class="py-3.5 px-4 text-right font-mono font-bold text-slate-300">
                                {{ $c->formatted_price }}
                            </td>
                            <td class="py-3.5 px-4 text-center font-mono font-bold text-pink-400">
                                {{ $c->commission_rate }}%
                            </td>
                            <td class="py-3.5 px-4 text-center font-mono font-bold text-white text-sm">
                                {{ number_format($c->total_hours_served, 1) }}
                            </td>
                            <td class="py-3.5 px-4 text-right font-mono font-bold text-amber-400">
                                Rp {{ number_format($gross, 0, ',', '.') }}
                            </td>
                            <td class="py-3.5 px-4 text-right font-mono font-bold text-emerald-400">
                                Rp {{ number_format($comm, 0, ',', '.') }}
                            </td>
                            <td class="py-3.5 px-4 text-right font-mono font-bold text-purple-400">
                                Rp {{ number_format($house, 0, ',', '.') }}
                            </td>
                            <td class="py-3.5 px-4 text-center">
                                <button type="button" onclick="openEditCompanionModal({{ json_encode($c) }})"
                                    class="p-1.5 bg-slate-800 hover:bg-slate-700 text-purple-400 rounded-lg text-xs transition-all" title="Edit Data">
                                    <i class="fa-solid fa-pen-to-square"></i>
                                </button>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- ==================== COMPANION MODALS ==================== -->

<!-- 1. Add Companion Modal -->
<div id="addCompanionModal" class="fixed inset-0 bg-slate-950/80 backdrop-blur-sm z-50 hidden flex items-center justify-center p-4">
    <div class="bg-[#0c1222] border border-slate-800 rounded-3xl max-w-md w-full p-6 sm:p-8 shadow-2xl relative">
        <button type="button" onclick="closeModal('addCompanionModal')" class="absolute top-5 right-5 text-slate-400 hover:text-white">
            <i class="fa-solid fa-xmark text-lg"></i>
        </button>

        <div class="mb-5">
            <span class="text-[10px] font-bold uppercase tracking-wider text-pink-400">MASTER PEMANDU LAGU</span>
            <h3 class="text-xl font-black text-white mt-0.5">Tambah Personel LC</h3>
        </div>

        <form method="POST" action="{{ route('owner.companions.store') }}" class="space-y-4">
            @csrf
            <div class="grid grid-cols-2 gap-3">
                <div>
                    <label class="block text-xs font-semibold text-slate-300 uppercase mb-1">Nama Panggung *</label>
                    <input type="text" name="stage_name" required placeholder="Contoh: Luna"
                        class="w-full px-3.5 py-2.5 bg-slate-900 border border-slate-700 rounded-xl text-xs text-white focus:outline-none focus:border-pink-500">
                </div>
                <div>
                    <label class="block text-xs font-semibold text-slate-300 uppercase mb-1">Nama Asli</label>
                    <input type="text" name="real_name" placeholder="Contoh: Siti M."
                        class="w-full px-3.5 py-2.5 bg-slate-900 border border-slate-700 rounded-xl text-xs text-white focus:outline-none focus:border-pink-500">
                </div>
            </div>

            <div>
                <label class="block text-xs font-semibold text-slate-300 uppercase mb-1">No. WhatsApp / HP</label>
                <input type="text" name="phone" placeholder="Contoh: 08123456789"
                    class="w-full px-3.5 py-2.5 bg-slate-900 border border-slate-700 rounded-xl text-xs text-white focus:outline-none focus:border-pink-500">
            </div>

            <div class="grid grid-cols-2 gap-3">
                <div>
                    <label class="block text-xs font-semibold text-slate-300 uppercase mb-1">Tarif Jasa / Jam (Rp) *</label>
                    <input type="number" name="price_per_hour" required min="0" step="5000" value="175000"
                        class="w-full px-3.5 py-2.5 bg-slate-900 border border-slate-700 rounded-xl text-xs text-white font-mono focus:outline-none focus:border-pink-500">
                </div>
                <div>
                    <label class="block text-xs font-semibold text-emerald-400 uppercase mb-1">Hak Komisi LC (%) *</label>
                    <input type="number" name="commission_rate" required min="0" max="100" value="70"
                        class="w-full px-3.5 py-2.5 bg-slate-900 border border-emerald-500/40 rounded-xl text-xs text-emerald-400 font-mono font-bold focus:outline-none focus:border-emerald-500">
                </div>
            </div>

            <div>
                <label class="block text-xs font-semibold text-slate-300 uppercase mb-1">Keahlian & Genre (Pisahkan Koma)</label>
                <input type="text" name="skills" placeholder="Contoh: Pop, Mandarin, Dangdut Koplo, Host Acara"
                    class="w-full px-3.5 py-2.5 bg-slate-900 border border-slate-700 rounded-xl text-xs text-white focus:outline-none focus:border-pink-500">
            </div>

            <div>
                <label class="block text-xs font-semibold text-slate-300 uppercase mb-1">Catatan Tambahan</label>
                <textarea name="notes" rows="2" placeholder="Catatan internal manajemen..."
                    class="w-full px-3.5 py-2.5 bg-slate-900 border border-slate-700 rounded-xl text-xs text-white focus:outline-none focus:border-pink-500"></textarea>
            </div>

            <div class="pt-2">
                <button type="submit" class="w-full py-3 px-4 rounded-xl bg-gradient-to-r from-pink-600 to-rose-600 hover:from-pink-500 hover:to-rose-500 text-white font-extrabold text-sm shadow-xl shadow-pink-600/30 transition-all">
                    Simpan Personel LC
                </button>
            </div>
        </form>
    </div>
</div>

<!-- 2. Edit Companion Modal -->
<div id="editCompanionModal" class="fixed inset-0 bg-slate-950/80 backdrop-blur-sm z-50 hidden flex items-center justify-center p-4">
    <div class="bg-[#0c1222] border border-slate-800 rounded-3xl max-w-md w-full p-6 sm:p-8 shadow-2xl relative">
        <button type="button" onclick="closeModal('editCompanionModal')" class="absolute top-5 right-5 text-slate-400 hover:text-white">
            <i class="fa-solid fa-xmark text-lg"></i>
        </button>

        <div class="mb-5">
            <span class="text-[10px] font-bold uppercase tracking-wider text-pink-400">EDIT PERSONEL LC</span>
            <h3 class="text-xl font-black text-white mt-0.5">Perbarui Data & Komisi</h3>
        </div>

        <form method="POST" id="editCompanionForm" class="space-y-4">
            @csrf
            @method('PUT')

            <div class="grid grid-cols-2 gap-3">
                <div>
                    <label class="block text-xs font-semibold text-slate-300 uppercase mb-1">Nama Panggung *</label>
                    <input type="text" name="stage_name" id="edit_stage_name" required
                        class="w-full px-3.5 py-2.5 bg-slate-900 border border-slate-700 rounded-xl text-xs text-white focus:outline-none focus:border-pink-500">
                </div>
                <div>
                    <label class="block text-xs font-semibold text-slate-300 uppercase mb-1">Nama Asli</label>
                    <input type="text" name="real_name" id="edit_real_name"
                        class="w-full px-3.5 py-2.5 bg-slate-900 border border-slate-700 rounded-xl text-xs text-white focus:outline-none focus:border-pink-500">
                </div>
            </div>

            <div class="grid grid-cols-2 gap-3">
                <div>
                    <label class="block text-xs font-semibold text-slate-300 uppercase mb-1">No. WhatsApp</label>
                    <input type="text" name="phone" id="edit_phone"
                        class="w-full px-3.5 py-2.5 bg-slate-900 border border-slate-700 rounded-xl text-xs text-white focus:outline-none focus:border-pink-500">
                </div>
                <div>
                    <label class="block text-xs font-semibold text-slate-300 uppercase mb-1">Status Keaktifan *</label>
                    <select name="status" id="edit_status" required class="w-full px-3.5 py-2.5 bg-slate-900 border border-slate-700 rounded-xl text-xs text-white focus:outline-none focus:border-pink-500">
                        <option value="standby">Standby</option>
                        <option value="in_room">In-Room</option>
                        <option value="off">Off Duty</option>
                    </select>
                </div>
            </div>

            <div class="grid grid-cols-2 gap-3">
                <div>
                    <label class="block text-xs font-semibold text-slate-300 uppercase mb-1">Tarif Jasa / Jam (Rp) *</label>
                    <input type="number" name="price_per_hour" id="edit_price_per_hour" required min="0" step="5000"
                        class="w-full px-3.5 py-2.5 bg-slate-900 border border-slate-700 rounded-xl text-xs text-white font-mono focus:outline-none focus:border-pink-500">
                </div>
                <div>
                    <label class="block text-xs font-semibold text-emerald-400 uppercase mb-1">Hak Komisi LC (%) *</label>
                    <input type="number" name="commission_rate" id="edit_commission_rate" required min="0" max="100"
                        class="w-full px-3.5 py-2.5 bg-slate-900 border border-emerald-500/40 rounded-xl text-xs text-emerald-400 font-mono font-bold focus:outline-none focus:border-emerald-500">
                </div>
            </div>

            <div>
                <label class="block text-xs font-semibold text-slate-300 uppercase mb-1">Keahlian & Repertoar</label>
                <input type="text" name="skills" id="edit_skills"
                    class="w-full px-3.5 py-2.5 bg-slate-900 border border-slate-700 rounded-xl text-xs text-white focus:outline-none focus:border-pink-500">
            </div>

            <div class="pt-2">
                <button type="submit" class="w-full py-3 px-4 rounded-xl bg-pink-600 hover:bg-pink-500 text-white font-extrabold text-sm shadow-xl shadow-pink-600/30 transition-all">
                    Perbarui Data LC
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

    function openEditCompanionModal(c) {
        document.getElementById('editCompanionForm').action = "/owner/companions/" + c.id;
        document.getElementById('edit_stage_name').value = c.stage_name;
        document.getElementById('edit_real_name').value = c.real_name || '';
        document.getElementById('edit_phone').value = c.phone || '';
        document.getElementById('edit_status').value = c.status;
        document.getElementById('edit_price_per_hour').value = c.price_per_hour;
        document.getElementById('edit_commission_rate').value = c.commission_rate;
        document.getElementById('edit_skills').value = c.skills || '';
        openModal('editCompanionModal');
    }
</script>
@endsection
