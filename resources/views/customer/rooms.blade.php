@extends('layouts.app')

@section('title', 'Katalog Tipe Kamar Karaoke - Grand Karaoke')

@section('content')
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-12">
    <!-- Breadcrumb & Header -->
    <div class="mb-10 text-center max-w-3xl mx-auto">
        <span class="text-xs font-bold uppercase tracking-wider text-purple-400">PILIHAN TIPE RUANGAN</span>
        <h1 class="text-3xl sm:text-5xl font-black text-white mt-1">Katalog Kamar Karaoke & Lounge</h1>
        <p class="text-sm text-slate-400 mt-3 leading-relaxed">
            Seluruh kamar kami dirancang dengan akustik kedap suara berkualitas studio, pencahayaan LED ambiens dinamis, serta sound system profesional dari JBL, Bose, dan Meyer Sound.
        </p>
    </div>

    <!-- Tier Filter Tabs & Rooms List -->
    <div class="space-y-16">
        @php
            $tierLabels = [
                'small' => ['title' => 'Small Room (Kapasitas 2-4 Orang)', 'desc' => 'Pilihan tepat untuk hangout santai berdua atau bersama beberapa teman terdekat.', 'badge' => 'POPULER'],
                'medium' => ['title' => 'Medium Room (Kapasitas 6-8 Orang)', 'desc' => 'Ruangan favorit untuk pesta keluarga kecil, reuni akrab, atau arisan seru.', 'badge' => 'BEST SELLER'],
                'large' => ['title' => 'Large Room (Kapasitas 12-16 Orang)', 'desc' => 'Kamar luas dengan panggung mini dan efek lighting konser untuk grup besar.', 'badge' => 'PARTY SIZE'],
                'vip' => ['title' => 'Imperial VIP Suite (Kapasitas 20-25 Orang)', 'desc' => 'Suite privat termewah dengan private bar, toilet dalam eksklusif, dan pelayan pribadi.', 'badge' => 'ULTRA LUXURY'],
            ];
        @endphp

        @foreach(['small', 'medium', 'large', 'vip'] as $typeKey)
            @if(isset($rooms[$typeKey]))
                <div class="border-t border-slate-800/80 pt-10">
                    <div class="flex flex-col sm:flex-row sm:items-center justify-between mb-6">
                        <div>
                            <div class="flex items-center space-x-3">
                                <h2 class="text-2xl font-bold text-white">{{ $tierLabels[$typeKey]['title'] }}</h2>
                                <span class="px-2.5 py-0.5 rounded text-[10px] font-black uppercase tracking-wider bg-purple-500/20 text-purple-300 border border-purple-500/30">
                                    {{ $tierLabels[$typeKey]['badge'] }}
                                </span>
                            </div>
                            <p class="text-xs text-slate-400 mt-1">{{ $tierLabels[$typeKey]['desc'] }}</p>
                        </div>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                        @foreach($rooms[$typeKey] as $room)
                            <div class="bg-[#0c1222] border border-slate-800 rounded-2xl overflow-hidden hover:border-purple-500/50 transition-all flex flex-col justify-between group shadow-xl">
                                <div>
                                    <!-- Image / Visual Banner -->
                                    <div class="h-44 bg-gradient-to-tr from-slate-900 via-slate-800 to-purple-950 flex flex-col items-center justify-center p-6 relative overflow-hidden">
                                        <div class="w-16 h-16 rounded-2xl bg-purple-600/20 border border-purple-500/30 flex items-center justify-center text-purple-400 mb-2 group-hover:scale-110 transition-transform">
                                            <i class="fa-solid fa-door-open text-2xl"></i>
                                        </div>
                                        <span class="text-xl font-black text-white font-mono tracking-wider">{{ $room->room_number }}</span>
                                        <span class="text-xs text-purple-300 font-semibold">{{ $room->name }}</span>

                                        <!-- Status Badge -->
                                        <div class="absolute top-3 right-3">
                                            @if($room->status === 'available')
                                                <span class="px-2.5 py-1 rounded-md text-[10px] font-bold uppercase tracking-wider bg-emerald-500/20 text-emerald-400 border border-emerald-500/30">
                                                    Ready / Available
                                                </span>
                                            @elseif($room->status === 'occupied')
                                                <span class="px-2.5 py-1 rounded-md text-[10px] font-bold uppercase tracking-wider bg-rose-500/20 text-rose-400 border border-rose-500/30">
                                                    Sedang Digunakan
                                                </span>
                                            @elseif($room->status === 'cleaning')
                                                <span class="px-2.5 py-1 rounded-md text-[10px] font-bold uppercase tracking-wider bg-cyan-500/20 text-cyan-400 border border-cyan-500/30">
                                                    Pembersihan
                                                </span>
                                            @else
                                                <span class="px-2.5 py-1 rounded-md text-[10px] font-bold uppercase tracking-wider bg-slate-500/20 text-slate-400 border border-slate-500/30">
                                                    Maintenance
                                                </span>
                                            @endif
                                        </div>
                                    </div>

                                    <!-- Content -->
                                    <div class="p-6">
                                        <div class="flex items-center justify-between pb-4 border-b border-slate-800">
                                            <div>
                                                <span class="text-[10px] uppercase font-bold text-slate-500">Tarif Sewa</span>
                                                <p class="text-xl font-extrabold text-amber-400 font-mono">{{ $room->formatted_price }} <span class="text-xs text-slate-400 font-sans font-normal">/ Jam</span></p>
                                            </div>
                                            <div class="text-right">
                                                <span class="text-[10px] uppercase font-bold text-slate-500">Kapasitas</span>
                                                <p class="text-sm font-bold text-slate-200"><i class="fa-solid fa-users text-purple-400 mr-1"></i> Max {{ $room->capacity }} Org</p>
                                            </div>
                                        </div>

                                        <p class="text-xs text-slate-400 mt-4 leading-relaxed line-clamp-2">{{ $room->description }}</p>

                                        <!-- Facilities Tags -->
                                        <div class="mt-4">
                                            <span class="text-[10px] font-bold uppercase tracking-wider text-slate-500 block mb-2">Fasilitas Utama:</span>
                                            <div class="flex flex-wrap gap-1.5">
                                                @foreach($room->facilities_array as $fac)
                                                    <span class="bg-slate-800/80 text-slate-300 text-[10px] px-2 py-0.5 rounded border border-slate-700/60">
                                                        {{ $fac }}
                                                    </span>
                                                @endforeach
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <!-- Action Button -->
                                <div class="p-6 pt-0">
                                    <a href="{{ route('customer.reservation') }}?room_id={{ $room->id }}"
                                        class="w-full py-2.5 px-4 rounded-xl bg-gradient-to-r from-purple-600 to-indigo-600 hover:from-purple-500 hover:to-indigo-500 text-white font-bold text-xs text-center block transition-all shadow-lg shadow-purple-600/20">
                                        Pilih Kamar {{ $room->room_number }} &rarr;
                                    </a>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            @endif
        @endforeach
    </div>
</div>
@endsection
