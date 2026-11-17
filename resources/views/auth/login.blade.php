@extends('layouts.app')

@section('title', 'Login Pegawai & Member - Grand Karaoke')

@section('content')
<div class="min-h-[75vh] flex items-center justify-center px-4 py-12">
    <div class="w-full max-w-md">
        <!-- Brand Header -->
        <div class="text-center mb-8">
            <div class="inline-flex w-16 h-16 rounded-2xl bg-gradient-to-tr from-purple-600 via-violet-500 to-amber-400 p-[2px] mb-4 shadow-xl shadow-purple-600/30">
                <div class="w-full h-full bg-[#0c1222] rounded-[14px] flex items-center justify-center">
                    <i class="fa-solid fa-microphone-lines text-amber-400 text-2xl"></i>
                </div>
            </div>
            <h2 class="text-2xl font-black text-white tracking-tight">PORTAL AKSES MASUK</h2>
            <p class="text-xs text-slate-400 mt-1">Sistem Otentikasi Role-Based Access Control (RBAC)</p>
        </div>

        <!-- Login Card -->
        <div class="bg-[#0c1222] border border-slate-800 rounded-2xl p-6 sm:p-8 shadow-2xl relative overflow-hidden">
            <div class="absolute -top-20 -right-20 w-40 h-40 bg-purple-600/10 rounded-full blur-3xl pointer-events-none"></div>

            <!-- Error Alerts -->
            @if ($errors->any())
                <div class="mb-5 p-4 rounded-xl bg-rose-500/10 border border-rose-500/30 text-rose-300 text-xs">
                    <ul class="list-disc list-inside space-y-1">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <form method="POST" action="{{ route('login.post') }}" class="space-y-4">
                @csrf
                <div>
                    <label for="username" class="block text-xs font-semibold text-slate-300 uppercase tracking-wider mb-2">Username / Email</label>
                    <div class="relative">
                        <span class="absolute inset-y-0 left-0 flex items-center pl-3.5 text-slate-500">
                            <i class="fa-solid fa-user text-sm"></i>
                        </span>
                        <input type="text" id="username" name="username" value="{{ old('username') }}" required autofocus
                            placeholder="Masukkan username Anda"
                            class="w-full pl-10 pr-4 py-2.5 bg-slate-900 border border-slate-700/80 rounded-xl text-slate-100 placeholder-slate-500 text-sm focus:outline-none focus:border-purple-500 focus:ring-1 focus:ring-purple-500">
                    </div>
                </div>

                <div>
                    <label for="password" class="block text-xs font-semibold text-slate-300 uppercase tracking-wider mb-2">Password</label>
                    <div class="relative">
                        <span class="absolute inset-y-0 left-0 flex items-center pl-3.5 text-slate-500">
                            <i class="fa-solid fa-lock text-sm"></i>
                        </span>
                        <input type="password" id="password" name="password" required
                            placeholder="Masukkan password Anda"
                            class="w-full pl-10 pr-4 py-2.5 bg-slate-900 border border-slate-700/80 rounded-xl text-slate-100 placeholder-slate-500 text-sm focus:outline-none focus:border-purple-500 focus:ring-1 focus:ring-purple-500">
                    </div>
                </div>

                <div class="flex items-center justify-between text-xs pt-1">
                    <label class="flex items-center text-slate-400 cursor-pointer">
                        <input type="checkbox" name="remember" class="w-4 h-4 rounded bg-slate-900 border-slate-700 text-purple-600 focus:ring-purple-500 focus:ring-offset-0">
                        <span class="ml-2">Ingat Sesi Saya</span>
                    </label>
                </div>

                <button type="submit" class="w-full py-3 px-4 rounded-xl bg-gradient-to-r from-purple-600 to-indigo-600 hover:from-purple-500 hover:to-indigo-500 text-white font-bold text-sm shadow-lg shadow-purple-600/30 transition-all hover:scale-[1.02] border border-purple-400/30">
                    <i class="fa-solid fa-right-to-bracket mr-2"></i> Masuk ke Sistem
                </button>
            </form>

            <!-- Quick Demo Login Selection -->
            <div class="mt-6 pt-5 border-t border-slate-800">
                <p class="text-[11px] font-bold uppercase tracking-wider text-amber-400 text-center mb-3">
                    <i class="fa-solid fa-bolt mr-1"></i> Quick Demo Login 1-Klik:
                </p>
                <div class="grid grid-cols-3 gap-2">
                    <button type="button" onclick="fillLogin('owner', 'password123')"
                        class="p-2.5 bg-amber-500/10 hover:bg-amber-500/20 border border-amber-500/30 rounded-xl text-center transition-all group">
                        <i class="fa-solid fa-crown text-amber-400 text-base block mb-1 group-hover:scale-110 transition-transform"></i>
                        <span class="text-[11px] font-bold text-amber-300 block">Owner</span>
                        <span class="text-[9px] text-slate-400">Full Access</span>
                    </button>
                    <button type="button" onclick="fillLogin('kasir', 'password123')"
                        class="p-2.5 bg-purple-500/10 hover:bg-purple-500/20 border border-purple-500/30 rounded-xl text-center transition-all group">
                        <i class="fa-solid fa-cash-register text-purple-400 text-base block mb-1 group-hover:scale-110 transition-transform"></i>
                        <span class="text-[11px] font-bold text-purple-300 block">Kasir</span>
                        <span class="text-[9px] text-slate-400">POS & Room</span>
                    </button>
                    <button type="button" onclick="fillLogin('budi', 'password123')"
                        class="p-2.5 bg-blue-500/10 hover:bg-blue-500/20 border border-blue-500/30 rounded-xl text-center transition-all group">
                        <i class="fa-solid fa-user text-blue-400 text-base block mb-1 group-hover:scale-110 transition-transform"></i>
                        <span class="text-[11px] font-bold text-blue-300 block">Customer</span>
                        <span class="text-[9px] text-slate-400">Reservasi</span>
                    </button>
                </div>
            </div>

            <div class="mt-6 text-center text-xs text-slate-400">
                Belum punya akun pelanggan?
                <a href="{{ route('register') }}" class="text-purple-400 font-semibold hover:underline ml-1">Daftar Akun Baru</a>
            </div>
        </div>
    </div>
</div>

<script>
    function fillLogin(user, pass) {
        document.getElementById('username').value = user;
        document.getElementById('password').value = pass;
    }
</script>
@endsection
