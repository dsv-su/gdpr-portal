<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateSystemsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('systems', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('app_env');
            $table->string('app_debug');
            $table->string('app_url');
            $table->string('case_start_id')->nullable();
            $table->integer('case_ttl');
            $table->string('authorization_parameter');
            $table->string('authorization', 200);
            $table->string('login_route');
            $table->integer('plugin_tries');
            $table->integer('plugin_request_timeout');
            $table->string('registrator');
            $table->string('db');
            $table->string('db_host');
            $table->string('db_port');
            $table->string('db_database');
            $table->string('db_username');
            $table->string('db_password');
            $table->string('client_id');
            $table->string('client_secret');
            $table->string('auth_url');
            $table->string('base_uri');
            $table->string('redirect_uri');
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
        Schema::dropIfExists('systems');
    }
}
