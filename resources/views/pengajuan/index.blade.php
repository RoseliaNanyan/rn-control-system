@extends('layouts.app')

@section('content')

@php
$label = [
    'pengajuan masuk' => 'Pengajuan Masuk',
    'berkas' => 'Berkas Lengkap',
    'survey' => 'Survey Lapangan',
    'rapat' => 'Rapat Tim Kredit',
    'persetujuan' => 'Persetujuan Anggota',
    'pencairan' => 'Pencairan',
    'ditolak' => 'Ditolak',
    'dibatalkan' => 'Dibatalkan'
];

$warnaStatus = [
    'pengajuan masuk' => 'warning',
    'berkas' => 'warning',
    'survey' => 'warning',
    'rapat' => 'warning',
    'persetujuan' => 'success',
    'pencairan' => 'success',
    'ditolak' => 'danger',
    'dibatalkan' => 'danger'
];
@endphp

<!-- ================= HEADER ================= -->
<div class="card">
    <h2 class="page-title">Daftar Pengajuan Kredit diatas Simpanan</h2>
    <p class="page-subtitle">Kelola dan monitor proses kredit</p>
</div>

<!-- ================= STAT ================= -->
<div class="grid-3">
    <div class="stat-card">
        <p>Total</p>
        <h3>{{ $total }}</h3>
    </div>

    <div class="stat-card success">
        <p>Disetujui</p>
        <h3>{{ $disetujui }}</h3>
    </div>

    <div class="stat-card danger">
        <p>Ditolak</p>
        <h3>{{ $ditolak }}</h3>
    </div>
</div>

<!-- ================= FILTER ================= -->
<div class="card">
    <form method="GET" class="flex items-end gap-3 flex-wrap">

        <div class="flex flex-col w-64">
            <label class="text-xs font-semibold text-gray-600 mb-1">Nama</label>
            <input type="text" name="search"
                placeholder="Cari nama..."
                value="{{ request('search') }}"
                class="input">
        </div>

        <div class="flex flex-col">
            <label class="text-xs font-semibold text-gray-600 mb-1">Dari</label>
            <input type="text" name="tanggal_awal"
                class="input datepicker w-40"
               value="{{ request('tanggal_awal', $tanggalAwal) }}">
        </div>

        <div class="flex flex-col">
            <label class="text-xs font-semibold text-gray-600 mb-1">Sampai</label>
            <input type="text" name="tanggal_akhir"
                class="input datepicker w-40"
                value="{{ request('tanggal_akhir', $tanggalAkhir) }}">
        </div>

        <div class="flex flex-col w-48">
            <label class="text-xs font-semibold text-gray-600 mb-1">Status</label>
            <select name="status" class="input">
                <option value="">Semua</option>
                @foreach($label as $key => $val)
                    <option value="{{ $key }}"
                        {{ request('status') == $key ? 'selected' : '' }}>
                        {{ $val }}
                    </option>
                @endforeach
            </select>
        </div>

        <div>
            <button type="submit" class="btn btn-primary btn-md">
                Filter
            </button>
        </div>

    </form>
</div>

<!-- ================= TABLE ================= -->
<div class="card">

<div class="card-header mb-4 flex justify-between items-center">

    <h3 class="font-semibold text-gray-800">
        Data Pengajuan
    </h3>

    <div class="flex items-center gap-2">

        <!-- HIDE -->
        <button type="button"
                id="toggleTable"
                class="btn btn-warning btn-md">
            Hide
        </button>
<a href="{{ route('pengajuan.exportPdf', request()->query()) }}"
   class="btn btn-danger btn-md">
    ⬇ Download PDF
</a>
        <a href="{{ route('pengajuan.export', request()->query()) }}"
           class="btn btn-success btn-md">
            ⬇ Download Excel
        </a>

        @if(in_array(auth()->user()->role, ['admin','staf']))

<a href="/create" class="btn btn-danger btn-md">
    + Tambah Pengajuan
</a>

@endif

    </div>

</div>

<div id="tableContent">

    <table class="table">
        <thead>
            <tr>
                <th>CIF</th>
                <th>Nama</th>
                <th>Tgl Pengajuan</th>
                <th>Tgl Selesai</th>
                <th>Jumlah</th>
                <th>Status</th>
                <th>Umur (hari)</th>
                <th>Aksi</th>
            </tr>
        </thead>

        <tbody>
        @forelse($data as $d)
        <tr>
            <td>{{ $d->anggota->cif }}</td>

            <td>{{ $d->anggota->nama }}</td>

            <td>
                {{ \Carbon\Carbon::parse($d->tanggal_pengajuan)->format('d/m/Y') }}
            </td>

            <td>
                @if(in_array(strtolower($d->status), ['pencairan','ditolak']))
                    {{ $d->riwayatTerakhir 
                        ? \Carbon\Carbon::parse($d->riwayatTerakhir->tanggal)->format('d/m/Y') 
                        : '-' }}
                @else
                    -
                @endif
            </td>

            <td>
                Rp {{ number_format($d->jumlah_pengajuan,0,',','.') }}
            </td>

            <!-- STATUS -->
            <td>
                @php
                    $status = strtolower($d->status);
                    $warna = $warnaStatus[$status] ?? 'warning';
                @endphp

                <span class="badge {{ $warna }}">
                    {{ $label[$status] ?? $status }}
                </span>
            </td>

            <!-- UMUR -->
           <td class="text-center">
    <span class="badge {{ $d->warna }}">
        {{ $d->hari_label }}
    </span>
</td>

            <!-- AKSI -->
            <td class="flex justify-center items-center gap-2">

    <a href="{{ route('pengajuan.detail', $d->id) }}" class="btn btn-primary btn-sm">
        Detail
    </a>

    @if(
    in_array(auth()->user()->role, ['admin','staf']) &&
    !in_array(strtolower($d->status), ['pencairan','ditolak'])
)

<a href="{{ route('pengajuan.edit', $d->id) }}" class="btn btn-warning btn-sm">
    Edit
</a>

@endif

</td>

        </tr>
        @empty
        <tr>
            <td colspan="8" class="text-center text-gray-500">
                Tidak ada data pengajuan
            </td>
        </tr>
        @endforelse
        </tbody>
    </table>

    <div class="mt-4">
        {{ $data->links() }}
    </div>
</div>
</div>

<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">
<script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>

<script>
document.addEventListener("DOMContentLoaded", function () {

    flatpickr(".datepicker", {
        dateFormat: "d/m/Y",
        allowInput: false
    });

});

document.addEventListener("DOMContentLoaded", function () {

    const btn = document.getElementById('toggleTable');
    const table = document.getElementById('tableContent');

    btn.addEventListener('click', function () {

      if (table.classList.contains('hidden')) {

    table.classList.remove('hidden');
    btn.innerText = 'Hide';

} else {

    table.classList.add('hidden');
    btn.innerText = 'Show';

}

    });

});
</script>

@endsection