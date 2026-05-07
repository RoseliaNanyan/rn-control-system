@extends('layouts.app')

@section('content')

<div style="max-width:900px;">

<div class="card">
    <h2>Tambah Anggota</h2>
    <p>Input data anggota baru</p>
</div>

@if($errors->any())
<div class="card" style="background:#fee2e2;color:#991b1b;">
    <ul style="margin:0;padding-left:20px;">
        @foreach($errors->all() as $err)
            <li>{{ $err }}</li>
        @endforeach
    </ul>
</div>
@endif

<div class="card">

<form method="POST" action="{{ route('anggota.store') }}">
@csrf

<div class="grid">

<div>
<label>CIF</label>
<input type="text" name="cif" class="input" 
       value="{{ request('cif') ?? old('cif') }}">
</div>

<div>
<label>Nama</label>
<input type="text" name="nama" class="input" value="{{ old('nama') }}">
</div>

<div class="full">
<label>Alamat</label>
<input type="text" name="alamat" class="input" value="{{ old('alamat') }}">
</div>

<div>
<label>Kelurahan</label>
<input type="text" name="kelurahan" class="input" value="{{ old('kelurahan') }}">
</div>

<div>
<label>Kecamatan</label>
<input type="text" name="kecamatan" class="input" value="{{ old('kecamatan') }}">
</div>

</div>

<br>

<button class="btn">💾 Simpan</button>
<a href="/pengajuan" class="btn btn-gray">Kembali</a>

</form>

</div>

</div>

@endsection