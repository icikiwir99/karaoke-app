@extends('layouts.admin')

@section('title', 'Laporan Keuangan & Margin Produk (Miras & F&B) - Owner Area')
@section('header_title', 'Laporan Keuangan & Margin Produk')
@section('header_subtitle', 'Analisis laba kotor, rincian omset per pos layanan, dan ekspor data penjualan')

@section('content')
<div class="space-y-8">
    <!-- Filter Bar & Export Actions -->
    <div class="bg-[#0c1222] border border-slate-800 rounded-2xl p-4 flex flex-col md:flex-row md:items-center justify-between gap-4">
        <form action="{{ route('owner.reports') }}" method="GET" class="flex flex-wrap items-center gap-3 text-xs">
            <span class="text-slate-400 font-bold uppercase text-[10px]">Filter Periode:</span>
            <div class="flex items-center space-x-1.5">
                <input type="date" name="start_date" value="{{ $startDate }}"
                    class="px-3 py-1.5 bg-slate-900 border border-slate-700 rounded-xl text-white focus:outline-none focus:border-amber-400 text-xs">
                <span class="text-slate-500">s/d</span>
                <input type="date" name="end_date" value="{{ $endDate }}"
                    class="px-3 py-1.5 bg-slate-900 border border-slate-700 rounded-xl text-white focus:outline-none focus:border-amber-400 text-xs">
            </div>
            <button type="submit" class="px-4 py-1.5 bg-purple-600 hover:bg-purple-500 text-white rounded-xl font-bold transition-all">
                <i class="fa-solid fa-filter mr-1"></i> Terapkan
            </button>
        </form>

        <div class="flex items-center space-x-2">
            <a href="{{ route('owner.reports.exportCsv', ['start_date' => $startDate, 'end_date' => $endDate]) }}"
                class="px-4 py-2 bg-emerald-600 hover:bg-emerald-500 text-white rounded-xl text-xs font-bold transition-all shadow-lg shadow-emerald-600/20 flex items-center">
                <i class="fa-solid fa-file-excel mr-1.5"></i> Ekspor CSV / Excel
            </a>
            <button onclick="window.print()" class="px-3.5 py-2 bg-slate-800 hover:bg-slate-700 text-slate-200 rounded-xl text-xs font-bold transition-all">
                <i class="fa-solid fa-print mr-1.5"></i> Cetak Laporan
            </button>
        </div>
    </div>

    <!-- Revenue Breakdown Summary KPIs -->
    <div class="grid grid-cols-2 md:grid-cols-5 gap-4">
        <div class="bg-[#0c1222] border border-amber-500/30 rounded-2xl p-4 shadow-xl">
            <span class="text-[9px] uppercase font-bold text-amber-400 tracking-wider">Gross Omset Bersih</span>
            <p class="text-xl font-black text-amber-300 font-mono mt-1">Rp {{ number_format($summary['gross_revenue'], 0, ',', '.') }}</p>
        </div>
        <div class="bg-[#0c1222] border border-slate-800 rounded-2xl p-4 shadow-xl">
            <span class="text-[9px] uppercase font-bold text-cyan-400 tracking-wider">Omset Sewa Room</span>
            <p class="text-xl font-black text-white font-mono mt-1">Rp {{ number_format($summary['total_room_rev'], 0, ',', '.') }}</p>
        </div>
        <div class="bg-[#0c1222] border border-slate-800 rounded-2xl p-4 shadow-xl">
            <span class="text-[9px] uppercase font-bold text-purple-400 tracking-wider">Omset F&B & Miras</span>
            <p class="text-xl font-black text-white font-mono mt-1">Rp {{ number_format($summary['total_fnb_rev'], 0, ',', '.') }}</p>
        </div>
        <div class="bg-[#0c1222] border border-slate-800 rounded-2xl p-4 shadow-xl">
            <span class="text-[9px] uppercase font-bold text-pink-400 tracking-wider">Omset Layanan LC</span>
            <p class="text-xl font-black text-white font-mono mt-1">Rp {{ number_format($summary['total_service_rev'], 0, ',', '.') }}</p>
        </div>
        <div class="bg-[#0c1222] border border-slate-800 rounded-2xl p-4 shadow-xl">
            <span class="text-[9px] uppercase font-bold text-rose-400 tracking-wider">Total Diskon Diberikan</span>
            <p class="text-xl font-black text-rose-400 font-mono mt-1">Rp {{ number_format($summary['total_discounts'], 0, ',', '.') }}</p>
        </div>
    </div>

    <!-- SPIRITS & MIRAS PROFIT MARGIN ANALYSIS TABLE -->
    <div class="bg-[#0c1222] border border-slate-800 rounded-2xl p-6 shadow-xl">
        <div class="mb-4">
            <span class="text-[10px] font-bold uppercase tracking-wider text-amber-400">ANALISIS LABA KOTOR PRODUK</span>
            <h3 class="text-base font-bold text-white mt-0.5">Laporan Margin Keuntungan Minuman / Miras Terlaris</h3>
            <p class="text-xs text-slate-400 mt-1">Rincian perbandingan harga jual, harga pokok penjualan (COGS), dan profit margin bersih yang dihasilkan.</p>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-left text-xs text-slate-300">
                <thead class="bg-slate-900/80 text-[10px] uppercase font-bold text-slate-400 border-b border-slate-800">
                    <tr>
                        <th class="py-3 px-4">Nama Produk Menu</th>
                        <th class="py-3 px-4 text-center">Qty Terjual</th>
                        <th class="py-3 px-4 text-right">Rata-rata Harga Jual</th>
                        <th class="py-3 px-4 text-right">Harga Modal (COGS)</th>
                        <th class="py-3 px-4 text-right">Total Pendapatan</th>
                        <th class="py-3 px-4 text-right">Total Modal Bahan</th>
                        <th class="py-3 px-4 text-right">Total Laba Kotor (Profit)</th>
                        <th class="py-3 px-4 text-center">Margin %</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-800/60">
                    @forelse($alcoholMargins as $m)
                        @php
                            $marginPercent = $m->total_cost > 0 ? round(($m->total_profit / $m->total_cost) * 100, 1) : 100;
                        @endphp
                        <tr class="hover:bg-slate-900/40 transition-colors">
                            <td class="py-3.5 px-4 font-bold text-white flex items-center space-x-2">
                                <i class="fa-solid fa-wine-bottle text-amber-400 text-xs"></i>
                                <span>{{ $m->item_name }}</span>
                            </td>
                            <td class="py-3.5 px-4 text-center font-bold text-slate-200 font-mono">{{ $m->total_qty }}</td>
                            <td class="py-3.5 px-4 text-right font-mono text-slate-300">Rp {{ number_format($m->avg_price, 0, ',', '.') }}</td>
                            <td class="py-3.5 px-4 text-right font-mono text-slate-400">Rp {{ number_format($m->avg_cogs, 0, ',', '.') }}</td>
                            <td class="py-3.5 px-4 text-right font-mono font-bold text-white">Rp {{ number_format($m->total_revenue, 0, ',', '.') }}</td>
                            <td class="py-3.5 px-4 text-right font-mono text-slate-400">Rp {{ number_format($m->total_cost, 0, ',', '.') }}</td>
                            <td class="py-3.5 px-4 text-right font-mono font-black text-emerald-400 text-sm">
                                + Rp {{ number_format($m->total_profit, 0, ',', '.') }}
                            </td>
                            <td class="py-3.5 px-4 text-center">
                                <span class="px-2 py-0.5 rounded text-[10px] font-black uppercase font-mono bg-amber-500/20 text-amber-300 border border-amber-500/30">
                                    {{ $marginPercent }}%
                                </span>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" class="py-8 text-center text-slate-500">Belum ada penjualan F&B/Miras pada rentang tanggal ini.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <!-- Detailed Sales Transactions Log -->
    <div class="bg-[#0c1222] border border-slate-800 rounded-2xl p-6 shadow-xl">
        <div class="flex items-center justify-between mb-4">
            <div>
                <h3 class="text-base font-bold text-white">Daftar Transaksi Kasir</h3>
                <p class="text-xs text-slate-400">Rincian seluruh struk pembayaran yang telah lunas</p>
            </div>
            <span class="text-xs text-slate-400">{{ $sales->total() }} Transaksi Ditemukan</span>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-left text-xs text-slate-300">
                <thead class="bg-slate-900/80 text-[10px] uppercase font-bold text-slate-400 border-b border-slate-800">
                    <tr>
                        <th class="py-3 px-4">No. Invoice</th>
                        <th class="py-3 px-4">Waktu</th>
                        <th class="py-3 px-4">Kasir</th>
                        <th class="py-3 px-4">Customer</th>
                        <th class="py-3 px-4">Room</th>
                        <th class="py-3 px-4 text-right">Sewa Room</th>
                        <th class="py-3 px-4 text-right">F&B / Miras</th>
                        <th class="py-3 px-4 text-right">Layanan LC</th>
                        <th class="py-3 px-4 text-right">Grand Total</th>
                        <th class="py-3 px-4 text-center">Metode</th>
                        <th class="py-3 px-4 text-center">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-800/60">
                    @forelse($sales as $sale)
                        <tr class="hover:bg-slate-900/40 transition-colors">
                            <td class="py-3 px-4 font-mono font-bold text-amber-400">{{ $sale->invoice_number }}</td>
                            <td class="py-3 px-4">{{ $sale->created_at->format('d/m/Y H:i') }}</td>
                            <td class="py-3 px-4 font-semibold text-slate-300">{{ $sale->user ? $sale->user->name : '-' }}</td>
                            <td class="py-3 px-4 font-bold text-white">{{ $sale->customer_name }}</td>
                            <td class="py-3 px-4">
                                @if($sale->room)
                                    <span class="px-2 py-0.5 rounded bg-slate-800 border border-slate-700 font-mono font-bold">{{ $sale->room->room_number }}</span>
                                @else
                                    <span class="text-slate-500">-</span>
                                @endif
                            </td>
                            <td class="py-3 px-4 text-right font-mono text-slate-300">Rp {{ number_format($sale->total_room, 0, ',', '.') }}</td>
                            <td class="py-3 px-4 text-right font-mono text-slate-300">Rp {{ number_format($sale->total_fnb, 0, ',', '.') }}</td>
                            <td class="py-3 px-4 text-right font-mono text-slate-300">Rp {{ number_format($sale->total_service, 0, ',', '.') }}</td>
                            <td class="py-3 px-4 text-right font-mono font-black text-white">
                                {{ $sale->formatted_grand_total }}
                            </td>
                            <td class="py-3 px-4 text-center">
                                @if($sale->is_complimentary)
                                    <span class="px-2 py-0.5 rounded text-[10px] font-black uppercase tracking-wider bg-amber-500/20 text-amber-300 border border-amber-500/30" title="{{ $sale->complimentary_reason }}">
                                        VIP Free
                                    </span>
                                @else
                                    <span class="px-2 py-0.5 rounded text-[10px] font-bold uppercase {{ $sale->payment_method === 'cash' ? 'bg-emerald-500/20 text-emerald-300 border border-emerald-500/30' : 'bg-blue-500/20 text-blue-300 border border-blue-500/30' }}">
                                        {{ $sale->payment_method }}
                                    </span>
                                @endif
                            </td>
                            <td class="py-3 px-4 text-center">
                                <a href="{{ route('kasir.thermalReceipt', $sale->id) }}" target="_blank"
                                    class="p-1.5 bg-slate-800 hover:bg-purple-600 text-slate-300 hover:text-white rounded-lg text-xs transition-all inline-block"
                                    title="Lihat Struk Thermal">
                                    <i class="fa-solid fa-print"></i>
                                </a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="11" class="py-8 text-center text-slate-500">Belum ada transaksi pada periode ini.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="mt-6">
            {{ $sales->links() }}
        </div>
    </div>
</div>
@endsection
