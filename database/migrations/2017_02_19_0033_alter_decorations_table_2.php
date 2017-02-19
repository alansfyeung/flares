<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AlterDecorationsTable2 extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('decorations', function(Blueprint $table){ 
            $table->unsignedInteger('parent_id')->nullable();
            $table->integer('parent_order')->default(0);
            $table->foreign('parent_id')->references('dec_id')->on('decorations')->onDelete('no action');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('decorations', function (Blueprint $table) {
            $table->dropForeign('decorations_parent_id_foreign');
            $table->dropColumn('parent_id');
            $table->dropColumn('parent_order');
        });
    }
}
