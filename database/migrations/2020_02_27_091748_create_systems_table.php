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
            $table->integer('plugin_tries')->nullable();
            $table->integer('plugin_request_timeout')->nullable();
            $table->string('registrator')->nullable();
            $table->string('db')->nullable();
            $table->string('db_host')->nullable();
            $table->string('db_port')->nullable();
            $table->string('db_database')->nullable();
            $table->string('db_username')->nullable();
            $table->string('db_password')->nullable();
            $table->string('client_id')->nullable();
            $table->string('client_secret')->nullable();
            $table->string('auth_url')->nullable();
            $table->string('base_uri')->nullable();
            $table->string('redirect_uri')->nullable();
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
