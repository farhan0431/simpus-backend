<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateSettingsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('settings', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('pemerintah');
            $table->string('deskripsi_pemerintah');            
            $table->string('slogan');
            $table->string('inisial');
            $table->string('kantor_badan')->nullable();
            $table->string('inisial_kantor_badan')->nullable();
            $table->text('alamat')->nullable();
            $table->integer('range_tahun');
            $table->unsignedInteger('provinsi');
            $table->unsignedInteger('kota');
            $table->string('logo');
            $table->softDeletes();
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
        Schema::dropIfExists('settings');
    }
}
