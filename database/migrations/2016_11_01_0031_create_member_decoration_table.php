<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateMemberDecorationTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('member_decoration', function(Blueprint $table){
            
            // "Award ID"
            $table->increments('awd_id');
            
            $table->string('regt_num', 10);
            $table->unsignedInteger('dec_id');
            
            $table->dateTime('date')->nullable();

            $table->timestamps();
            $table->softDeletes();

            
            // todo: FK to leave table
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
        if (Schema::hasTable('member_decoration')){
            Schema::table('member_decoration', function ($table) {
                $table->dropForeign('member_decoration_regt_num_foreign');
                $table->dropForeign('member_decoration_dec_id_foreign');
            });
            Schema::drop('member_decoration');
        }
    }
}
