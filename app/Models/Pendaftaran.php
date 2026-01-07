<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Pendaftaran extends Model
{
    use HasFactory;

    protected $table = 'pendaftaran';
    protected $primaryKey = 'no_daftar';
    public $incrementing = true;
    protected $keyType = 'int';

    protected $fillable = [
        'nama_pemohon',
        'tanggal_daftar',
        'hari',
        'tanggal_hadir',
        'jam_hadir',
    ];

    protected $casts = [
        'tanggal_daftar' => 'date',
        'tanggal_hadir' => 'date',
    ];

    public function daftarUlang()
    {
        return $this->hasMany(DaftarUlang::class, 'no_daftar', 'no_daftar');
    }
}
