<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AlterMemberDecorationsTable2 extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('member_decorations', function (Blueprint $table) {
            $table->unsignedInteger('user_id')->default(1);      // Just set it to the first 
            $table->unsignedInteger('dec_appr_id')->nullable();
            $table->foreign('user_id')->references('user_id')->on('users');
            $table->foreign('dec_appr_id')->references('dec_appr_id')->on('decoration_approvals');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('member_decorations', function ($table) {
            $table->dropForeign('member_decorations_user_id_foreign');
            $table->dropForeign('member_decorations_dec_appr_id_foreign');
            $table->dropColumn('user_id');
            $table->dropColumn('dec_appr_id');
        });
    }
}
