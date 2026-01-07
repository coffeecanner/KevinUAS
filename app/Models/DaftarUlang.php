<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DaftarUlang extends Model
{
    use HasFactory;

    protected $table = 'daftar_ulang';

    protected $fillable = [
        'no_daftar',
        'nama_pemohon',
        'hari_harus_datang',
        'tanggal_harus_datang',
        'ktp',
        'kk',
        'ijazah_akta',
        'keterangan',
        'no_antrian',
    ];

    public function pendaftaran()
    {
        return $this->belongsTo(Pendaftaran::class, 'no_daftar', 'no_daftar');
    }
}
