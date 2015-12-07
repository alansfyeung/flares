<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateRefTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('ref_misc', function(Blueprint $table){
            $table->increments('id');
            $table->string('name', 255); 
            $table->string('value', 255); 
            $table->string('cond', 255); 
        });
        Schema::create('ref_ranks', function(Blueprint $table){
            $table->string('abbr', 10);
            $table->string('name', 100); 
            $table->integer('pos'); 
            $table->primary('abbr');
        });
        Schema::create('ref_postings', function(Blueprint $table){
            $table->string('abbr', 10);
            $table->string('name', 100); 
            $table->integer('pos'); 
            $table->primary('abbr');
        });
        Schema::create('ref_platoons', function(Blueprint $table){
            $table->string('abbr', 10);
            $table->string('name', 100); 
            $table->integer('pos'); 
            $table->primary('abbr');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('ref_misc');
        Schema::dropIfExists('ref_ranks');
        Schema::dropIfExists('ref_postings');
        Schema::dropIfExists('ref_platoons');                       
    }
}
