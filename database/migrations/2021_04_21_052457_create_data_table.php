<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateDataTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('data', function (Blueprint $table) {
            $table->id();
            $table->integer('id_laporan');
            $table->integer('jenis_identitas')->comment('0:KTP | 1:SIM | 2:Tidak Ada');
            $table->integer('no_identitas')->nullable();
            $table->string('nama');
            $table->integer('jenis_kecelakaan')->comment('0: Kecelakaan Tunggal | 1: Kecelakaan Ganda');
            $table->text('kondisi_korban');
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
        Schema::dropIfExists('data');
    }
}
