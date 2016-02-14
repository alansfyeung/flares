<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateSystemUsersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('users', function(Blueprint $table){
            $table->increments('user_id');
            $table->string('username')->unique();
            $table->integer('access_level');
            $table->dateTime('last_login_time');
            $table->string('email')->nullable();
            $table->string('password');
            $table->binary('signature_blob')->nullable();
            $table->string('signature_extn')->nullable();
            $table->rememberToken();
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('users');
    }
}
