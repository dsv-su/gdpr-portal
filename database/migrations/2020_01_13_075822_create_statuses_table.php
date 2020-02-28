<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateStatusesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('statuses', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('searchcase_id');
            $table->unsignedBigInteger('plugin_id');
            $table->foreign('searchcase_id')->references('id')->on('searchcases')->onDelete('cascade');
            $table->foreign('plugin_id')->references('id')->on('plugins')->onDelete('cascade');
            $table->string('plugin_name');
            $table->integer('status');
            $table->integer('progress_status');
            $table->integer('download_status');
            $table->string('code')->nullable();
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
        Schema::dropIfExists('statuses');
    }
}
