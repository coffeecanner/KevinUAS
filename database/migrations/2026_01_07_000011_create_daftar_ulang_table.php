<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateDaftarUlangTable extends Migration
{
    public function up()
    {
        Schema::create('daftar_ulang', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('no_daftar');
            $table->string('nama_pemohon');
            $table->string('hari_harus_datang');
            $table->date('tanggal_harus_datang');
            $table->boolean('ktp')->default(false);
            $table->boolean('kk')->default(false);
            $table->boolean('ijazah_akta')->default(false);
            $table->string('keterangan')->default('TIDAK');
            $table->unsignedBigInteger('no_antrian')->nullable();
            $table->timestamps();

            $table->foreign('no_daftar')->references('no_daftar')->on('pendaftaran')->onDelete('cascade');
        });
    }

    public function down()
    {
        Schema::dropIfExists('daftar_ulang');
    }
}
