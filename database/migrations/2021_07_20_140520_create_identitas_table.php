<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateIdentitasTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('identitas', function (Blueprint $table) {
            $table->id();
            $table->integer('no_rm');
            $table->string('nama');
            $table->string('nik');
            $table->date('tanggal_lahir');
            $table->string('alamat');
            $table->string('telp');
            $table->string('riwayat_alergi');
            $table->integer('status_pembayaran')->comment('1:BPJS | 2:Umum');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('identitas');
    }
}
