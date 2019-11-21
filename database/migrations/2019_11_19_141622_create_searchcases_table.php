<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateSearchcasesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('searchcases', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('case_id');
            $table->string('request');
            $table->integer('status');
            $table->boolean('registrar');
            $table->integer('download');
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
        Schema::dropIfExists('searchcases');
    }
}
