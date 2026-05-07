<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\Pengajuan;

class Anggota extends Model
{
    protected $table = 'anggota'; // 🔥 INI YANG KURANG

    protected $fillable = [
        'cif',
        'nama',
        'alamat',
        'kelurahan',
        'kecamatan'
    ];

    public function pengajuan()
    {
        return $this->hasMany(Pengajuan::class);
    }
}