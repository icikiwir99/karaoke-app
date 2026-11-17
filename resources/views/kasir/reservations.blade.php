@extends('layouts.admin')

@section('title', 'Manajemen Reservasi Tamu')
@section('header_title', 'Manajemen Reservasi Tamu')
@section('header_subtitle', 'Konfirmasi booking online dari WhatsApp dan kelola transaksi tamu')

@section('content')
<div class="space-y-6">
    <!-- Filter Tabs -->
    <div class="bg-[#0c1222] border border-slate-800 rounded-2xl p-4 flex flex-wrap items-center justify-between gap-4">
        <div class="flex flex-wrap items-center gap-2">
            <a href="{{ route('kasir.reservations', ['status' => 'all']) }}"
                class="px-3.5 py-1.5 rounded-xl text-xs font-bold transition-all {{ $status === 'all' ? 'bg-purple-600 text-white shadow-lg shadow-purple-600/30' : 'bg-slate-900 text-slate-300 hover:bg-slate-800 border border-slate-800' }}">
                Semua Reservasi ({{ $counts['all'] }})
            </a>
            <a href="{{ route('kasir.reservations', ['status' => 'pending']) }}"
                class="px-3.5 py-1.5 rounded-xl text-xs font-bold transition-all {{ $status === 'pending' ? 'bg-amber-500 text-slate-950 font-black shadow-lg shadow-amber-500/30' : 'bg-slate-900 text-amber-300 hover:bg-slate-800 border border-slate-800' }}">
                Pending Konfirmasi ({{ $counts['pending'] }})
            </a>
            <a href="{{ route('kasir.reservations', ['status' => 'confirmed']) }}"
                class="px-3.5 py-1.5 rounded-xl text-xs font-bold transition-all {{ $status === 'confirmed' ? 'bg-emerald-600 text-white shadow-lg shadow-emerald-600/30' : 'bg-slate-900 text-emerald-300 hover:bg-slate-800 border border-slate-800' }}">
                Aktif / Confirmed ({{ $counts['confirmed'] }})
            </a>
            <a href="{{ route('kasir.reservations', ['status' => 'completed']) }}"
                class="px-3.5 py-1.5 rounded-xl text-xs font-bold transition-all {{ $status === 'completed' ? 'bg-blue-600 text-white shadow-lg shadow-blue-600/30' : 'bg-slate-900 text-slate-300 hover:bg-slate-800 border border-slate-800' }}">
                Selesai ({{ $counts['completed'] }})
            </a>
            <a href="{{ route('kasir.reservations', ['status' => 'cancelled']) }}"
                class="px-3.5 py-1.5 rounded-xl text-xs font-bold transition-all {{ $status === 'cancelled' ? 'bg-rose-600 text-white shadow-lg shadow-rose-600/30' : 'bg-slate-900 text-rose-300 hover:bg-slate-800 border border-slate-800' }}">
                Dibatalkan ({{ $counts['cancelled'] }})
            </a>
        </div>

        <a href="{{ route('kasir.rooms') }}" class="px-3.5 py-1.5 bg-emerald-600 hover:bg-emerald-500 text-white rounded-xl text-xs font-bold transition-all">
            <i class="fa-solid fa-plus mr-1"></i> Input Walk-in Baru
        </a>
    </div>

    <!-- Reservations Table -->
    <div class="bg-[#0c1222] border border-slate-800 rounded-2xl p-6 shadow-xl">
        <div class="overflow-x-auto">
            <table class="w-full text-left text-xs text-slate-300">
                <thead class="bg-slate-900/80 text-[10px] uppercase font-bold text-slate-400 border-b border-slate-800">
                    <tr>
                        <th class="py-3 px-4">Kode Booking</th>
                        <th class="py-3 px-4">Tamu</th>
                        <th class="py-3 px-4">Kamar & Paket</th>
                        <th class="py-3 px-4">Jadwal & Durasi</th>
                        <th class="py-3 px-4">Add-on LC</th>
                        <th class="py-3 px-4">Pre-Order F&B</th>
                        <th class="py-3 px-4 text-right">Total Estimasi</th>
                        <th class="py-3 px-4 text-center">Status</th>
                        <th class="py-3 px-4 text-center">Aksi Kasir</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-800/60">
                    @forelse($bookings as $booking)
                        <tr class="hover:bg-slate-900/40 transition-colors">
                            <td class="py-3.5 px-4 font-mono font-bold text-amber-400">{{ $booking->booking_code }}</td>
                            <td class="py-3.5 px-4">
                                <span class="font-bold text-white block">{{ $booking->customer_name }}</span>
                                <span class="text-[11px] text-slate-400">{{ $booking->customer_phone }}</span>
                            </td>
                            <td class="py-3.5 px-4">
                                <span class="font-bold text-slate-200 block">{{ $booking->room->room_number }} ({{ ucfirst($booking->room->type) }})</span>
                                @if($booking->package)
                                    <span class="text-[10px] text-amber-400 font-semibold block">{{ $booking->package->name }}</span>
                                @endif
                            </td>
                            <td class="py-3.5 px-4">
                                <span class="text-white block font-medium">{{ $booking->start_time->format('d M, H:i') }} WIB</span>
                                <span class="text-[10px] text-slate-400">{{ $booking->duration_hours }} Jam Sesi</span>
                            </td>
                            <td class="py-3.5 px-4">
                                @if($booking->include_lc)
                                    <span class="text-pink-400 font-bold block">{{ $booking->lc_qty }} LC x {{ $booking->lc_hours }} Jam</span>
                                    @if($booking->companions->count() > 0)
                                        <span class="text-[10px] text-slate-400">{{ $booking->companions->pluck('companion.stage_name')->join(', ') }}</span>
                                    @endif
                                @else
                                    <span class="text-slate-500">Tanpa LC</span>
                                @endif
                            </td>
                            <td class="py-3.5 px-4">
                                @if($booking->items->count() > 0)
                                    <span class="text-amber-400 font-bold block">{{ $booking->items->count() }} Item Pre-order</span>
                                    <span class="text-[10px] text-slate-400 font-mono">Rp {{ number_format($booking->preorder_total, 0, ',', '.') }}</span>
                                @else
                                    <span class="text-slate-500">-</span>
                                @endif
                            </td>
                            <td class="py-3.5 px-4 text-right font-mono font-bold text-white">
                                {{ $booking->formatted_total }}
                            </td>
                            <td class="py-3.5 px-4 text-center">
                                @if($booking->status === 'pending')
                                    <span class="px-2.5 py-1 rounded text-[10px] font-black uppercase tracking-wider bg-amber-500/20 text-amber-300 border border-amber-500/30">
                                        Pending
                                    </span>
                                @elseif($booking->status === 'confirmed')
                                    <span class="px-2.5 py-1 rounded text-[10px] font-black uppercase tracking-wider bg-emerald-500/20 text-emerald-300 border border-emerald-500/30">
                                        Confirmed
                                    </span>
                                @elseif($booking->status === 'completed')
                                    <span class="px-2.5 py-1 rounded text-[10px] font-black uppercase tracking-wider bg-blue-500/20 text-blue-300 border border-blue-500/30">
                                        Completed
                                    </span>
                                @else
                                    <span class="px-2.5 py-1 rounded text-[10px] font-black uppercase tracking-wider bg-rose-500/20 text-rose-300 border border-rose-500/30">
                                        Cancelled
                                    </span>
                                @endif
                            </td>
                            <td class="py-3.5 px-4 text-center">
                                <div class="flex items-center justify-center space-x-1.5">
                                    @if($booking->status === 'pending')
                                        <!-- Confirm Button -->
                                        <form method="POST" action="{{ route('kasir.reservations.confirm', $booking->id) }}">
                                            @csrf
                                            <button type="submit" class="px-2.5 py-1 bg-emerald-600 hover:bg-emerald-500 text-white rounded-lg text-[11px] font-bold transition-all" title="Konfirmasi & Buka Kamar">
                                                <i class="fa-solid fa-check mr-1"></i> Konfirmasi
                                            </button>
                                        </form>

                                        <!-- Cancel Button -->
                                        <form method="POST" action="{{ route('kasir.reservations.cancel', $booking->id) }}" onsubmit="return confirm('Yakin batalkan booking ini?')">
                                            @csrf
                                            <button type="submit" class="p-1 bg-slate-800 hover:bg-rose-900/60 text-slate-400 hover:text-rose-300 rounded-lg text-xs transition-all" title="Batalkan">
                                                <i class="fa-solid fa-xmark"></i>
                                            </button>
                                        </form>

                                    @elseif($booking->status === 'confirmed')
                                        <!-- Direct POS Link -->
                                        <a href="{{ route('kasir.pos', ['booking_id' => $booking->id]) }}"
                                            class="px-2.5 py-1 bg-purple-600 hover:bg-purple-500 text-white rounded-lg text-[11px] font-bold transition-all" title="Buka di Layar POS">
                                            <i class="fa-solid fa-cash-register mr-1"></i> Kasir POS
                                        </a>
                                    @else
                                        <span class="text-slate-500 text-[11px]">-</span>
                                    @endif
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="9" class="py-8 text-center text-slate-500">Tidak ada data reservasi pada kategori ini.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="mt-6">
            {{ $bookings->links() }}
        </div>
    </div>
</div>
@endsection
