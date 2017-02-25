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
            $table->string('shortcode', 10)->unique()->nullable();
            $table->text('visual')->nullable();
            $table->integer('precedence')->nullable();
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
        Schema::table('decorations', function (Blueprint $table) {
            $table->dropColumn('shortcode');
            $table->dropColumn('visual');
            $table->dropColumn('precedence');
            $table->dropColumn('service_period_months');
        });
    }
}
