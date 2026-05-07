@extends('layouts.app')

@section('content')

<div class="card">
    <h2 class="page-title">Tambah User</h2>
    <p class="page-subtitle">
        Buat akun baru untuk sistem
    </p>
</div>

@if($errors->any())
<div class="card">
    <div class="rounded-xl bg-red-50 border border-red-200 px-4 py-3">
        <ul class="text-sm text-red-700 space-y-1">
            @foreach($errors->all() as $err)
                <li>• {{ $err }}</li>
            @endforeach
        </ul>
    </div>
</div>
@endif

<div class="card">

    <form method="POST" action="{{ route('users.store') }}">
        @csrf

        <div class="grid grid-cols-2 gap-5">

            <!-- NAMA -->
            <div class="col-span-2">
                <label class="block text-sm font-semibold text-gray-700 mb-2">
                    Nama
                </label>

                <input
                    type="text"
                    name="name"
                    class="input"
                    placeholder="Masukkan nama user"
                    required
                >
            </div>

            <!-- USERNAME -->
            <div>
                <label class="block text-sm font-semibold text-gray-700 mb-2">
                    Username
                </label>

                <input
                    type="text"
                    name="username"
                    class="input"
                    placeholder="Masukkan username"
                    required
                >
            </div>

            <!-- PASSWORD -->
            <div>
                <label class="block text-sm font-semibold text-gray-700 mb-2">
                    Password
                </label>

                <input
                    type="password"
                    name="password"
                    class="input"
                    placeholder="Masukkan password"
                    required
                >
            </div>

            <!-- ROLE -->
            <div class="col-span-2">
                <label class="block text-sm font-semibold text-gray-700 mb-2">
                    Role
                </label>

                <select name="role" class="input" required>
                    <option value="">Pilih Role</option>
                    <option value="admin">Admin</option>
                    <option value="staf">Staf</option>
                    <option value="pengawas">Pengawas</option>
                </select>
            </div>

        </div>

        <!-- BUTTON -->
        <div class="mt-6 flex items-center gap-3">

            <button
                type="submit"
                class="bg-blue-600 hover:bg-blue-700 text-white px-5 py-2.5 rounded-lg text-sm font-medium transition"
            >
                💾 Simpan
            </button>

            <a href="{{ route('users.index') }}"
               class="bg-gray-200 hover:bg-gray-300 text-gray-700 px-5 py-2.5 rounded-lg text-sm font-medium transition">
                Kembali
            </a>

        </div>

    </form>

</div>

@endsection