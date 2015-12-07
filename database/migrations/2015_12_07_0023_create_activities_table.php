<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateActivitiesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('activities', function(Blueprint $table){
            $table->increments('acty_id');
            $table->string('name', 100);
            $table->string('type', 100)->nullable();
            $table->dateTime('start_date');
            $table->dateTime('end_date');
            $table->text('desc');
            $table->tinyInteger('is_parade_night');
            $table->tinyInteger('is_half_day');
            $table->tinyInteger('is_rescheduled');
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
        Schema::dropIfExists('activities');
    }
}
