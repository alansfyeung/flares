<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AlterDecorationsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('decorations', function(Blueprint $table){ 
            $table->string('shortcode', 10)->nullable();
            $table->integer('service_period_months')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('users', function (Blueprint $table) {
            $tabl->dropColumn('shortcode');
            $tabl->dropColumn('service_period_months');
        });
    }
}
