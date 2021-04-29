<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateLaporanTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('laporan', function (Blueprint $table) {
            $table->id();
            $table->integer('jenis_identitas')->comment('0:KTP | 1:SIM | 2:Tidak Ada');
            $table->integer('no_identitas')->nullable();
            $table->string('nama');
            $table->integer('jenis_kecelakaan')->comment('0: Kecelakaan Tunggal | 1: Kecelakaan Ganda');
            $table->text('kondisi_korban');
            $table->integer('status')->default('0')->comment('0: Laporan Terkirim | 1: Laporan Diterima | 2: Ambulance Menuju TKP | 3: Pasien Dibawa Kerumah Sakit | 4: Pasien Ditangani');
            $table->integer('id_pembuat');
            $table->integer('status_laporan')->default('0')->comment('0: Belum Dilapor ke asuransi | 1: Telah dilapor');
            $table->string('lat');
            $table->string('lng');
            $table->string('foto');
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
        Schema::dropIfExists('laporan');
    }
}
