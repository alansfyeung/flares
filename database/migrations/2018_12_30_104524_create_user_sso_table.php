<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateUserSsoTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('user_sso', function (Blueprint $table) {
            $table->increments('sso_id');
            $table->unsignedInteger('user_id');
            $table->string('sso_token', 32);
            $table->tinyInteger('is_redirect')->nullable();
            $table->dateTime('expires_at')->nullable();
            $table->timestamps();
            $table->foreign('user_id')->references('user_id')->on('users');
        }); 
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        if (Schema::hasTable('user_sso')){
            Schema::table('user_sso', function ($table) {
                $table->dropForeign('user_sso_user_id_foreign');
            });
            Schema::drop('user_sso');
        }
    }
}
