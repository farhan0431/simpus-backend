<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateOdontogramTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('odontogram', function (Blueprint $table) {
            $table->id();
            $table->integer('no_rm');

            $table->integer('a18');
            $table->integer('a17');
            $table->integer('a16');
            $table->integer('a15_55');
            $table->integer('a14_54');
            $table->integer('a13_53');
            $table->integer('a12_52');
            $table->integer('a11_51');

            $table->integer('a28');
            $table->integer('a27');
            $table->integer('a26');
            $table->integer('a25_65');
            $table->integer('a24_64');
            $table->integer('a23_63');
            $table->integer('a22_62');
            $table->integer('a21_61');

            $table->integer('a38');
            $table->integer('a37');
            $table->integer('a36');
            $table->integer('a35_75');
            $table->integer('a34_74');
            $table->integer('a33_73');
            $table->integer('a32_72');
            $table->integer('a31_71');

            $table->integer('a48');
            $table->integer('a47');
            $table->integer('a46');
            $table->integer('a45_85');
            $table->integer('a44_84');
            $table->integer('a43_83');
            $table->integer('a41_84');
            $table->integer('a41_81');
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
        Schema::dropIfExists('odontogram');
    }
}
