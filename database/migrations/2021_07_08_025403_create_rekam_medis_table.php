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
            $table->integer('berat_badan');
            $table->integer('tinggi_badan');
            $table->string('tekanan_darah');
            $table->string('nadi');
            $table->integer('lingkar_perut');
            $table->integer('suhu');
            $table->string('nafas');
            $table->integer('rujukan_poli')->comment('1: Umum | 2:Gigi');
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
