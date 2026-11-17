<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Struk Pembayaran - {{ $sale->invoice_number }}</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=JetBrains+Mono:wght@400;600;800&family=Plus+Jakarta+Sans:wght@400;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">

    <style>
        body {
            font-family: 'JetBrains Mono', monospace;
            background-color: #090d16;
            color: #000;
        }
        @media print {
            body {
                background-color: #fff !important;
                color: #000 !important;
                margin: 0 !important;
                padding: 0 !important;
            }
            .no-print {
                display: none !important;
            }
            .receipt-box {
                box-shadow: none !important;
                border: none !important;
                width: 100% !important;
                max-width: 80mm !important;
                margin: 0 auto !important;
                padding: 0 !important;
            }
        }
    </style>
</head>
<body class="min-h-screen py-8 flex flex-col items-center justify-center font-mono">

    <!-- Top Action Bar (No Print) -->
    <div class="no-print max-w-[80mm] w-full mb-4 flex items-center justify-between px-2">
        <a href="{{ route('kasir.pos') }}" class="px-3 py-1.5 rounded-xl bg-slate-800 text-slate-300 hover:text-white text-xs font-sans font-semibold transition-all">
            <i class="fa-solid fa-arrow-left mr-1"></i> Kembali ke POS
        </a>
        <button onclick="window.print()" class="px-4 py-1.5 rounded-xl bg-purple-600 hover:bg-purple-500 text-white text-xs font-sans font-bold shadow-lg shadow-purple-600/30 transition-all">
            <i class="fa-solid fa-print mr-1"></i> Cetak Struk
        </button>
    </div>

    <!-- Thermal Receipt Paper (80mm Width) -->
    <div class="receipt-box w-full max-w-[80mm] bg-white text-black p-5 shadow-2xl rounded-sm text-xs leading-tight">
        <!-- Receipt Header -->
        <div class="text-center pb-3 border-b border-dashed border-gray-400">
            <h1 class="text-base font-extrabold tracking-wider uppercase">GRAND KARAOKE</h1>
            <p class="text-[10px] tracking-widest font-bold uppercase">& EXECUTIVE LOUNGE</p>
            <p class="text-[9px] text-gray-700 mt-1">Jl. Boulevard Utama No. 88, Golden City</p>
            <p class="text-[9px] text-gray-700">Telp / WA: 0812-3456-7890</p>
        </div>

        <!-- Transaction Meta -->
        <div class="py-2.5 border-b border-dashed border-gray-400 text-[10px] space-y-1">
            <div class="flex justify-between">
                <span>No. Inv:</span>
                <span class="font-bold">{{ $sale->invoice_number }}</span>
            </div>
            <div class="flex justify-between">
                <span>Waktu :</span>
                <span>{{ $sale->created_at->format('d/m/Y H:i:s') }}</span>
            </div>
            <div class="flex justify-between">
                <span>Kasir :</span>
                <span>{{ $sale->user ? $sale->user->name : 'Staff Kasir' }}</span>
            </div>
            <div class="flex justify-between">
                <span>Tamu  :</span>
                <span class="font-bold">{{ $sale->customer_name }}</span>
            </div>
            @if($sale->room)
                <div class="flex justify-between">
                    <span>Kamar :</span>
                    <span class="font-bold">Room {{ $sale->room->room_number }} ({{ ucfirst($sale->room->type) }})</span>
                </div>
            @endif
        </div>

        <!-- Itemized Breakdown (Transparent per Category) -->
        <div class="py-2.5 border-b border-dashed border-gray-400 space-y-2">
            <!-- 1. Room Items -->
            @php
                $roomItems = $sale->items->where('item_type', 'room');
                $fnbItems = $sale->items->where('item_type', 'fnb');
                $serviceItems = $sale->items->where('item_type', 'service');
            @endphp

            @if($roomItems->count() > 0)
                <div>
                    <span class="text-[9px] font-bold uppercase tracking-wider block border-b border-gray-200 pb-0.5 mb-1">[SEWA KAMAR / PAKET]</span>
                    @foreach($roomItems as $rItem)
                        <div class="text-[10px]">
                            <div class="font-semibold">{{ $rItem->item_name }}</div>
                            <div class="flex justify-between text-gray-700 pl-2">
                                <span>{{ $rItem->quantity }}x @ {{ number_format($rItem->price, 0, ',', '.') }}</span>
                                <span class="font-bold text-black">{{ number_format($rItem->subtotal, 0, ',', '.') }}</span>
                            </div>
                        </div>
                    @endforeach
                </div>
            @endif

            <!-- 2. F&B & Spirits Items -->
            @if($fnbItems->count() > 0)
                <div class="pt-1">
                    <span class="text-[9px] font-bold uppercase tracking-wider block border-b border-gray-200 pb-0.5 mb-1">[F&B & SPIRITS / MIRAS]</span>
                    @foreach($fnbItems as $fItem)
                        <div class="text-[10px]">
                            <div class="font-semibold">{{ $fItem->item_name }}</div>
                            <div class="flex justify-between text-gray-700 pl-2">
                                <span>{{ $fItem->quantity }}x @ {{ number_format($fItem->price, 0, ',', '.') }}</span>
                                <span class="font-bold text-black">{{ number_format($fItem->subtotal, 0, ',', '.') }}</span>
                            </div>
                        </div>
                    @endforeach
                </div>
            @endif

            <!-- 3. Companion (LC) Service Items -->
            @if($serviceItems->count() > 0)
                <div class="pt-1">
                    <span class="text-[9px] font-bold uppercase tracking-wider block border-b border-gray-200 pb-0.5 mb-1">[LAYANAN PEMANDU LAGU (LC)]</span>
                    @foreach($serviceItems as $sItem)
                        <div class="text-[10px]">
                            <div class="font-semibold">{{ $sItem->item_name }}</div>
                            <div class="flex justify-between text-gray-700 pl-2">
                                <span>{{ $sItem->quantity }} Jam @ {{ number_format($sItem->price, 0, ',', '.') }}</span>
                                <span class="font-bold text-black">{{ number_format($sItem->subtotal, 0, ',', '.') }}</span>
                            </div>
                        </div>
                    @endforeach
                </div>
            @endif
        </div>

        <!-- Subtotals & Grand Total -->
        <div class="py-2.5 border-b border-dashed border-gray-400 space-y-1 text-[11px]">
            <div class="flex justify-between text-gray-700">
                <span>Subtotal Room:</span>
                <span>Rp {{ number_format($sale->total_room, 0, ',', '.') }}</span>
            </div>
            <div class="flex justify-between text-gray-700">
                <span>Subtotal F&B:</span>
                <span>Rp {{ number_format($sale->total_fnb, 0, ',', '.') }}</span>
            </div>
            <div class="flex justify-between text-gray-700">
                <span>Subtotal LC:</span>
                <span>Rp {{ number_format($sale->total_service, 0, ',', '.') }}</span>
            </div>

            @if($sale->discount > 0)
                <div class="flex justify-between text-red-600 font-bold">
                    <span>Diskon:</span>
                    <span>- Rp {{ number_format($sale->discount, 0, ',', '.') }}</span>
                </div>
            @endif

            <div class="flex justify-between text-xs font-black pt-1 border-t border-gray-300">
                <span>GRAND TOTAL:</span>
                <span>{{ $sale->formatted_grand_total }}</span>
            </div>
        </div>

        <!-- Payment Info -->
        <div class="py-2.5 border-b border-dashed border-gray-400 space-y-1 text-[10px]">
            <div class="flex justify-between">
                <span>Metode Bayar:</span>
                <span class="font-bold uppercase">{{ $sale->payment_method }}</span>
            </div>

            @if($sale->is_complimentary)
                <div class="p-1.5 bg-gray-100 rounded text-center font-bold text-[9px] uppercase">
                    *** COMPLIMENTARY 100% OWNER ***<br>
                    <span class="font-normal">{{ $sale->complimentary_reason ?? 'Otorisasi Khusus' }}</span>
                </div>
            @else
                <div class="flex justify-between">
                    <span>Tunai / Bayar:</span>
                    <span>Rp {{ number_format($sale->paid_amount, 0, ',', '.') }}</span>
                </div>
                <div class="flex justify-between font-bold">
                    <span>Kembalian:</span>
                    <span>Rp {{ number_format($sale->change_amount, 0, ',', '.') }}</span>
                </div>
            @endif
        </div>

        <!-- Receipt Footer -->
        <div class="text-center pt-3 text-[9px] text-gray-600 space-y-1">
            <p class="font-bold uppercase">Terima Kasih Atas Kunjungan Anda!</p>
            <p>Barang yang sudah dibeli tidak dapat ditukar/dikembalikan.</p>
            <p class="pt-1 text-[8px] text-gray-400">=== GRAND KARAOKE & LOUNGE POS ===</p>
        </div>
    </div>

    <!-- Auto Print Script -->
    <script>
        window.addEventListener('load', function() {
            // Optional slight delay before print dialog
            // window.print();
        });
    </script>
</body>
</html>
