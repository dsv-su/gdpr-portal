<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePluginsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('plugins', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('plugin');
            $table->string('name');
            $table->string('client_id')->nullable();
            $table->string('client_secret')->nullable();
            $table->string('auth')->nullable();
            $table->string('auth_url')->nullable();
            $table->string('base_uri')->nullable();
            $table->string('redirect_uri')->nullable();
            $table->string('endpoint_url')->nullable();
            $table->string('owner_email')->nullable();
            $table->integer('search')->nullable();
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
        Schema::dropIfExists('plugins');
    }
}
