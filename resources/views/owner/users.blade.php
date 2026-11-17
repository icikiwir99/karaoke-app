@extends('layouts.admin')

@section('title', 'Manajemen Akun Login Pegawai (RBAC) - Owner Area')
@section('header_title', 'Manajemen Akun Pegawai (RBAC)')
@section('header_subtitle', 'Pengaturan hak akses, otentikasi kasir, dan penambahan akun staf outlet')

@section('content')
<div class="space-y-6">
    <!-- Action Bar -->
    <div class="bg-[#0c1222] border border-slate-800 rounded-2xl p-4 flex flex-wrap items-center justify-between gap-4">
        <div>
            <span class="text-xs font-bold text-slate-300">Total Akun Terdaftar: <span class="text-purple-400 font-mono font-bold">{{ $users->count() }} Pengguna</span></span>
            <p class="text-[11px] text-slate-500">Metode keamanan Role-Based Access Control (RBAC): Owner, Kasir, Customer.</p>
        </div>

        <button type="button" onclick="openModal('addUserModal')"
            class="px-4 py-2 bg-gradient-to-r from-purple-600 to-indigo-600 hover:from-purple-500 hover:to-indigo-500 text-white text-xs font-black rounded-xl shadow-lg shadow-purple-600/25 transition-all">
            <i class="fa-solid fa-user-plus mr-1.5"></i> Buat Akun Pegawai Baru
        </button>
    </div>

    <!-- Users Table -->
    <div class="bg-[#0c1222] border border-slate-800 rounded-2xl p-6 shadow-xl">
        <div class="overflow-x-auto">
            <table class="w-full text-left text-xs text-slate-300">
                <thead class="bg-slate-900/80 text-[10px] uppercase font-bold text-slate-400 border-b border-slate-800">
                    <tr>
                        <th class="py-3 px-4">Nama Lengkap</th>
                        <th class="py-3 px-4">Username</th>
                        <th class="py-3 px-4">Role Akses (RBAC)</th>
                        <th class="py-3 px-4">Kontak / Telp</th>
                        <th class="py-3 px-4">Email</th>
                        <th class="py-3 px-4">Terdaftar Sejak</th>
                        <th class="py-3 px-4 text-center">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-800/60">
                    @foreach($users as $user)
                        <tr class="hover:bg-slate-900/40 transition-colors">
                            <td class="py-3.5 px-4 font-bold text-white flex items-center space-x-2.5">
                                <span class="w-7 h-7 rounded-lg bg-slate-800 text-purple-400 font-bold flex items-center justify-center text-xs uppercase">
                                    {{ substr($user->name, 0, 1) }}
                                </span>
                                <span>{{ $user->name }}</span>
                            </td>
                            <td class="py-3.5 px-4 font-mono font-bold text-amber-400">{{ $user->username }}</td>
                            <td class="py-3.5 px-4">
                                @if($user->role === 'owner')
                                    <span class="px-2.5 py-0.5 rounded text-[10px] font-black uppercase tracking-wider bg-amber-500/20 text-amber-300 border border-amber-500/30">
                                        <i class="fa-solid fa-crown mr-1"></i> Owner
                                    </span>
                                @elseif($user->role === 'kasir')
                                    <span class="px-2.5 py-0.5 rounded text-[10px] font-black uppercase tracking-wider bg-purple-500/20 text-purple-300 border border-purple-500/30">
                                        <i class="fa-solid fa-cash-register mr-1"></i> Kasir
                                    </span>
                                @else
                                    <span class="px-2.5 py-0.5 rounded text-[10px] font-black uppercase tracking-wider bg-blue-500/20 text-blue-300 border border-blue-500/30">
                                        <i class="fa-solid fa-user mr-1"></i> Customer
                                    </span>
                                @endif
                            </td>
                            <td class="py-3.5 px-4 font-mono text-slate-300">{{ $user->phone ?? '-' }}</td>
                            <td class="py-3.5 px-4 text-slate-400">{{ $user->email ?? '-' }}</td>
                            <td class="py-3.5 px-4 text-slate-500">{{ $user->created_at->format('d M Y') }}</td>
                            <td class="py-3.5 px-4 text-center">
                                <button type="button" onclick="openEditUserModal({{ json_encode($user) }})"
                                    class="p-1.5 bg-slate-800 hover:bg-slate-700 text-purple-400 rounded-lg text-xs transition-all" title="Edit Akun">
                                    <i class="fa-solid fa-pen-to-square"></i>
                                </button>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- ==================== USER MODALS ==================== -->

<!-- 1. Add User Modal -->
<div id="addUserModal" class="fixed inset-0 bg-slate-950/80 backdrop-blur-sm z-50 hidden flex items-center justify-center p-4">
    <div class="bg-[#0c1222] border border-slate-800 rounded-3xl max-w-md w-full p-6 sm:p-8 shadow-2xl relative">
        <button type="button" onclick="closeModal('addUserModal')" class="absolute top-5 right-5 text-slate-400 hover:text-white">
            <i class="fa-solid fa-xmark text-lg"></i>
        </button>

        <div class="mb-5">
            <span class="text-[10px] font-bold uppercase tracking-wider text-purple-400">AKUN LOGIN PEGAWAI</span>
            <h3 class="text-xl font-black text-white mt-0.5">Tambah Pengguna Baru</h3>
        </div>

        <form method="POST" action="{{ route('owner.users.store') }}" class="space-y-4">
            @csrf
            <div>
                <label class="block text-xs font-semibold text-slate-300 uppercase mb-1">Nama Lengkap *</label>
                <input type="text" name="name" required placeholder="Contoh: Rian Kasir"
                    class="w-full px-3.5 py-2.5 bg-slate-900 border border-slate-700 rounded-xl text-xs text-white focus:outline-none focus:border-purple-500">
            </div>

            <div class="grid grid-cols-2 gap-3">
                <div>
                    <label class="block text-xs font-semibold text-slate-300 uppercase mb-1">Username Login *</label>
                    <input type="text" name="username" required placeholder="Contoh: kasir3"
                        class="w-full px-3.5 py-2.5 bg-slate-900 border border-slate-700 rounded-xl text-xs text-white focus:outline-none focus:border-purple-500">
                </div>
                <div>
                    <label class="block text-xs font-semibold text-slate-300 uppercase mb-1">Role Akses (RBAC) *</label>
                    <select name="role" required class="w-full px-3.5 py-2.5 bg-slate-900 border border-slate-700 rounded-xl text-xs text-white focus:outline-none focus:border-purple-500">
                        <option value="kasir" selected>Kasir / Staff Outlet</option>
                        <option value="owner">Owner / Executive</option>
                        <option value="customer">Customer / Tamu</option>
                    </select>
                </div>
            </div>

            <div class="grid grid-cols-2 gap-3">
                <div>
                    <label class="block text-xs font-semibold text-slate-300 uppercase mb-1">No. WhatsApp *</label>
                    <input type="text" name="phone" required placeholder="08123456789"
                        class="w-full px-3.5 py-2.5 bg-slate-900 border border-slate-700 rounded-xl text-xs text-white focus:outline-none focus:border-purple-500">
                </div>
                <div>
                    <label class="block text-xs font-semibold text-slate-300 uppercase mb-1">Password *</label>
                    <input type="password" name="password" required minlength="6" placeholder="Min. 6 karakter"
                        class="w-full px-3.5 py-2.5 bg-slate-900 border border-slate-700 rounded-xl text-xs text-white focus:outline-none focus:border-purple-500">
                </div>
            </div>

            <div>
                <label class="block text-xs font-semibold text-slate-300 uppercase mb-1">Email (Opsional)</label>
                <input type="email" name="email" placeholder="kasir@karaoke.com"
                    class="w-full px-3.5 py-2.5 bg-slate-900 border border-slate-700 rounded-xl text-xs text-white focus:outline-none focus:border-purple-500">
            </div>

            <div class="pt-2">
                <button type="submit" class="w-full py-3 px-4 rounded-xl bg-purple-600 hover:bg-purple-500 text-white font-extrabold text-sm shadow-xl shadow-purple-600/30 transition-all">
                    Simpan Akun Pengguna
                </button>
            </div>
        </form>
    </div>
</div>

<!-- 2. Edit User Modal -->
<div id="editUserModal" class="fixed inset-0 bg-slate-950/80 backdrop-blur-sm z-50 hidden flex items-center justify-center p-4">
    <div class="bg-[#0c1222] border border-slate-800 rounded-3xl max-w-md w-full p-6 sm:p-8 shadow-2xl relative">
        <button type="button" onclick="closeModal('editUserModal')" class="absolute top-5 right-5 text-slate-400 hover:text-white">
            <i class="fa-solid fa-xmark text-lg"></i>
        </button>

        <div class="mb-5">
            <span class="text-[10px] font-bold uppercase tracking-wider text-amber-400">EDIT AKUN PENGGUNA</span>
            <h3 class="text-xl font-black text-white mt-0.5">Perbarui Data Pegawai</h3>
        </div>

        <form method="POST" id="editUserForm" class="space-y-4">
            @csrf
            @method('PUT')

            <div>
                <label class="block text-xs font-semibold text-slate-300 uppercase mb-1">Nama Lengkap *</label>
                <input type="text" name="name" id="edit_user_name" required
                    class="w-full px-3.5 py-2.5 bg-slate-900 border border-slate-700 rounded-xl text-xs text-white focus:outline-none focus:border-amber-400">
            </div>

            <div class="grid grid-cols-2 gap-3">
                <div>
                    <label class="block text-xs font-semibold text-slate-300 uppercase mb-1">Username Login *</label>
                    <input type="text" name="username" id="edit_user_username" required
                        class="w-full px-3.5 py-2.5 bg-slate-900 border border-slate-700 rounded-xl text-xs text-white focus:outline-none focus:border-amber-400">
                </div>
                <div>
                    <label class="block text-xs font-semibold text-slate-300 uppercase mb-1">Role Akses (RBAC) *</label>
                    <select name="role" id="edit_user_role" required class="w-full px-3.5 py-2.5 bg-slate-900 border border-slate-700 rounded-xl text-xs text-white focus:outline-none focus:border-amber-400">
                        <option value="kasir">Kasir / Staff Outlet</option>
                        <option value="owner">Owner / Executive</option>
                        <option value="customer">Customer / Tamu</option>
                    </select>
                </div>
            </div>

            <div class="grid grid-cols-2 gap-3">
                <div>
                    <label class="block text-xs font-semibold text-slate-300 uppercase mb-1">No. WhatsApp</label>
                    <input type="text" name="phone" id="edit_user_phone"
                        class="w-full px-3.5 py-2.5 bg-slate-900 border border-slate-700 rounded-xl text-xs text-white focus:outline-none focus:border-amber-400">
                </div>
                <div>
                    <label class="block text-xs font-semibold text-slate-300 uppercase mb-1">Ganti Password (Opsional)</label>
                    <input type="password" name="password" placeholder="Kosongkan jika tidak ubah"
                        class="w-full px-3.5 py-2.5 bg-slate-900 border border-slate-700 rounded-xl text-xs text-white focus:outline-none focus:border-amber-400">
                </div>
            </div>

            <div>
                <label class="block text-xs font-semibold text-slate-300 uppercase mb-1">Email</label>
                <input type="email" name="email" id="edit_user_email"
                    class="w-full px-3.5 py-2.5 bg-slate-900 border border-slate-700 rounded-xl text-xs text-white focus:outline-none focus:border-amber-400">
            </div>

            <div class="pt-2">
                <button type="submit" class="w-full py-3 px-4 rounded-xl bg-gradient-to-r from-amber-500 to-amber-600 hover:from-amber-400 hover:to-amber-500 text-slate-950 font-black text-sm shadow-xl shadow-amber-500/20 transition-all">
                    Perbarui Data Pengguna
                </button>
            </div>
        </form>
    </div>
</div>

<script>
    function openModal(id) {
        document.getElementById(id).classList.remove('hidden');
    }
    function closeModal(id) {
        document.getElementById(id).classList.add('hidden');
    }

    function openEditUserModal(user) {
        document.getElementById('editUserForm').action = "/owner/users/" + user.id;
        document.getElementById('edit_user_name').value = user.name;
        document.getElementById('edit_user_username').value = user.username;
        document.getElementById('edit_user_role').value = user.role;
        document.getElementById('edit_user_phone').value = user.phone || '';
        document.getElementById('edit_user_email').value = user.email || '';
        openModal('editUserModal');
    }
</script>
@endsection
