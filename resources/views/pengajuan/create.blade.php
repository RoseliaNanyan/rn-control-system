@extends('layouts.app')

@section('content')

<div class="card">
    <h2 class="page-title">Input Pengajuan</h2>
    <p class="page-subtitle">Tambah data pengajuan baru</p>
</div>

@if($errors->any())
<div class="card">
    <ul class="text-red-600 text-sm">
        @foreach($errors->all() as $err)
            <li>{{ $err }}</li>
        @endforeach
    </ul>
</div>
@endif

<div class="card">
<form method="POST" action="{{ route('pengajuan.store') }}">
@csrf

<div class="grid grid-cols-2 gap-4">

    <!-- CIF -->
    <div class="relative">
        <label class="block text-xs font-semibold text-gray-600 mb-1">CIF</label>
        <input type="text" name="cif" id="cif_input" class="input" required>
    </div>

    <!-- NAMA -->
    <div>
        <label class="block text-xs font-semibold text-gray-600 mb-1">Nama</label>
        <input type="text" name="nama" class="input" required>
    </div>

    <!-- ALAMAT -->
    <div>
        <label class="block text-xs font-semibold text-gray-600 mb-1">Alamat</label>
        <input type="text" name="alamat" class="input">
    </div>

    <!-- KELURAHAN -->
    <div>
        <label class="block text-xs font-semibold text-gray-600 mb-1">Kelurahan</label>
        <input type="text" name="kelurahan" class="input">
    </div>

    <!-- KECAMATAN -->
    <div>
        <label class="block text-xs font-semibold text-gray-600 mb-1">Kecamatan</label>
        <input type="text" name="kecamatan" class="input">
    </div>

    <!-- TANGGAL -->
    <div>
       <label class="block text-xs font-semibold text-gray-600 mb-1">Tanggal Pengajuan</label>
        <input type="text" name="tanggal_pengajuan" class="input datepicker" placeholder="dd/mm/yyyy">
    </div>

   <!-- JUMLAH -->
<div>
    <label class="block text-xs font-semibold text-gray-600 mb-1">
        Jumlah
    </label>

    <div class="flex overflow-hidden rounded-lg border border-gray-300 bg-white">

      <div class="px-5 py-2 flex items-center bg-gray-100 border-r text-gray-700 font-semibold">
            Rp
        </div>

        <input
            type="text"
            id="rupiah"
           class="flex-1 px-4 py-2 text-sm outline-none border-0 focus:ring-0 bg-white"
            placeholder="Masukkan nominal">
    </div>

    <input type="hidden" name="jumlah" id="jumlah_real">
</div>
    <!-- JENIS -->
    <div>
        <label class="block text-xs font-semibold text-gray-600 mb-1">Jenis</label>
        <select name="jenis" class="input" required>
            <option value="">Pilih</option>
            <option value="KDRN">KDRN</option>
            <option value="KON">KON</option>
            <option value="PHM">PHM</option>
            <option value="PHT">PHT</option>
            <option value="PRO">PRO</option>
            <option value="PMU">PMU</option>
        </select>
    </div>

    <!-- TUJUAN -->
    <div class="col-span-2">
        <label class="block text-xs font-semibold text-gray-600 mb-1">Tujuan</label>
        <input type="text" name="tujuan" class="input">
    </div>

</div>

<div class="mt-4 flex gap-2">
    <button class="btn btn-primary btn-md">💾 Simpan</button>
    <a href="{{ route('pengajuan.index') }}" class="btn btn-gray btn-md">Kembali</a>
</div>

</form>

</div>

<!-- DATEPICKER -->
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">
<script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>

<script>
document.addEventListener("DOMContentLoaded", function () {
    function showNotif(type, title, message) {

    const notif = document.createElement('div');

    notif.className =
        'mb-4 rounded-xl border px-4 py-3 shadow-sm';

    // warna notif
    if (type === 'success') {

        notif.classList.add(
            'border-green-200',
            'bg-green-50',
            'text-green-700'
        );

    } else {

        notif.classList.add(
            'border-red-200',
            'bg-red-50',
            'text-red-700'
        );

    }

    // isi notif
    notif.innerHTML = `
        <div class="font-semibold">
            ${title}
        </div>

        <div class="text-sm">
            ${message}
        </div>
    `;

    // ambil card
    const card = document.querySelector('.card');

    // tampilkan notif di paling atas
    card.prepend(notif);

    // hilang otomatis
    setTimeout(() => {
        notif.remove();
    }, 3000);

}

    // DATE FORMAT
    flatpickr(".datepicker", {
        dateFormat: "d/m/Y",
        allowInput: false
    });

    // RUPIAH FORMAT
    const rupiah = document.getElementById('rupiah');
    const real = document.getElementById('jumlah_real');

    rupiah.addEventListener('input', function () {

        let angka = this.value.replace(/[^0-9]/g, '');

        real.value = angka;

        this.value = angka
            ? new Intl.NumberFormat('id-ID').format(angka)
            : '';

    });

    rupiah.addEventListener('blur', function () {

        if (this.value === '') {
            real.value = '';
        }

    });

    // CIF CHECK
    const cifInput = document.getElementById('cif_input');

    cifInput.addEventListener('blur', function () {

        let cif = this.value;

        if (!cif) return;

        fetch(`/cek-anggota/${cif}`)

    .then(async res => {

        if (!res.ok) {
            return null;
        }

        return await res.json();

    })

    .then(data => {
        console.log(data);
        
        // DATA DITEMUKAN
        if (data) {

    showNotif(
        'success',
        'Data anggota ditemukan',
        'Data otomatis berhasil diisi.'
    );

    document.querySelector('input[name="nama"]').value = data.nama ?? '';
    document.querySelector('input[name="alamat"]').value = data.alamat ?? '';
    document.querySelector('input[name="kelurahan"]').value = data.kelurahan ?? '';
    document.querySelector('input[name="kecamatan"]').value = data.kecamatan ?? '';

    document.querySelector('input[name="tanggal_pengajuan"]').focus();

}
        // DATA TIDAK DITEMUKAN
       else {

    showNotif(
        'error',
        'CIF belum terdaftar',
        'Silakan isi data anggota secara manual.'
    );

    document.querySelector('input[name="nama"]').value = '';
    document.querySelector('input[name="alamat"]').value = '';
    document.querySelector('input[name="kelurahan"]').value = '';
    document.querySelector('input[name="kecamatan"]').value = '';

}
    })

    .catch(error => {

        console.log(error);

    });

    });

});
</script>

@endsection