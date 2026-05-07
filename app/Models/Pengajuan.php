<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Pengajuan extends Model
{
    protected $fillable = [
        'anggota_id',
        'tanggal_pengajuan',
        'jumlah_pengajuan',
        'jenis_pinjaman',
        'tujuan_pinjaman',
        'status',
        'tanggal_pencairan',
        'nilai_disetujui',
        'petugas_survey'
    ];

    // ================= RELASI =================

    public function anggota()
    {
        return $this->belongsTo(Anggota::class);
    }

    // 🔥 WAJIB INI (BIAR ERROR HILANG)
    public function riwayat()
    {
        return $this->hasMany(RiwayatProses::class, 'pengajuan_id');
    }

    // 🔥 BONUS (AMBIL TERAKHIR)
    public function riwayatTerakhir()
    {
        return $this->hasOne(RiwayatProses::class, 'pengajuan_id')
                    ->latestOfMany('tanggal');
    }
}