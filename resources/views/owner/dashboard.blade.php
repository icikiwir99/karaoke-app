@extends('layouts.admin')

@section('title', 'Dashboard Eksekutif & Analitik Bisnis - Owner Area')
@section('header_title', 'Dashboard Eksekutif & Analitik Bisnis')
@section('header_subtitle', 'Analisis performa omset, jam sibuk (peak hours), stok kritis, dan privilege kontrol')

@section('content')
<div class="space-y-8">
    <!-- Top Executive KPI Summary -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6">
        <!-- Total Omset Keseluruhan -->
        <div class="bg-[#0c1222] border border-amber-500/30 rounded-2xl p-5 shadow-xl flex items-center space-x-4">
            <div class="w-12 h-12 rounded-xl bg-amber-500/20 border border-amber-500/30 text-amber-400 flex items-center justify-center text-xl shrink-0">
                <i class="fa-solid fa-vault"></i>
            </div>
            <div>
                <span class="text-[10px] uppercase font-bold text-amber-400 tracking-wider">Total Omset Akumulatif</span>
                <p class="text-2xl font-black text-amber-300 font-mono mt-0.5">Rp {{ number_format($totalRevenue, 0, ',', '.') }}</p>
                <span class="text-[10px] text-slate-500">{{ $totalTransactions }} Transaksi Tercatat</span>
            </div>
        </div>

        <!-- Omset Bulan Ini -->
        <div class="bg-[#0c1222] border border-slate-800 rounded-2xl p-5 shadow-xl flex items-center space-x-4">
            <div class="w-12 h-12 rounded-xl bg-purple-600/20 border border-purple-500/30 text-purple-400 flex items-center justify-center text-xl shrink-0">
                <i class="fa-solid fa-calendar-check"></i>
            </div>
            <div>
                <span class="text-[10px] uppercase font-bold text-slate-400 tracking-wider">Omset Bulan Ini</span>
                <p class="text-2xl font-black text-white font-mono mt-0.5">Rp {{ number_format($thisMonthRevenue, 0, ',', '.') }}</p>
                <span class="text-[10px] text-purple-400 font-semibold">Bulan {{ date('F Y') }}</span>
            </div>
        </div>

        <!-- Omset Hari Ini -->
        <div class="bg-[#0c1222] border border-slate-800 rounded-2xl p-5 shadow-xl flex items-center space-x-4">
            <div class="w-12 h-12 rounded-xl bg-emerald-600/20 border border-emerald-500/30 text-emerald-400 flex items-center justify-center text-xl shrink-0">
                <i class="fa-solid fa-cash-register"></i>
            </div>
            <div>
                <span class="text-[10px] uppercase font-bold text-slate-400 tracking-wider">Omset Hari Ini</span>
                <p class="text-2xl font-black text-emerald-300 font-mono mt-0.5">Rp {{ number_format($todayRevenue, 0, ',', '.') }}</p>
                <span class="text-[10px] text-slate-500">{{ date('d M Y') }}</span>
            </div>
        </div>

        <!-- Kamar Terisi / Occupancy Rate -->
        @php
            $occupancyRate = $totalRooms > 0 ? round(($occupiedRooms / $totalRooms) * 100, 1) : 0;
        @endphp
        <div class="bg-[#0c1222] border border-slate-800 rounded-2xl p-5 shadow-xl flex items-center space-x-4">
            <div class="w-12 h-12 rounded-xl bg-rose-600/20 border border-rose-500/30 text-rose-400 flex items-center justify-center text-xl shrink-0">
                <i class="fa-solid fa-chart-pie"></i>
            </div>
            <div>
                <span class="text-[10px] uppercase font-bold text-slate-400 tracking-wider">Occupancy Rate</span>
                <p class="text-2xl font-black text-white font-mono mt-0.5">{{ $occupancyRate }}%</p>
                <span class="text-[10px] text-rose-400">{{ $occupiedRooms }} dari {{ $totalRooms }} Kamar Terisi</span>
            </div>
        </div>
    </div>

    <!-- STOCK CONTROL & LOW STOCK ALERT WIDGET -->
    @if($lowStockProducts->count() > 0)
        <div class="bg-rose-950/30 border border-rose-500/40 rounded-2xl p-5 shadow-xl">
            <div class="flex items-center justify-between mb-3">
                <div class="flex items-center space-x-2 text-rose-300">
                    <span class="w-3 h-3 rounded-full bg-rose-500 animate-ping"></span>
                    <i class="fa-solid fa-triangle-exclamation text-base"></i>
                    <h3 class="text-sm font-extrabold uppercase tracking-wide">Stock Alert: Minuman & F&B Menipis di Gudang / Bar!</h3>
                </div>
                <a href="{{ route('owner.products') }}?low_stock=1" class="text-xs font-bold text-rose-300 hover:text-white underline">
                    Kelola Stok Master &rarr;
                </a>
            </div>
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-3">
                @foreach($lowStockProducts as $lowP)
                    <div class="bg-[#0c1222]/90 border border-rose-500/30 p-3 rounded-xl flex items-center justify-between text-xs">
                        <div>
                            <span class="text-[9px] uppercase font-bold text-amber-400 block">{{ $lowP->category }}</span>
                            <span class="font-bold text-white block truncate max-w-[150px]">{{ $lowP->name }}</span>
                            <span class="text-[10px] text-slate-400">Min. Batas: {{ $lowP->min_stock }} {{ $lowP->unit }}</span>
                        </div>
                        <div class="text-right">
                            <span class="text-base font-black text-rose-400 font-mono">{{ $lowP->stock }}</span>
                            <span class="text-[9px] text-slate-400 block">{{ $lowP->unit }} tersisa</span>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    @endif

    <!-- Analytical Charts Grid (Chart.js) -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        <!-- 1. Daily Revenue Trend (Last 7 Days) -->
        <div class="bg-[#0c1222] border border-slate-800 rounded-2xl p-6 shadow-xl flex flex-col justify-between">
            <div class="flex items-center justify-between mb-4">
                <div>
                    <h3 class="text-base font-bold text-white flex items-center">
                        <i class="fa-solid fa-chart-line text-purple-400 mr-2"></i> Tren Pendapatan Harian (7 Hari Terakhir)
                    </h3>
                    <p class="text-xs text-slate-400">Performa penjualan tunai & non-tunai per tanggal</p>
                </div>
            </div>
            <div class="h-64">
                <canvas id="revenueChart"></canvas>
            </div>
        </div>

        <!-- 2. Peak Hours Analytics -->
        <div class="bg-[#0c1222] border border-slate-800 rounded-2xl p-6 shadow-xl flex flex-col justify-between">
            <div class="flex items-center justify-between mb-4">
                <div>
                    <h3 class="text-base font-bold text-white flex items-center">
                        <i class="fa-solid fa-business-time text-amber-400 mr-2"></i> Analisis Jam Sibuk (Peak Hours)
                    </h3>
                    <p class="text-xs text-slate-400">Distribusi frekuensi transaksi operasional per jam</p>
                </div>
            </div>
            <div class="h-64">
                <canvas id="peakHoursChart"></canvas>
            </div>
        </div>
    </div>

    <!-- Room Popularity & Alcohol Margins Highlights -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        <!-- Room Popularity Table -->
        <div class="bg-[#0c1222] border border-slate-800 rounded-2xl p-6 shadow-xl">
            <div class="flex items-center justify-between mb-4">
                <h3 class="text-base font-bold text-white flex items-center">
                    <i class="fa-solid fa-ranking-star text-amber-400 mr-2"></i> Statistik Kamar Paling Diminati
                </h3>
                <a href="{{ route('owner.rooms') }}" class="text-xs text-purple-400 hover:underline font-semibold">Master Kamar &rarr;</a>
            </div>

            <div class="overflow-x-auto">
                <table class="w-full text-left text-xs text-slate-300">
                    <thead class="bg-slate-900/80 text-[10px] uppercase font-bold text-slate-400 border-b border-slate-800">
                        <tr>
                            <th class="py-2.5 px-3">Kamar</th>
                            <th class="py-2.5 px-3">Tipe</th>
                            <th class="py-2.5 px-3 text-center">Total Dipakai</th>
                            <th class="py-2.5 px-3 text-right">Kontribusi Omset</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-800/60">
                        @foreach($roomStats->take(5) as $r)
                            <tr class="hover:bg-slate-900/40">
                                <td class="py-2.5 px-3 font-mono font-bold text-white">{{ $r->room_number }} - {{ $r->name }}</td>
                                <td class="py-2.5 px-3 uppercase text-[10px] text-purple-400 font-bold">{{ $r->type }}</td>
                                <td class="py-2.5 px-3 text-center font-bold text-slate-200">{{ $r->total_bookings }}x</td>
                                <td class="py-2.5 px-3 text-right font-mono font-bold text-amber-400">
                                    Rp {{ number_format($r->total_revenue ?? 0, 0, ',', '.') }}
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Top Alcohol Profit Margins -->
        <div class="bg-[#0c1222] border border-slate-800 rounded-2xl p-6 shadow-xl">
            <div class="flex items-center justify-between mb-4">
                <div>
                    <h3 class="text-base font-bold text-white flex items-center">
                        <i class="fa-solid fa-wine-glass text-amber-400 mr-2"></i> Margin Keuntungan Minuman / Miras Tertinggi
                    </h3>
                    <p class="text-xs text-slate-400">Produk spirits dengan kontribusi margin profit terbesar</p>
                </div>
                <a href="{{ route('owner.reports') }}" class="text-xs text-amber-400 hover:underline font-semibold">Laporan Lengkap &rarr;</a>
            </div>

            <div class="space-y-3">
                @foreach($topAlcoholMargin as $alc)
                    <div class="p-3 bg-slate-900/80 border border-slate-800 rounded-xl flex items-center justify-between text-xs">
                        <div class="overflow-hidden pr-2">
                            <span class="font-bold text-white block truncate">{{ $alc->name }}</span>
                            <span class="text-[10px] text-slate-400 font-mono">Beli: {{ $alc->formatted_purchase_price }} &bull; Jual: {{ $alc->formatted_price }}</span>
                        </div>
                        <div class="text-right shrink-0">
                            <span class="text-xs font-black text-emerald-400 font-mono block">+ Rp {{ number_format($alc->margin, 0, ',', '.') }}</span>
                            <span class="text-[10px] font-bold text-amber-400 bg-amber-500/10 px-1.5 py-0.2 rounded border border-amber-500/20">
                                Margin {{ $alc->margin_percentage }}%
                            </span>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
    // 1. Daily Revenue Chart
    const ctxRev = document.getElementById('revenueChart').getContext('2d');
    new Chart(ctxRev, {
        type: 'line',
        data: {
            labels: {!! json_encode($dailyDates) !!},
            datasets: [{
                label: 'Omset Harian (Rp)',
                data: {!! json_encode($dailyRevenues) !!},
                borderColor: '#a855f7',
                backgroundColor: 'rgba(168, 85, 247, 0.15)',
                borderWidth: 3,
                tension: 0.35,
                fill: true,
                pointBackgroundColor: '#fbbf24',
                pointBorderColor: '#fff',
                pointRadius: 5
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: { display: false }
            },
            scales: {
                x: {
                    grid: { color: 'rgba(255, 255, 255, 0.05)' },
                    ticks: { color: '#94a3b8' }
                },
                y: {
                    grid: { color: 'rgba(255, 255, 255, 0.05)' },
                    ticks: {
                        color: '#94a3b8',
                        callback: function(val) {
                            return 'Rp ' + (val / 1000000).toFixed(1) + 'M';
                        }
                    }
                }
            }
        }
    });

    // 2. Peak Hours Bar Chart
    const ctxPeak = document.getElementById('peakHoursChart').getContext('2d');
    new Chart(ctxPeak, {
        type: 'bar',
        data: {
            labels: {!! json_encode($hoursLabels) !!},
            datasets: [{
                label: 'Jumlah Transaksi',
                data: {!! json_encode($hoursData) !!},
                backgroundColor: 'rgba(245, 158, 11, 0.75)',
                hoverBackgroundColor: '#f59e0b',
                borderRadius: 6
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: { display: false }
            },
            scales: {
                x: {
                    grid: { color: 'rgba(255, 255, 255, 0.05)' },
                    ticks: { color: '#94a3b8' }
                },
                y: {
                    beginAtZero: true,
                    grid: { color: 'rgba(255, 255, 255, 0.05)' },
                    ticks: { color: '#94a3b8', stepSize: 1 }
                }
            }
        }
    });
</script>
@endpush
@endsection
