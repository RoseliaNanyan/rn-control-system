<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class RiwayatProses extends Model
{
    protected $fillable = [
    'pengajuan_id',
    'tahap',
    'keterangan',
    'tanggal',
    'user_input'
];
}
