@extends('layouts.admin')

@section('title', 'Manajemen Shift Kasir & Rekonsiliasi Kas')
@section('header_title', 'Manajemen Shift Kasir')
@section('header_subtitle', 'Pencatatan kas modal awal, mutasi kas masuk/keluar, dan rekonsiliasi tutup shift')

@section('content')
<div class="space-y-8">
    <!-- Active Shift Status Card -->
    <div class="bg-[#0c1222] border {{ $activeShift ? 'border-emerald-500/40' : 'border-rose-500/40' }} rounded-3xl p-6 sm:p-8 shadow-2xl relative overflow-hidden">
        <div class="flex flex-col lg:flex-row lg:items-center justify-between gap-6">
            <div>
                <div class="flex items-center space-x-3 mb-2">
                    <span class="px-3 py-1 rounded-full text-xs font-black uppercase tracking-wider {{ $activeShift ? 'bg-emerald-500/20 text-emerald-300 border border-emerald-500/30' : 'bg-rose-500/20 text-rose-300 border border-rose-500/30' }}">
                        {{ $activeShift ? 'SHIFT AKTIF (OPEN)' : 'SHIFT BELUM DIBUKA' }}
                    </span>
                    @if($activeShift)
                        <span class="text-xs text-slate-400 font-mono">Dibuka: {{ $activeShift->start_time->format('d M Y, H:i') }} WIB</span>
                    @endif
                </div>

                @if($activeShift)
                    <h2 class="text-2xl font-black text-white">{{ $activeShift->shift_name }} (Kasir: {{ $activeShift->user->name }})</h2>
                    <p class="text-xs text-slate-400 mt-1 max-w-xl">{{ $activeShift->notes ?? 'Tidak ada catatan pembukaan shift.' }}</p>

                    <!-- Real-Time Cash Drawer Status -->
                    <div class="grid grid-cols-2 sm:grid-cols-4 gap-4 mt-6 pt-6 border-t border-slate-800">
                        <div>
                            <span class="text-[10px] uppercase font-bold text-slate-500">Modal Awal Laci</span>
                            <p class="text-lg font-mono font-bold text-white mt-0.5">Rp {{ number_format($activeShift->starting_cash, 0, ',', '.') }}</p>
                        </div>
                        <div>
                            <span class="text-[10px] uppercase font-bold text-slate-500">Penjualan Tunai</span>
                            <p class="text-lg font-mono font-bold text-emerald-400 mt-0.5">Rp {{ number_format($activeShift->total_sales_cash, 0, ',', '.') }}</p>
                        </div>
                        <div>
                            <span class="text-[10px] uppercase font-bold text-slate-500">Penjualan Non-Tunai</span>
                            <p class="text-lg font-mono font-bold text-blue-400 mt-0.5">Rp {{ number_format($activeShift->total_sales_noncash, 0, ',', '.') }}</p>
                        </div>
                        <div>
                            <span class="text-[10px] uppercase font-bold text-amber-400">Ekspektasi Kas Laci</span>
                            <p class="text-lg font-mono font-black text-amber-400 mt-0.5">Rp {{ number_format($activeShift->expected_cash, 0, ',', '.') }}</p>
                        </div>
                    </div>
                @else
                    <h2 class="text-2xl font-black text-white">Buka Shift Baru untuk Memulai Transaksi</h2>
                    <p class="text-xs text-slate-400 mt-1">
                        Setiap pergantian pegawai kasir diwajibkan mencatat modal kas pembuka untuk menjaga transparansi rekonsiliasi keuangan.
                    </p>
                @endif
            </div>

            <!-- Action Buttons -->
            <div class="flex flex-col sm:flex-row gap-3 shrink-0">
                @if($activeShift)
                    <button type="button" onclick="openModal('cashMovementModal')"
                        class="px-4 py-2.5 bg-slate-800 hover:bg-slate-700 text-slate-200 font-bold text-xs rounded-xl transition-all border border-slate-700">
                        <i class="fa-solid fa-money-bill-transfer mr-1.5 text-amber-400"></i> Catat Kas Masuk / Keluar
                    </button>
                    <button type="button" onclick="openModal('closeShiftModal')"
                        class="px-5 py-2.5 bg-rose-600 hover:bg-rose-500 text-white font-extrabold text-xs rounded-xl shadow-lg shadow-rose-600/30 transition-all">
                        <i class="fa-solid fa-lock mr-1.5"></i> Tutup Shift Sekarang
                    </button>
                @else
                    <button type="button" onclick="openModal('startShiftModal')"
                        class="px-6 py-3 bg-gradient-to-r from-emerald-600 to-teal-600 hover:from-emerald-500 hover:to-teal-500 text-white font-extrabold text-xs rounded-xl shadow-xl shadow-emerald-600/30 transition-all">
                        <i class="fa-solid fa-key mr-2"></i> Input Modal & Buka Shift
                    </button>
                @endif
            </div>
        </div>
    </div>

    <!-- Shift History Table -->
    <div class="bg-[#0c1222] border border-slate-800 rounded-2xl p-6 shadow-xl">
        <h3 class="text-base font-bold text-white mb-1">Riwayat & Rekonsiliasi Shift Kasir</h3>
        <p class="text-xs text-slate-400 mb-4">Laporan pencatatan modal awal, penerimaan kas, dan selisih fisik kas akhir</p>

        <div class="overflow-x-auto">
            <table class="w-full text-left text-xs text-slate-300">
                <thead class="bg-slate-900/80 text-[10px] uppercase font-bold text-slate-400 border-b border-slate-800">
                    <tr>
                        <th class="py-3 px-4">Nama Shift</th>
                        <th class="py-3 px-4">Pegawai Kasir</th>
                        <th class="py-3 px-4">Waktu Buka / Tutup</th>
                        <th class="py-3 px-4 text-right">Modal Awal</th>
                        <th class="py-3 px-4 text-right">Penjualan Tunai</th>
                        <th class="py-3 px-4 text-right">Kas Fisik Akhir</th>
                        <th class="py-3 px-4 text-right">Selisih Kas</th>
                        <th class="py-3 px-4 text-center">Status</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-800/60">
                    @forelse($recentShifts as $sh)
                        <tr class="hover:bg-slate-900/40 transition-colors">
                            <td class="py-3.5 px-4 font-bold text-white">{{ $sh->shift_name }}</td>
                            <td class="py-3.5 px-4 font-semibold text-slate-200">{{ $sh->user ? $sh->user->name : '-' }}</td>
                            <td class="py-3.5 px-4 text-[11px]">
                                <span>{{ $sh->start_time->format('d/m/Y H:i') }}</span>
                                <span class="text-slate-500 block">s/d {{ $sh->end_time ? $sh->end_time->format('d/m/Y H:i') : 'Sekarang' }}</span>
                            </td>
                            <td class="py-3.5 px-4 text-right font-mono font-bold text-slate-300">
                                Rp {{ number_format($sh->starting_cash, 0, ',', '.') }}
                            </td>
                            <td class="py-3.5 px-4 text-right font-mono font-bold text-emerald-400">
                                Rp {{ number_format($sh->total_sales_cash, 0, ',', '.') }}
                            </td>
                            <td class="py-3.5 px-4 text-right font-mono font-bold text-amber-400">
                                {{ $sh->actual_cash !== null ? 'Rp ' . number_format($sh->actual_cash, 0, ',', '.') : '-' }}
                            </td>
                            <td class="py-3.5 px-4 text-right font-mono font-bold">
                                @if($sh->difference !== null)
                                    @if($sh->difference == 0)
                                        <span class="text-emerald-400">Rp 0 (Pas)</span>
                                    @elseif($sh->difference > 0)
                                        <span class="text-blue-400">+ Rp {{ number_format($sh->difference, 0, ',', '.') }}</span>
                                    @else
                                        <span class="text-rose-400">- Rp {{ number_format(abs($sh->difference), 0, ',', '.') }}</span>
                                    @endif
                                @else
                                    <span class="text-slate-500">-</span>
                                @endif
                            </td>
                            <td class="py-3.5 px-4 text-center">
                                @if($sh->status === 'open')
                                    <span class="px-2.5 py-1 rounded text-[10px] font-black uppercase tracking-wider bg-emerald-500/20 text-emerald-300 border border-emerald-500/30">
                                        OPEN
                                    </span>
                                @else
                                    <span class="px-2.5 py-1 rounded text-[10px] font-black uppercase tracking-wider bg-slate-800 text-slate-400 border border-slate-700">
                                        CLOSED
                                    </span>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" class="py-8 text-center text-slate-500">Belum ada riwayat shift yang tercatat.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- ==================== SHIFT MODALS ==================== -->

<!-- 1. Start Shift Modal -->
<div id="startShiftModal" class="fixed inset-0 bg-slate-950/80 backdrop-blur-sm z-50 hidden flex items-center justify-center p-4">
    <div class="bg-[#0c1222] border border-slate-800 rounded-3xl max-w-md w-full p-6 sm:p-8 shadow-2xl relative">
        <button type="button" onclick="closeModal('startShiftModal')" class="absolute top-5 right-5 text-slate-400 hover:text-white">
            <i class="fa-solid fa-xmark text-lg"></i>
        </button>

        <div class="mb-5">
            <span class="text-[10px] font-bold uppercase tracking-wider text-emerald-400">PEMBUKAAN SHIFT BARU</span>
            <h3 class="text-xl font-black text-white mt-0.5">Input Kas Modal Awal</h3>
        </div>

        <form method="POST" action="{{ route('kasir.shifts.start') }}" class="space-y-4">
            @csrf
            <div>
                <label class="block text-xs font-semibold text-slate-300 uppercase mb-1">Nama / Waktu Shift *</label>
                <input type="text" name="shift_name" required value="Shift Reguler - {{ date('d M') }}"
                    class="w-full px-3.5 py-2.5 bg-slate-900 border border-slate-700 rounded-xl text-sm text-white focus:outline-none focus:border-emerald-500">
            </div>

            <div>
                <label class="block text-xs font-semibold text-slate-300 uppercase mb-1">Modal Kas Awal di Laci (Rp) *</label>
                <input type="number" name="starting_cash" required min="0" step="1000" value="1000000"
                    placeholder="Contoh: 1000000"
                    class="w-full px-3.5 py-2.5 bg-slate-900 border border-slate-700 rounded-xl text-sm text-amber-400 font-mono font-bold focus:outline-none focus:border-emerald-500">
                <span class="text-[10px] text-slate-500 mt-1 block">Nominal uang tunai pecahan yang diserahkan untuk kembalian.</span>
            </div>

            <div>
                <label class="block text-xs font-semibold text-slate-300 uppercase mb-1">Catatan Tambahan (Opsional)</label>
                <textarea name="notes" rows="2" placeholder="Catatan kondisi serah terima laci..."
                    class="w-full px-3.5 py-2.5 bg-slate-900 border border-slate-700 rounded-xl text-xs text-white focus:outline-none focus:border-emerald-500"></textarea>
            </div>

            <div class="pt-2">
                <button type="submit" class="w-full py-3 px-4 rounded-xl bg-gradient-to-r from-emerald-600 to-teal-600 hover:from-emerald-500 hover:to-teal-500 text-white font-extrabold text-sm shadow-xl shadow-emerald-600/30 transition-all">
                    Buka Shift Kasir Sekarang
                </button>
            </div>
        </form>
    </div>
</div>

<!-- 2. Cash Movement Modal (In / Out) -->
@if($activeShift)
<div id="cashMovementModal" class="fixed inset-0 bg-slate-950/80 backdrop-blur-sm z-50 hidden flex items-center justify-center p-4">
    <div class="bg-[#0c1222] border border-slate-800 rounded-3xl max-w-md w-full p-6 sm:p-8 shadow-2xl relative">
        <button type="button" onclick="closeModal('cashMovementModal')" class="absolute top-5 right-5 text-slate-400 hover:text-white">
            <i class="fa-solid fa-xmark text-lg"></i>
        </button>

        <div class="mb-5">
            <span class="text-[10px] font-bold uppercase tracking-wider text-amber-400">MUTASI KAS KECIL (PETTY CASH)</span>
            <h3 class="text-xl font-black text-white mt-0.5">Catat Kas Masuk / Keluar</h3>
        </div>

        <form method="POST" action="{{ route('kasir.shifts.movement') }}" class="space-y-4">
            @csrf
            <input type="hidden" name="cashier_shift_id" value="{{ $activeShift->id }}">

            <div>
                <label class="block text-xs font-semibold text-slate-300 uppercase mb-1">Jenis Mutasi *</label>
                <div class="grid grid-cols-2 gap-3">
                    <label class="p-3 rounded-xl border border-slate-700 bg-slate-900 cursor-pointer flex items-center space-x-2">
                        <input type="radio" name="type" value="in" required class="text-emerald-500">
                        <span class="text-xs font-bold text-emerald-400">Kas Masuk (In)</span>
                    </label>
                    <label class="p-3 rounded-xl border border-slate-700 bg-slate-900 cursor-pointer flex items-center space-x-2">
                        <input type="radio" name="type" value="out" required checked class="text-rose-500">
                        <span class="text-xs font-bold text-rose-400">Kas Keluar (Out)</span>
                    </label>
                </div>
            </div>

            <div>
                <label class="block text-xs font-semibold text-slate-300 uppercase mb-1">Nominal (Rp) *</label>
                <input type="number" name="amount" required min="1000" step="1000" placeholder="Contoh: 50000"
                    class="w-full px-3.5 py-2.5 bg-slate-900 border border-slate-700 rounded-xl text-sm text-white font-mono font-bold focus:outline-none focus:border-amber-400">
            </div>

            <div>
                <label class="block text-xs font-semibold text-slate-300 uppercase mb-1">Alasan / Keperluan *</label>
                <input type="text" name="reason" required placeholder="Contoh: Beli es batu kristal darurat, biaya galon..."
                    class="w-full px-3.5 py-2.5 bg-slate-900 border border-slate-700 rounded-xl text-xs text-white focus:outline-none focus:border-amber-400">
            </div>

            <div class="pt-2">
                <button type="submit" class="w-full py-3 px-4 rounded-xl bg-gradient-to-r from-amber-500 to-amber-600 hover:from-amber-400 hover:to-amber-500 text-slate-950 font-black text-sm shadow-xl shadow-amber-500/20 transition-all">
                    Simpan Mutasi Kas
                </button>
            </div>
        </form>
    </div>
</div>

<!-- 3. Close Shift Modal -->
<div id="closeShiftModal" class="fixed inset-0 bg-slate-950/80 backdrop-blur-sm z-50 hidden flex items-center justify-center p-4">
    <div class="bg-[#0c1222] border border-slate-800 rounded-3xl max-w-md w-full p-6 sm:p-8 shadow-2xl relative">
        <button type="button" onclick="closeModal('closeShiftModal')" class="absolute top-5 right-5 text-slate-400 hover:text-white">
            <i class="fa-solid fa-xmark text-lg"></i>
        </button>

        <div class="mb-5">
            <span class="text-[10px] font-bold uppercase tracking-wider text-rose-400">REKONSILIASI AKHIR SHIFT</span>
            <h3 class="text-xl font-black text-white mt-0.5">Tutup Shift Kasir</h3>
            <p class="text-xs text-slate-400 mt-1">Hitung seluruh uang fisik di laci dan masukkan nominalnya di bawah ini.</p>
        </div>

        <form method="POST" action="{{ route('kasir.shifts.close', $activeShift->id) }}" class="space-y-4">
            @csrf

            <div class="p-3.5 rounded-xl bg-slate-900/80 border border-slate-800 space-y-1.5 text-xs">
                <div class="flex justify-between text-slate-400">
                    <span>Ekspektasi Kas Sistem:</span>
                    <span class="font-mono font-bold text-white">Rp {{ number_format($activeShift->expected_cash, 0, ',', '.') }}</span>
                </div>
            </div>

            <div>
                <label class="block text-xs font-semibold text-slate-300 uppercase mb-1">Kas Fisik Nyata di Laci (Rp) *</label>
                <input type="number" name="actual_cash" id="actual_cash_input" required min="0" step="1000"
                    placeholder="Hitung uang tunai di laci..."
                    class="w-full px-3.5 py-2.5 bg-slate-900 border border-slate-700 rounded-xl text-base text-amber-400 font-mono font-black focus:outline-none focus:border-rose-500">
            </div>

            <div>
                <label class="block text-xs font-semibold text-slate-300 uppercase mb-1">Catatan Penutupan</label>
                <textarea name="notes" rows="2" placeholder="Catatan selisih kas atau serah terima..."
                    class="w-full px-3.5 py-2.5 bg-slate-900 border border-slate-700 rounded-xl text-xs text-white focus:outline-none focus:border-rose-500"></textarea>
            </div>

            <div class="pt-2">
                <button type="submit" class="w-full py-3 px-4 rounded-xl bg-rose-600 hover:bg-rose-500 text-white font-extrabold text-sm shadow-xl shadow-rose-600/30 transition-all">
                    Konfirmasi Tutup Shift & Selesaikan Rekonsiliasi
                </button>
            </div>
        </form>
    </div>
</div>
@endif

<script>
    function openModal(id) {
        document.getElementById(id).classList.remove('hidden');
    }
    function closeModal(id) {
        document.getElementById(id).classList.add('hidden');
    }
</script>
@endsection
