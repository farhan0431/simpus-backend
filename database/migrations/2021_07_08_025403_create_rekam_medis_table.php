<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateRekamMedisTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('rekam_medis', function (Blueprint $table) {
            $table->id();
            $table->integer('no_rm');
            $table->string('nama');
            $table->string('nik');
            $table->date('tanggal_lahir');
            $table->string('alamat');
            $table->string('telp');
            $table->integer('berat_badan');
            $table->integer('tinggi_badan');
            $table->string('tekanan_darah');
            $table->string('nadi');
            $table->integer('lingkar_perut');
            $table->integer('suhu');
            $table->string('nafas');
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
        Schema::dropIfExists('rekam_medis');
    }
}
