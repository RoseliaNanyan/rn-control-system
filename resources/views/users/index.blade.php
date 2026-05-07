@extends('layouts.app')

@section('content')

<div class="card">
    <h2>Manajemen User</h2>
    <p>Kelola akun pengguna sistem</p>
</div>

<div class="card">

    <div class="flex items-center justify-between mb-5">

        <div>
            <h3 class="text-lg font-semibold text-gray-800">
                Manajemen User
            </h3>

            <p class="text-sm text-gray-500">
                Kelola akun pengguna sistem
            </p>
        </div>

        <a href="{{ route('users.create') }}"
           class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg text-sm font-medium transition">

            + Tambah User

        </a>

    </div>

    <div class="overflow-x-auto">

        <table class="w-full text-sm">

            <thead>
                <tr class="border-b text-gray-600">

                    <th class="text-left py-3">Nama</th>
                    <th class="text-left py-3">Username</th>
                    <th class="text-left py-3">Role</th>
                    <th class="text-left py-3">Aksi</th>

                </tr>
            </thead>

            <tbody>

               @foreach($users as $d)

                <tr class="border-b hover:bg-gray-50 transition">

                    <td class="py-3">
                        {{ $d->name }}
                    </td>

                    <td class="py-3">
                        {{ $d->username }}
                    </td>

                    <td class="py-3">

                        @if($d->role == 'admin')

                            <span class="bg-red-100 text-red-700 px-3 py-1 rounded-full text-xs font-semibold">
                                Admin
                            </span>

                        @elseif($d->role == 'staf')

                            <span class="bg-blue-100 text-blue-700 px-3 py-1 rounded-full text-xs font-semibold">
                                Staf
                            </span>

                        @else

                            <span class="bg-green-100 text-green-700 px-3 py-1 rounded-full text-xs font-semibold">
                                Pengawas
                            </span>

                        @endif

                    </td>

                    <td class="py-3">

                        <div class="flex gap-2">

                            <a href="{{ route('users.edit', $d->id) }}"
                               class="bg-yellow-100 text-yellow-700 px-3 py-1 rounded-lg text-xs font-medium hover:bg-yellow-200 transition">

                                Edit

                            </a>

                            <a href="{{ route('users.delete', $d->id) }}"
                               class="bg-red-100 text-red-700 px-3 py-1 rounded-lg text-xs font-medium hover:bg-red-200 transition">

                                Hapus

                            </a>

                        </div>

                    </td>

                </tr>

                @endforeach

            </tbody>

        </table>

    </div>

</div>

@endsection