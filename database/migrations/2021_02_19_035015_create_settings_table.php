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
            $table->string('rumah_sakit');
            $table->string('deskripsi_rumah_sakit');    
            $table->string('inisial_rumah_sakit');
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
