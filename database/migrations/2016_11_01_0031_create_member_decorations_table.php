<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateMemberDecorationsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('member_decorations', function(Blueprint $table){
            
            // "Award ID"
            $table->increments('awd_id');
            
            $table->string('regt_num', 10);
            $table->unsignedInteger('dec_id');
            
            $table->text('citation')->nullable();
            $table->dateTime('date')->nullable();

            $table->timestamps();
            $table->softDeletes();

            $table->foreign('regt_num')->references('regt_num')->on('members');
            $table->foreign('dec_id')->references('dec_id')->on('decorations');
            
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        if (Schema::hasTable('member_decorations')){
            Schema::table('member_decorations', function ($table) {
                $table->dropForeign('member_decorations_regt_num_foreign');
                $table->dropForeign('member_decorations_dec_id_foreign');
            });
            Schema::drop('member_decorations');
        }
    }
}
