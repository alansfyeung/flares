<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AlterUsersTable extends Migration
{
    /**
     * Add SSO fields and remove 'signature' fields
     *
     * @return void
     */
    public function up()
    {
        Schema::table('users', function (Blueprint $table) {
            $table->string('forums_username')->nullable();
            $table->tinyInteger('allow_sso')->default(0);
            $table->dropColumn('signature_blob');
            $table->dropColumn('signature_extn');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('users', function ($table) {
            $table->dropColumn('forums_username');
            $table->dropColumn('allow_sso');
            $table->binary('signature_blob')->nullable();
            $table->string('signature_extn')->nullable();
        });
    }
}
