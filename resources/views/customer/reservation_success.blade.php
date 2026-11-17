@extends('layouts.app')

@section('title', 'Reservasi Berhasil - ' . $booking->booking_code . ' - HY KAROKE AND LOUNGE')

@section('content')
<div class="max-w-2xl mx-auto px-4 sm:px-6 lg:px-8 py-14">
    <!-- Success Badge -->
    <div class="text-center mb-8">
        <div class="w-16 h-16 rounded-full bg-emerald-500/20 border-2 border-emerald-500/40 text-emerald-400 flex items-center justify-center mx-auto text-2xl mb-4 shadow-xl shadow-emerald-500/20 animate-bounce">
            <i class="fa-solid fa-check"></i>
        </div>
        <span class="text-xs font-bold uppercase tracking-widest text-emerald-400">RESERVASI BERHASIL DISIMPAN</span>
        <h1 class="text-3xl font-black text-white mt-1">Terima Kasih, {{ $booking->customer_name }}!</h1>
        <p class="text-xs sm:text-sm text-slate-400 mt-2">
            Permintaan reservasi Anda telah tercatat di sistem kami dengan status <span class="text-amber-400 font-bold uppercase">Pending Confirmation</span>.
        </p>
    </div>

    <!-- Booking Summary Card -->
    <div class="bg-[#0c1222] border border-slate-800 rounded-3xl p-6 sm:p-8 shadow-2xl relative overflow-hidden mb-8">
        <!-- Ticket Header -->
        <div class="flex items-center justify-between pb-6 border-b border-dashed border-slate-800">
            <div>
                <span class="text-[10px] uppercase font-bold text-slate-500 tracking-wider">Kode Booking</span>
                <p class="text-xl font-mono font-black text-amber-400 mt-0.5">{{ $booking->booking_code }}</p>
            </div>
            <div class="text-right">
                <span class="text-[10px] uppercase font-bold text-slate-500 tracking-wider">Status</span>
                <span class="inline-block mt-0.5 px-2.5 py-1 rounded text-[11px] font-extrabold uppercase bg-amber-500/20 text-amber-300 border border-amber-500/30">
                    Menunggu Konfirmasi
                </span>
            </div>
        </div>

        <!-- Details Grid -->
        <div class="py-6 space-y-4 text-xs border-b border-dashed border-slate-800">
            <div class="flex justify-between">
                <span class="text-slate-400">Kamar Karaoke</span>
                <span class="font-bold text-white">{{ $booking->room->room_number }} - {{ $booking->room->name }} ({{ ucfirst($booking->room->type) }})</span>
            </div>
            @if($booking->package)
                <div class="flex justify-between">
                    <span class="text-slate-400">Paket Bundling</span>
                    <span class="font-bold text-amber-300">{{ $booking->package->name }}</span>
                </div>
            @endif
            <div class="flex justify-between">
                <span class="text-slate-400">Jadwal Kedatangan</span>
                <span class="font-bold text-white">{{ $booking->start_time->format('d M Y, H:i') }} WIB</span>
            </div>
            <div class="flex justify-between">
                <span class="text-slate-400">Durasi Sesi</span>
                <span class="font-bold text-white">{{ $booking->duration_hours }} Jam</span>
            </div>

            @if($booking->include_lc && $booking->lc_qty > 0)
                <div class="flex justify-between">
                    <span class="text-slate-400">Layanan Pemandu Lagu (LC)</span>
                    <span class="font-bold text-pink-400">{{ $booking->lc_qty }} Orang x {{ $booking->lc_hours }} Jam</span>
                </div>
            @endif

            @if($booking->items->count() > 0)
                <div class="pt-2">
                    <span class="text-[11px] font-bold text-slate-300 uppercase block mb-2">Pre-Order Minuman & F&B:</span>
                    <ul class="space-y-1.5 pl-2 border-l border-slate-800">
                        @foreach($booking->items as $it)
                            <li class="flex justify-between text-slate-400">
                                <span>{{ $it->product->name }} (x{{ $it->quantity }})</span>
                                <span class="font-mono text-slate-200">Rp {{ number_format($it->subtotal, 0, ',', '.') }}</span>
                            </li>
                        @endforeach
                    </ul>
                </div>
            @endif

            @if($booking->notes)
                <div class="pt-2">
                    <span class="text-slate-400 block mb-1">Catatan Tambahan:</span>
                    <p class="text-slate-300 italic bg-slate-900/60 p-2.5 rounded-lg border border-slate-800">{{ $booking->notes }}</p>
                </div>
            @endif
        </div>

        <!-- Total Price -->
        <div class="pt-6 flex items-center justify-between">
            <div>
                <span class="text-[10px] uppercase font-bold text-slate-500">Estimasi Total Bayar</span>
                <p class="text-2xl font-black text-amber-400 font-mono mt-0.5">{{ $booking->formatted_total }}</p>
            </div>
            <span class="text-[10px] text-slate-500 text-right max-w-[150px]">Pembayaran lunas dapat dilakukan di kasir saat tiba.</span>
        </div>
    </div>

    <!-- WhatsApp Action Button -->
    <div class="space-y-3">
        <a href="{{ $whatsappUrl }}" target="_blank"
            class="w-full py-4 px-6 rounded-2xl bg-gradient-to-r from-emerald-500 to-green-600 hover:from-emerald-400 hover:to-green-500 text-white font-black text-sm shadow-xl shadow-emerald-500/30 flex items-center justify-center transition-all hover:scale-[1.02] border border-emerald-400/40">
            <i class="fa-brands fa-whatsapp text-2xl mr-3"></i>
            <span>Kirim Konfirmasi ke WhatsApp Kasir / Admin</span>
        </a>

        <div class="flex items-center justify-center space-x-4 pt-2">
            <a href="{{ route('home') }}" class="text-xs text-slate-400 hover:text-white transition-colors">
                <i class="fa-solid fa-house mr-1"></i> Kembali ke Beranda
            </a>
            <span class="text-slate-700">|</span>
            <button onclick="window.print()" class="text-xs text-purple-400 hover:text-purple-300 transition-colors">
                <i class="fa-solid fa-print mr-1"></i> Cetak Bukti Reservasi
            </button>
        </div>
    </div>
</div>
@endsection

