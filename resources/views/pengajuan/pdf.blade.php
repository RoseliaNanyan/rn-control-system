<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Laporan Pengajuan Kredit diatas Simpanan</title>

    <style>

        body{
            font-family: sans-serif;
            font-size: 12px;
        }

        .judul{
            text-align:center;
            margin-bottom:20px;
        }

        .judul h2,
        .judul h3,
        .judul h4,
        .judul p{
            margin:4px;
        }

        table{
            width:100%;
            border-collapse: collapse;
        }

        th,td{
            border:1px solid #000;
            padding:6px;
        }

        th{
            background:#eee;
        }

    </style>
</head>
<body>

<div class="judul">

    <h2>KOPERASI CU BETANG ASI</h2>

    <h3>KANTOR CABANG TAHASAK BATU SEPAN</h3>

    <h3>LAPORAN PENGAJUAN KREDIT DIATAS SIMPANAN</h3>

    <p>
    Tanggal
    {{ $tanggal_awal ?? '-' }}
    s.d
    {{ $tanggal_akhir ?? '-' }}
</p>
  
</div>

<table>

    <thead>
        <tr>
            <th>CIF</th>
            <th>Nama</th>
            <th>Tgl Pengajuan</th>
            <th>Jumlah</th>
            <th>Status</th>
        </tr>
    </thead>

    <tbody>

        @foreach($data as $d)

        <tr>

            <td>{{ $d->anggota->cif }}</td>

            <td>{{ $d->anggota->nama }}</td>

            <td>
                {{ \Carbon\Carbon::parse($d->tanggal_pengajuan)->format('d/m/Y') }}
            </td>

            <td>
                Rp {{ number_format($d->jumlah_pengajuan,0,',','.') }}
            </td>

            <td>
                {{ ucfirst($d->status) }}
            </td>

        </tr>

        @endforeach

    </tbody>

</table>
<div style="
    position: fixed;
    bottom: 10px;
    left: 10px;
    font-size: 10px;
    color: #555;
">
    Download:
    {{ now()->format('d/m/Y H:i') }}
</div>
</body>
</html>