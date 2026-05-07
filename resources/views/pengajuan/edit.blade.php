@extends('layouts.app')

@section('content')

<div class="card">
    <h3>Edit Pengajuan</h3>

    <form method="POST" action="{{ route('pengajuan.updateData', $data->id) }}">
        @csrf
        @method('PUT')

        <label>Tanggal</label>
        <input type="date" name="tanggal_pengajuan" class="input"
            value="{{ $data->tanggal_pengajuan }}">

        <label>Jumlah</label>
        <input type="number" name="jumlah" class="input"
            value="{{ $data->jumlah_pengajuan }}">

        <label>Jenis</label>
        <input type="text" name="jenis" class="input"
            value="{{ $data->jenis_pinjaman }}">

        <label>Tujuan</label>
        <input type="text" name="tujuan" class="input"
            value="{{ $data->tujuan_pinjaman }}">

        <br><br>

        <button class="btn btn-primary">Update</button>
    </form>
</div>

@endsection