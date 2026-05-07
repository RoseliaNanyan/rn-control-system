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
@endphp

<!-- HEADER -->
<div class="card">
    <h2 class="page-title">Detail Pengajuan</h2>
</div>

<!-- DATA -->
<div class="card">
    <div style="display:grid;grid-template-columns:1fr 1fr;gap:20px;">

        <div>
            <h4>Data Anggota</h4>
            <p><b>CIF:</b> {{ $data->anggota->cif ?? '-' }}</p>
            <p><b>Nama:</b> {{ $data->anggota->nama ?? '-' }}</p>
            <p><b>Alamat:</b> {{ $data->anggota->alamat ?? '-' }}</p>
        </div>

        <div>
            <h4>Data Pengajuan</h4>
            <p><b>Tanggal:</b> {{ $data->tanggal_pengajuan }}</p>
            <p><b>Jumlah:</b> Rp {{ number_format($data->jumlah_pengajuan,0,',','.') }}</p>
            <p><b>Jenis:</b> {{ $data->jenis_pinjaman }}</p>
            <p><b>Tujuan:</b> {{ $data->tujuan_pinjaman }}</p>

            <p><b>Status:</b>
                <span class="badge warning">
                    {{ $label[strtolower($data->status)] ?? $data->status }}
                </span>
            </p>
        </div>

    </div>
</div>

<!-- TIMELINE -->
<div class="card">
    <h3 class="mb-4 font-semibold">Timeline Proses</h3>

    @php
    $warnaTahap = [
        'pengajuan masuk' => 'border-blue-500 bg-blue-50',
        'berkas' => 'border-yellow-500 bg-yellow-50',
        'survey' => 'border-purple-500 bg-purple-50',
        'rapat' => 'border-indigo-500 bg-indigo-50',
        'persetujuan' => 'border-green-500 bg-green-50',
        'pencairan' => 'border-green-700 bg-green-100',
        'ditolak' => 'border-red-500 bg-red-50',
        'dibatalkan' => 'border-gray-500 bg-gray-100'
    ];
    @endphp

    @forelse($riwayat as $r)

        @php
            $warna = $warnaTahap[strtolower($r->tahap)] ?? 'border-gray-300 bg-gray-50';
        @endphp

        <div class="mb-3 p-3 rounded-lg border-l-4 {{ $warna }}">
            <div class="font-semibold">
                {{ \Carbon\Carbon::parse($r->tanggal)->format('d/m/Y') }}
                - {{ $label[$r->tahap] ?? $r->tahap }}
            </div>
            <div class="text-sm text-gray-600">
    {{ $r->keterangan }}
</div>

@if(
    in_array(strtolower($r->tahap), ['rapat','persetujuan','pencairan'])
    && $data->nilai_disetujui
)

<div class="mt-2">
    <span class="badge success">
        Nilai Disetujui:
        Rp {{ number_format($data->nilai_disetujui,0,',','.') }}
    </span>
</div>

@endif
        </div>

    @empty
        <p class="text-gray-500">Tidak ada riwayat</p>
    @endforelse

    <a href="/pengajuan" class="btn btn-gray btn-sm mt-3">← Kembali</a>
</div>

<!-- UPDATE -->
<div class="card">
    <h3>Update Proses</h3>

   @if(
    $tahap_berikutnya &&
    in_array(auth()->user()->role, ['admin','staf'])
)
  <form method="POST" action="{{ route('pengajuan.update') }}">
    @csrf

    <input type="hidden" name="pengajuan_id" value="{{ $data->id }}">
    <input type="hidden" name="tahap" value="{{ $tahap_berikutnya }}">

    <p class="mb-4">
        ➡ Tahap berikutnya:
        <b>{{ $label[$tahap_berikutnya] }}</b>
    </p>

    <div class="grid grid-cols-2 gap-4">

        <!-- TANGGAL -->
        <div>
            <label class="text-xs font-semibold text-gray-600 mb-1">
                Tanggal
            </label>

            <input type="text"
                   name="tanggal"
                   class="input datepicker"
                   required>
        </div>

        <!-- KETERANGAN -->
        <div>
            <label class="text-xs font-semibold text-gray-600 mb-1">
                Keterangan
            </label>

            <input type="text"
                   name="keterangan"
                   class="input"
                   placeholder="Diinput oleh"
                   required>
        </div>

    </div>

    {{-- ================= RAPAT KREDIT ================= --}}
   @if($data->status == 'rapat')

    <div class="mt-4">

        <label class="text-xs font-semibold text-gray-600 mb-1">
            Keputusan
        </label>

        <select name="keputusan"
                id="keputusan"
                class="input"
                required>

            <option value="">-- Pilih --</option>
            <option value="pencairan">Disetujui</option>
            <option value="ditolak">Ditolak</option>

        </select>

    </div>

    <!-- NILAI DISETUJUI -->
    <div class="mt-4 hidden" id="nilai_disetujui_box">

        <label class="text-xs font-semibold text-gray-600 mb-1">
            Nilai Disetujui
        </label>

        <input type="text"
       name="nilai_disetujui"
       id="nilai_disetujui"
       class="input"
       placeholder="Masukkan nilai disetujui">

    </div>

    @endif

    <div class="mt-4">
        <button type="submit"
                class="btn btn-primary btn-md">
            Proses
        </button>
    </div>

</form>

@elseif(!$tahap_berikutnya)

<p class="text-green-600 font-medium">
    ✔ Proses sudah selesai
</p>

@elseif(auth()->user()->role == 'pengawas')

<p class="text-gray-500">
    Mode monitoring pengawas
</p>

@endif

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

    const keputusan = document.getElementById('keputusan');
    const nilaiBox = document.getElementById('nilai_disetujui_box');

    if (keputusan) {

        keputusan.addEventListener('change', function () {

            if (this.value === 'pencairan') {
                nilaiBox.classList.remove('hidden');
            } else {
                nilaiBox.classList.add('hidden');
            }

        });

    }

});

document.addEventListener("DOMContentLoaded", function () {

    const inputNilai = document.getElementById('nilai_disetujui');

    if (inputNilai) {

        inputNilai.addEventListener('input', function (e) {

            let angka = this.value.replace(/\D/g, '');

            this.value = new Intl.NumberFormat('id-ID').format(angka);

        });

    }

});
</script>

@endsection