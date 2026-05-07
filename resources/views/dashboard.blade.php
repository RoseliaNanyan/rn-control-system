@extends('layouts.app')

@section('content')

<div class="card">
    <h2 class="page-title">Dashboard</h2>
    <p class="page-subtitle">Riwayat Aktivitas Terakhir</p>
</div>

<div class="card">

    <table class="table">
        <thead>
            <tr>
                <th>User</th>
                <th>Aktivitas</th>
                <th>Waktu</th>
            </tr>
        </thead>

        <tbody>
            @forelse($logs as $log)
            <tr>
                <td>{{ $log->user->username ?? '-' }}</td>
                <td>{{ $log->aktivitas }}</td>
                <td>{{ \Carbon\Carbon::parse($log->created_at)->format('d/m/Y H:i') }}</td>
            </tr>
            @empty
            <tr>
                <td colspan="3" class="text-center text-gray-500">
                    Belum ada aktivitas
                </td>
            </tr>
            @endforelse
        </tbody>
    </table>

</div>

@endsection