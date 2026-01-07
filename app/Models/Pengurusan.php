<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Pengurusan extends Model
{
    use HasFactory;

    protected $table = 'pengurusan';

    protected $fillable = [
        'no_antrian',
        'no_daftar',
        'nama_pemohon',
        'berkas',
        'status',
        'keterangan',
        'pembayaran',
    ];

    public function pendaftaran()
    {
        return $this->belongsTo(Pendaftaran::class, 'no_daftar', 'no_daftar');
    }
}
