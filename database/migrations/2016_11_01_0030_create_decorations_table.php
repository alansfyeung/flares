<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateDecorationsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('decorations', function(Blueprint $table){
            $table->increments('dec_id');
            $table->string('name', 100);
            $table->string('tier', 100);
            $table->text('desc');
            
            // Image as blob
            $table->binary('icon_blob')->nullable();
            $table->string('icon_mime_type', 255)->nullable();
            
            // Image as URL
            $table->string('icon_uri', 255)->nullable();
            $table->integer('icon_w')->nullable();
            $table->integer('icon_h')->nullable();
            
            // Couple with forums special rank table
            $table->integer('forums_special_rank_id')->nullable();
            
            // Authority
            $table->string('authorized_by', 255)->nullable();
            
            // Must have a commence date; null conclude date implies perpetual
            $table->dateTime('date_commence');
            $table->dateTime('date_conclude')->nullable();
            
            // Timestamps
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
        Schema::drop('decorations');
    }
}
