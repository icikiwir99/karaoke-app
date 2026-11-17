@extends('layouts.admin')

@section('title', 'Dashboard Operasional Kasir')
@section('header_title', 'Dashboard Operasional Kasir')
@section('header_subtitle', 'Pantau kamar, kas harian, dan antrian reservasi secara langsung')

@section('content')
<div class="space-y-8">
    <!-- Active Shift & Daily Summary Banner -->
    <div class="grid grid-cols-1 md:grid-cols-4 gap-6">
        <!-- Revenue Today -->
        <div class="bg-[#0c1222] border border-slate-800 rounded-2xl p-5 shadow-xl flex items-center space-x-4">
            <div class="w-12 h-12 rounded-xl bg-purple-600/20 border border-purple-500/30 flex items-center justify-center text-purple-400 shrink-0">
                <i class="fa-solid fa-coins text-xl"></i>
            </div>
            <div>
                <span class="text-[10px] uppercase font-bold text-slate-400 tracking-wider">Omset Hari Ini</span>
                <p class="text-xl font-black text-amber-400 font-mono mt-0.5">Rp {{ number_format($todayRevenue, 0, ',', '.') }}</p>
                <span class="text-[10px] text-slate-500">{{ $todayTransactionsCount }} Transaksi Selesai</span>
            </div>
        </div>

        <!-- Room Occupancy -->
        <div class="bg-[#0c1222] border border-slate-800 rounded-2xl p-5 shadow-xl flex items-center space-x-4">
            <div class="w-12 h-12 rounded-xl bg-emerald-600/20 border border-emerald-500/30 flex items-center justify-center text-emerald-400 shrink-0">
                <i class="fa-solid fa-door-open text-xl"></i>
            </div>
            <div>
                <span class="text-[10px] uppercase font-bold text-slate-400 tracking-wider">Status Kamar</span>
                <p class="text-xl font-black text-white mt-0.5">
                    <span class="text-emerald-400">{{ $roomCounts['available'] }}</span> Ready /
                    <span class="text-rose-400">{{ $roomCounts['occupied'] }}</span> In-Use
                </p>
                <span class="text-[10px] text-slate-500">{{ $roomCounts['cleaning'] }} Cleaning, {{ $roomCounts['blocked'] }} Blocked</span>
            </div>
        </div>

        <!-- Live LC Standby -->
        <div class="bg-[#0c1222] border border-slate-800 rounded-2xl p-5 shadow-xl flex items-center space-x-4">
            <div class="w-12 h-12 rounded-xl bg-pink-600/20 border border-pink-500/30 flex items-center justify-center text-pink-400 shrink-0">
                <i class="fa-solid fa-person-dress text-xl"></i>
            </div>
            <div>
                <span class="text-[10px] uppercase font-bold text-slate-400 tracking-wider">LC Pemandu Lagu</span>
                <p class="text-xl font-black text-white mt-0.5">
                    <span class="text-emerald-400">{{ $standbyCompanionsCount }}</span> Standby /
                    <span class="text-pink-400">{{ $inRoomCompanionsCount }}</span> In-Room
                </p>
                <a href="{{ route('kasir.companions') }}" class="text-[10px] text-pink-400 hover:underline font-semibold">Buka LC Tracker &rarr;</a>
            </div>
        </div>

        <!-- Pending Bookings Alert -->
        <div class="bg-[#0c1222] border border-slate-800 rounded-2xl p-5 shadow-xl flex items-center space-x-4">
            <div class="w-12 h-12 rounded-xl bg-cyan-600/20 border border-cyan-500/30 flex items-center justify-center text-cyan-400 shrink-0">
                <i class="fa-solid fa-bell text-xl"></i>
            </div>
            <div>
                <span class="text-[10px] uppercase font-bold text-slate-400 tracking-wider">Booking Online Baru</span>
                <p class="text-xl font-black {{ $pendingBookingsCount > 0 ? 'text-rose-400 animate-pulse' : 'text-slate-300' }} mt-0.5">
                    {{ $pendingBookingsCount }} Reservasi
                </p>
                <a href="{{ route('kasir.reservations') }}?status=pending" class="text-[10px] text-cyan-400 hover:underline font-semibold">Konfirmasi Sekarang &rarr;</a>
            </div>
        </div>
    </div>

    <!-- Active Cashier Shift Box -->
    <div class="bg-[#0c1222] border border-slate-800 rounded-2xl p-6 shadow-xl">
        <div class="flex flex-col md:flex-row md:items-center justify-between gap-4">
            <div class="flex items-center space-x-4">
                <div class="w-12 h-12 rounded-2xl {{ $activeShift ? 'bg-emerald-500/20 text-emerald-400 border border-emerald-500/30' : 'bg-rose-500/20 text-rose-400 border border-rose-500/30' }} flex items-center justify-center text-xl shrink-0">
                    <i class="fa-solid fa-cash-register"></i>
                </div>
                <div>
                    <div class="flex items-center space-x-2">
                        <h3 class="text-base font-bold text-white">Status Shift Kasir:</h3>
                        @if($activeShift)
                            <span class="px-2.5 py-0.5 rounded text-[10px] font-black uppercase tracking-wider bg-emerald-500/20 text-emerald-300 border border-emerald-500/30">
                                ACTIVE OPEN
                            </span>
                        @else
                            <span class="px-2.5 py-0.5 rounded text-[10px] font-black uppercase tracking-wider bg-rose-500/20 text-rose-300 border border-rose-500/30">
                                CLOSED / BELUM DIBUKA
                            </span>
                        @endif
                    </div>
                    @if($activeShift)
                        <p class="text-xs text-slate-400 mt-1">
                            Dibuka sejak: <span class="text-slate-200 font-semibold">{{ $activeShift->start_time->format('d M Y, H:i') }} WIB</span> | Modal Awal: <span class="text-amber-400 font-mono font-bold">Rp {{ number_format($activeShift->starting_cash, 0, ',', '.') }}</span>
                        </p>
                    @else
                        <p class="text-xs text-slate-400 mt-1">
                            Anda belum membuka shift kasir hari ini. Silakan input kas modal awal untuk mulai bertransaksi.
                        </p>
                    @endif
                </div>
            </div>

            <div class="flex items-center space-x-3">
                <a href="{{ route('kasir.shifts') }}" class="px-4 py-2 bg-slate-800 hover:bg-slate-700 text-slate-200 text-xs font-bold rounded-xl transition-all">
                    <i class="fa-solid fa-clock-rotate-left mr-1.5"></i> Riwayat Shift & Mutasi
                </a>
                <a href="{{ route('kasir.pos') }}" class="px-5 py-2 bg-gradient-to-r from-amber-500 to-amber-600 hover:from-amber-400 hover:to-amber-500 text-slate-950 text-xs font-black rounded-xl transition-all shadow-lg shadow-amber-500/20">
                    <i class="fa-solid fa-cash-register mr-1.5"></i> Buka Layar POS
                </a>
            </div>
        </div>
    </div>

    <!-- Quick Navigation to Operational Features -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
        <a href="{{ route('kasir.rooms') }}" class="bg-[#0c1222] border border-slate-800 hover:border-emerald-500/40 rounded-2xl p-6 transition-all hover:-translate-y-1 group shadow-xl">
            <div class="w-12 h-12 rounded-xl bg-emerald-600/20 border border-emerald-500/30 text-emerald-400 flex items-center justify-center text-xl mb-4 group-hover:scale-110 transition-transform">
                <i class="fa-solid fa-table-cells-large"></i>
            </div>
            <h4 class="text-base font-bold text-white group-hover:text-emerald-300 transition-colors">Monitoring Status Kamar (Grid)</h4>
            <p class="text-xs text-slate-400 mt-1.5 leading-relaxed">
                Tampilan visual real-time seluruh kamar karaoke. Check-in tamu walk-in, tambah pesanan minuman ke kamar, tugaskan LC, dan selesaikan billing.
            </p>
            <span class="inline-flex items-center text-xs font-bold text-emerald-400 mt-4">
                Buka Grid Kamar &rarr;
            </span>
        </a>

        <a href="{{ route('kasir.companions') }}" class="bg-[#0c1222] border border-slate-800 hover:border-pink-500/40 rounded-2xl p-6 transition-all hover:-translate-y-1 group shadow-xl">
            <div class="w-12 h-12 rounded-xl bg-pink-600/20 border border-pink-500/30 text-pink-400 flex items-center justify-center text-xl mb-4 group-hover:scale-110 transition-transform">
                <i class="fa-solid fa-person-dress"></i>
            </div>
            <h4 class="text-base font-bold text-white group-hover:text-pink-300 transition-colors">Live Companion Tracker</h4>
            <p class="text-xs text-slate-400 mt-1.5 leading-relaxed">
                Pantau status aktif seluruh personel pemandu lagu (Standby di lounge, In-Room bertugas, atau Off duty) dengan penugasan kamar cepat.
            </p>
            <span class="inline-flex items-center text-xs font-bold text-pink-400 mt-4">
                Buka Live Tracker &rarr;
            </span>
        </a>

        <a href="{{ route('kasir.reservations') }}" class="bg-[#0c1222] border border-slate-800 hover:border-purple-500/40 rounded-2xl p-6 transition-all hover:-translate-y-1 group shadow-xl">
            <div class="w-12 h-12 rounded-xl bg-purple-600/20 border border-purple-500/30 text-purple-400 flex items-center justify-center text-xl mb-4 group-hover:scale-110 transition-transform">
                <i class="fa-solid fa-calendar-check"></i>
            </div>
            <h4 class="text-base font-bold text-white group-hover:text-purple-300 transition-colors">Kelola Booking Online & Walk-in</h4>
            <p class="text-xs text-slate-400 mt-1.5 leading-relaxed">
                Konfirmasi booking yang masuk dari pelanggan online, pantau pre-order minuman, dan ubah status kamar menjadi occupied saat tamu tiba.
            </p>
            <span class="inline-flex items-center text-xs font-bold text-purple-400 mt-4">
                Kelola Reservasi &rarr;
            </span>
        </a>
    </div>

    <!-- Recent Sales Table -->
    <div class="bg-[#0c1222] border border-slate-800 rounded-2xl p-6 shadow-xl">
        <div class="flex items-center justify-between mb-4">
            <div>
                <h3 class="text-base font-bold text-white">Transaksi Kasir Terakhir</h3>
                <p class="text-xs text-slate-400">Daftar struk penjualan dan pembayaran terkini</p>
            </div>
            <a href="{{ route('kasir.pos') }}" class="text-xs text-purple-400 hover:underline font-semibold">Transaksi Baru &rarr;</a>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-left text-xs text-slate-300">
                <thead class="bg-slate-900/80 text-[10px] uppercase font-bold text-slate-400 border-b border-slate-800">
                    <tr>
                        <th class="py-3 px-4">No Invoice</th>
                        <th class="py-3 px-4">Waktu</th>
                        <th class="py-3 px-4">Customer</th>
                        <th class="py-3 px-4">Room</th>
                        <th class="py-3 px-4">Metode</th>
                        <th class="py-3 px-4 text-right">Grand Total</th>
                        <th class="py-3 px-4 text-center">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-800/60">
                    @forelse($recentSales as $sale)
                        <tr class="hover:bg-slate-900/40 transition-colors">
                            <td class="py-3 px-4 font-mono font-bold text-amber-400">{{ $sale->invoice_number }}</td>
                            <td class="py-3 px-4">{{ $sale->created_at->format('H:i') }} WIB</td>
                            <td class="py-3 px-4 font-semibold text-white">{{ $sale->customer_name }}</td>
                            <td class="py-3 px-4">
                                @if($sale->room)
                                    <span class="px-2 py-0.5 rounded bg-slate-800 border border-slate-700 font-mono font-bold">{{ $sale->room->room_number }}</span>
                                @else
                                    <span class="text-slate-500">-</span>
                                @endif
                            </td>
                            <td class="py-3 px-4">
                                <span class="px-2 py-0.5 rounded text-[10px] uppercase font-bold {{ $sale->payment_method === 'cash' ? 'bg-emerald-500/20 text-emerald-300 border border-emerald-500/30' : ($sale->payment_method === 'complimentary' ? 'bg-amber-500/20 text-amber-300 border border-amber-500/30' : 'bg-blue-500/20 text-blue-300 border border-blue-500/30') }}">
                                    {{ $sale->payment_method }}
                                </span>
                            </td>
                            <td class="py-3 px-4 text-right font-mono font-bold text-white">{{ $sale->formatted_grand_total }}</td>
                            <td class="py-3 px-4 text-center">
                                <a href="{{ route('kasir.thermalReceipt', $sale->id) }}" target="_blank"
                                    class="px-2.5 py-1 bg-slate-800 hover:bg-purple-600 text-slate-300 hover:text-white rounded-lg text-[11px] font-bold transition-all"
                                    title="Cetak Ulang Struk Thermal">
                                    <i class="fa-solid fa-print mr-1"></i> Struk
                                </a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="py-6 text-center text-slate-500">Belum ada transaksi pada hari ini.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection
