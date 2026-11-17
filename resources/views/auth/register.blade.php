@extends('layouts.app')

@section('title', 'Daftar Akun Pelanggan - HY KAROKE AND LOUNGE')

@section('content')
<div class="min-h-[75vh] flex items-center justify-center px-4 py-12">
    <div class="w-full max-w-md">
        <!-- Brand Header -->
        <div class="text-center mb-8">
            <h2 class="text-2xl font-black text-white tracking-tight">REGISTRASI PELANGGAN</h2>
            <p class="text-xs text-slate-400 mt-1">Daftar akun untuk kemudahan reservasi dan info promo eksklusif</p>
        </div>

        <div class="bg-[#0c1222] border border-slate-800 rounded-2xl p-6 sm:p-8 shadow-2xl relative overflow-hidden">
            @if ($errors->any())
                <div class="mb-5 p-4 rounded-xl bg-rose-500/10 border border-rose-500/30 text-rose-300 text-xs">
                    <ul class="list-disc list-inside space-y-1">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <form method="POST" action="{{ route('register.post') }}" class="space-y-4">
                @csrf
                <div>
                    <label for="name" class="block text-xs font-semibold text-slate-300 uppercase tracking-wider mb-1.5">Nama Lengkap</label>
                    <input type="text" id="name" name="name" value="{{ old('name') }}" required
                        placeholder="Contoh: Budi Santoso"
                        class="w-full px-4 py-2.5 bg-slate-900 border border-slate-700/80 rounded-xl text-slate-100 text-sm focus:outline-none focus:border-purple-500">
                </div>

                <div>
                    <label for="username" class="block text-xs font-semibold text-slate-300 uppercase tracking-wider mb-1.5">Username</label>
                    <input type="text" id="username" name="username" value="{{ old('username') }}" required
                        placeholder="Contoh: budisantoso"
                        class="w-full px-4 py-2.5 bg-slate-900 border border-slate-700/80 rounded-xl text-slate-100 text-sm focus:outline-none focus:border-purple-500">
                </div>

                <div>
                    <label for="phone" class="block text-xs font-semibold text-slate-300 uppercase tracking-wider mb-1.5">No. WhatsApp / HP</label>
                    <input type="text" id="phone" name="phone" value="{{ old('phone') }}" required
                        placeholder="Contoh: 08123456789"
                        class="w-full px-4 py-2.5 bg-slate-900 border border-slate-700/80 rounded-xl text-slate-100 text-sm focus:outline-none focus:border-purple-500">
                </div>

                <div>
                    <label for="email" class="block text-xs font-semibold text-slate-300 uppercase tracking-wider mb-1.5">Email (Opsional)</label>
                    <input type="email" id="email" name="email" value="{{ old('email') }}"
                        placeholder="Contoh: budi@gmail.com"
                        class="w-full px-4 py-2.5 bg-slate-900 border border-slate-700/80 rounded-xl text-slate-100 text-sm focus:outline-none focus:border-purple-500">
                </div>

                <div class="grid grid-cols-2 gap-3">
                    <div>
                        <label for="password" class="block text-xs font-semibold text-slate-300 uppercase tracking-wider mb-1.5">Password</label>
                        <input type="password" id="password" name="password" required
                            placeholder="Min. 6 digit"
                            class="w-full px-3.5 py-2.5 bg-slate-900 border border-slate-700/80 rounded-xl text-slate-100 text-sm focus:outline-none focus:border-purple-500">
                    </div>
                    <div>
                        <label for="password_confirmation" class="block text-xs font-semibold text-slate-300 uppercase tracking-wider mb-1.5">Ulangi Password</label>
                        <input type="password" id="password_confirmation" name="password_confirmation" required
                            placeholder="Konfirmasi"
                            class="w-full px-3.5 py-2.5 bg-slate-900 border border-slate-700/80 rounded-xl text-slate-100 text-sm focus:outline-none focus:border-purple-500">
                    </div>
                </div>

                <button type="submit" class="w-full mt-2 py-3 px-4 rounded-xl bg-gradient-to-r from-purple-600 to-indigo-600 hover:from-purple-500 hover:to-indigo-500 text-white font-bold text-sm shadow-lg shadow-purple-600/30 transition-all hover:scale-[1.02]">
                    <i class="fa-solid fa-user-plus mr-2"></i> Buat Akun Pelanggan
                </button>
            </form>

            <div class="mt-6 text-center text-xs text-slate-400">
                Sudah memiliki akun?
                <a href="{{ route('login') }}" class="text-purple-400 font-semibold hover:underline ml-1">Masuk Sekarang</a>
            </div>
        </div>
    </div>
</div>
@endsection

