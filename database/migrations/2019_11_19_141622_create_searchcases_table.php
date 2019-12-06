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
            $table->string('request_pnr')->nullable();
            $table->string('request_email')->nullable();
            $table->string('request_uid')->nullable();
            $table->integer('status_scipro_dev');
            $table->integer('status_moodle_test');
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
