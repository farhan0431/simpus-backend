<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePemeriksaanUmumTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('pemeriksaan_umum', function (Blueprint $table) {
            $table->id();
            $table->integer('no_rm');
            $table->string('subjek')->nullable();
            $table->string('objek')->nullable();
            $table->string('anamnesa')->nullable();
            $table->string('perawatan')->nullable();
            $table->string('diagnosa')->nullable();
            $table->string('dokter')->nullable();
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
        Schema::dropIfExists('pemeriksaan_umum');
    }
}
