<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePengurusanTable extends Migration
{
    public function up()
    {
        Schema::create('pengurusan', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('no_antrian');
            $table->unsignedBigInteger('no_daftar');
            $table->string('nama_pemohon');
            $table->string('berkas');
            $table->string('status');
            $table->string('keterangan');
            $table->unsignedBigInteger('pembayaran')->default(0);
            $table->timestamps();

            $table->foreign('no_daftar')->references('no_daftar')->on('pendaftaran')->onDelete('cascade');
        });
    }

    public function down()
    {
        Schema::dropIfExists('pengurusan');
    }
}
