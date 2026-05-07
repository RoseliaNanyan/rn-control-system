<?php

namespace App\Http\Controllers;

use App\Models\Anggota;
use Illuminate\Http\Request;

class AnggotaController extends Controller
{
    public function create(Request $request)
    {
        // ambil CIF dari URL (biar auto isi)
        $cif = $request->cif;

        return view('anggota.create', compact('cif'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'cif' => 'required|unique:anggotas,cif',
            'nama' => 'required',
        ]);

        Anggota::create([
            'cif' => $request->cif,
            'nama' => $request->nama,
            'alamat' => $request->alamat,
            'kelurahan' => $request->kelurahan,
            'kecamatan' => $request->kecamatan,
        ]);

        // balik ke pengajuan + bawa CIF
        return redirect('/create?cif=' . $request->cif)
            ->with('success', 'Anggota berhasil ditambahkan');
    }
}