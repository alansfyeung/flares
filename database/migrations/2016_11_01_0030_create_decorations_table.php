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
            $table->text('desc')->nullable();
            
            // Image as blob
            $table->binary('badge_blob')->nullable();
            $table->string('badge_mime_type', 255)->nullable();
            
            // Image as URL
            $table->string('badge_uri', 255)->nullable();
            $table->integer('badge_w')->nullable();
            $table->integer('badge_h')->nullable();
            
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
        Schema::table('decorations', function(Blueprint $table){
            DB::connection()->getPdo()->exec('ALTER TABLE `decorations` CHANGE `badge_blob` `badge_blob` LONGBLOB');
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
