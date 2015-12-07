<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateLeaveTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('leave', function(Blueprint $table){
            $table->increments('leave_id');
            $table->string('regt_num', 10);
            $table->dateTime('date_start');
            $table->dateTime('date_end')->nullable();
            $table->text('reason');
            $table->tinyInteger('is_approved')->default(0);
            $table->tinyInteger('is_autogen')->default(0);
            $table->timestamps();
            
            $table->foreign('regt_num')->references('regt_num')->on('members');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        if (Schema::hasTable('leave')){
            Schema::table('leave', function ($table) {
                $table->dropForeign('leave_regt_num_foreign');
            });
            Schema::drop('leave');
        }
    }
}
