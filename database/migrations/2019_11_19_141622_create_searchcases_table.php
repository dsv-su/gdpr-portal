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
            $table->integer('visability');
            $table->string('gdpr_userid');
            $table->string('gdpr_useremail');
            $table->string('gdpr_server');
            $table->string('case_id');
            $table->string('request_pnr')->nullable();
            $table->string('request_email')->nullable();
            $table->string('request_uid')->nullable();
            $table->integer('status_processed');
            $table->integer('status_flag');
            $table->boolean('registrar');
            $table->date('sent_registrar')->nullable();
            $table->integer('progress');
            $table->integer('plugins_processed');
            $table->integer('download_status');
            $table->integer('downloaded');
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
