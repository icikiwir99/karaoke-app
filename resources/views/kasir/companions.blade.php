@extends('layouts.admin')

@section('title', 'Live Companion Tracker (Pemandu Lagu LC)')
@section('header_title', 'Live Companion Tracker')
@section('header_subtitle', 'Monitoring real-time ketersediaan personel LC dan penugasan ke kamar')

@section('content')
<div class="space-y-6">
    <!-- Companion Stats Bar -->
    <div class="grid grid-cols-1 md:grid-cols-4 gap-6">
        <div class="bg-[#0c1222] border border-slate-800 rounded-2xl p-5 shadow-xl flex items-center space-x-4">
            <div class="w-12 h-12 rounded-xl bg-purple-600/20 border border-purple-500/30 text-purple-400 flex items-center justify-center text-xl shrink-0">
                <i class="fa-solid fa-users"></i>
            </div>
            <div>
                <span class="text-[10px] uppercase font-bold text-slate-400 tracking-wider">Total Personel</span>
                <p class="text-2xl font-black text-white font-mono mt-0.5">{{ $companions->count() }} Orang</p>
            </div>
        </div>

        <div class="bg-[#0c1222] border border-emerald-500/30 rounded-2xl p-5 shadow-xl flex items-center space-x-4">
            <div class="w-12 h-12 rounded-xl bg-emerald-600/20 border border-emerald-500/30 text-emerald-400 flex items-center justify-center text-xl shrink-0">
                <i class="fa-solid fa-circle-check"></i>
            </div>
            <div>
                <span class="text-[10px] uppercase font-bold text-emerald-400 tracking-wider">Status Standby</span>
                <p class="text-2xl font-black text-emerald-300 font-mono mt-0.5">{{ $companions->where('status', 'standby')->count() }} Orang</p>
                <span class="text-[10px] text-slate-500">Siap ditugaskan ke kamar</span>
            </div>
        </div>

        <div class="bg-[#0c1222] border border-pink-500/30 rounded-2xl p-5 shadow-xl flex items-center space-x-4">
            <div class="w-12 h-12 rounded-xl bg-pink-600/20 border border-pink-500/30 text-pink-400 flex items-center justify-center text-xl shrink-0">
                <i class="fa-solid fa-person-dress"></i>
            </div>
            <div>
                <span class="text-[10px] uppercase font-bold text-pink-400 tracking-wider">In-Room (Bertugas)</span>
                <p class="text-2xl font-black text-pink-300 font-mono mt-0.5">{{ $companions->where('status', 'in_room')->count() }} Orang</p>
                <span class="text-[10px] text-slate-500">Sedang mendampingi tamu</span>
            </div>
        </div>

        <div class="bg-[#0c1222] border border-slate-700 rounded-2xl p-5 shadow-xl flex items-center space-x-4">
            <div class="w-12 h-12 rounded-xl bg-slate-800 text-slate-400 flex items-center justify-center text-xl shrink-0">
                <i class="fa-solid fa-bed"></i>
            </div>
            <div>
                <span class="text-[10px] uppercase font-bold text-slate-400 tracking-wider">Off Duty / Libur</span>
                <p class="text-2xl font-black text-slate-300 font-mono mt-0.5">{{ $companions->where('status', 'off')->count() }} Orang</p>
                <span class="text-[10px] text-slate-500">Tidak sedang bertugas</span>
            </div>
        </div>
    </div>

    <!-- Companion Grid Cards -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
        @foreach($companions as $companion)
            @php
                $statusConfig = [
                    'standby' => [
                        'border' => 'border-emerald-500/30 hover:border-emerald-400',
                        'badge' => 'bg-emerald-500/20 text-emerald-300 border-emerald-500/30',
                        'label' => 'STANDBY LOUNGE',
                        'avatar_bg' => 'from-emerald-900 to-teal-900',
                    ],
                    'in_room' => [
                        'border' => 'border-pink-500/40 hover:border-pink-400',
                        'badge' => 'bg-pink-500/20 text-pink-300 border-pink-500/30 animate-pulse',
                        'label' => 'IN-ROOM BERTUGAS',
                        'avatar_bg' => 'from-pink-900 to-rose-900',
                    ],
                    'off' => [
                        'border' => 'border-slate-800 hover:border-slate-700',
                        'badge' => 'bg-slate-800 text-slate-400 border-slate-700',
                        'label' => 'OFF DUTY',
                        'avatar_bg' => 'from-slate-900 to-slate-800',
                    ],
                ][$companion->status];
            @endphp

            <div class="bg-[#0c1222] border {{ $statusConfig['border'] }} rounded-2xl p-5 shadow-xl flex flex-col justify-between transition-all hover:-translate-y-1">
                <div>
                    <!-- Card Top -->
                    <div class="flex items-start justify-between mb-4">
                        <div class="flex items-center space-x-3">
                            <div class="w-12 h-12 rounded-xl bg-gradient-to-tr {{ $statusConfig['avatar_bg'] }} border border-slate-700 flex items-center justify-center font-bold text-white text-base">
                                {{ substr($companion->stage_name, 0, 1) }}
                            </div>
                            <div>
                                <h3 class="text-base font-bold text-white">{{ $companion->stage_name }}</h3>
                                <div class="flex items-center text-amber-400 text-xs">
                                    <i class="fa-solid fa-star text-[10px] mr-1"></i>
                                    <span class="font-bold">{{ $companion->rating }}</span>
                                    <span class="text-slate-500 text-[10px] ml-1.5">({{ $companion->total_hours_served }} Jam)</span>
                                </div>
                            </div>
                        </div>

                        <span class="px-2 py-0.5 rounded text-[9px] font-black uppercase tracking-wider {{ $statusConfig['badge'] }} border">
                            {{ $statusConfig['label'] }}
                        </span>
                    </div>

                    <!-- Details -->
                    <div class="space-y-2 text-xs py-3 border-y border-slate-800/80 mb-4">
                        <div class="flex justify-between text-slate-400">
                            <span>Tarif Layanan:</span>
                            <span class="font-mono font-bold text-amber-400">{{ $companion->formatted_price }} / Jam</span>
                        </div>

                        <div class="flex justify-between text-slate-400">
                            <span>Posisi Kamar:</span>
                            @if($companion->status === 'in_room' && $companion->currentRoom)
                                <span class="font-bold text-rose-400 font-mono">Room {{ $companion->currentRoom->room_number }}</span>
                            @else
                                <span class="text-slate-500">-</span>
                            @endif
                        </div>

                        @if($companion->skills)
                            <div class="pt-1">
                                <span class="text-[10px] text-slate-500 uppercase font-bold block mb-1">Repertoar:</span>
                                <div class="flex flex-wrap gap-1">
                                    @foreach($companion->skills_array as $skill)
                                        <span class="text-[9px] px-1.5 py-0.5 rounded bg-slate-900 text-pink-300 border border-slate-800">
                                            {{ $skill }}
                                        </span>
                                    @endforeach
                                </div>
                            </div>
                        @endif
                    </div>
                </div>

                <!-- Fast Status Action Controls -->
                <form method="POST" action="{{ route('kasir.companions.status', $companion->id) }}" class="space-y-2">
                    @csrf
                    @if($companion->status === 'standby')
                        <div class="flex space-x-1.5">
                            <select name="current_room_id" class="w-full px-2 py-1.5 bg-slate-900 border border-slate-700 rounded-lg text-xs text-white focus:outline-none">
                                <option value="">Pilih Kamar...</option>
                                @foreach($rooms as $r)
                                    <option value="{{ $r->id }}">Room {{ $r->room_number }}</option>
                                @endforeach
                            </select>
                            <input type="hidden" name="status" value="in_room">
                            <button type="submit" class="px-3 py-1.5 bg-pink-600 hover:bg-pink-500 text-white rounded-lg text-xs font-bold shrink-0 transition-all">
                                Tugaskan
                            </button>
                        </div>
                    @elseif($companion->status === 'in_room')
                        <input type="hidden" name="status" value="standby">
                        <button type="submit" class="w-full py-2 bg-emerald-600/30 hover:bg-emerald-600 border border-emerald-500/40 text-emerald-300 hover:text-white rounded-xl text-xs font-bold transition-all flex items-center justify-center">
                            <i class="fa-solid fa-arrow-rotate-left mr-1.5"></i> Selesai & Kembali Standby
                        </button>
                    @else
                        <input type="hidden" name="status" value="standby">
                        <button type="submit" class="w-full py-2 bg-slate-800 hover:bg-slate-700 text-slate-300 rounded-xl text-xs font-bold transition-all">
                            Set Menjadi Standby (Masuk Kerja)
                        </button>
                    @endif
                </form>
            </div>
        @endforeach
    </div>
</div>
@endsection
