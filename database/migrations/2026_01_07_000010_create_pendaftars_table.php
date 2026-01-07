<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePendaftarsTable extends Migration
{
    public function up()
    {
        Schema::create('pendaftaran', function (Blueprint $table) {
            $table->bigIncrements('no_daftar');
            $table->string('nama_pemohon');
            $table->date('tanggal_daftar');
            $table->string('hari');
            $table->date('tanggal_hadir');
            $table->time('jam_hadir');
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('pendaftaran');
    }
}
