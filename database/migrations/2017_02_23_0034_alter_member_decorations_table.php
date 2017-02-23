<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AlterMemberDecorationsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('member_decorations', function(Blueprint $table){ 
            $table->unique(['regt_num', 'dec_id'], 'unique_regt_num_dec_id');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('member_decorations', function (Blueprint $table) {
            $table->dropUnique('unique_regt_num_dec_id');
        });
    }
}
