<?php

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AlterMembersTable extends Migration
{
    /**
     * On 2018-12-27 we decided to cut out a whole bunch of useless
     * stuff in member records. 
     *
     * @return void
     */
    public function up()
    {
        
        Schema::table('members', function (Blueprint $table) {
            $table->dateTime('joined_at')->nullable();
            $table->tinyInteger('is_enrolled')->default(0);
        });

        // We are going to get rid of is_active and is_fully_enrolled... copy values over to is_enrolled
        DB::statement('update `members` set is_enrolled=is_active');

        Schema::table('members', function (Blueprint $table) {
            $table->dropColumn('is_qual_mb');
            $table->dropColumn('is_qual_s303');
            $table->dropColumn('is_qual_gf');
            $table->dropColumn('is_active');
            $table->dropColumn('is_fully_enrolled');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('members', function(Blueprint $table) { 
            $table->tinyInteger('is_qual_mb')->default(0);
            $table->tinyInteger('is_qual_s303')->default(0);
            $table->tinyInteger('is_qual_gf')->default(0);
            $table->tinyInteger('is_active')->default(0);
            $table->tinyInteger('is_fully_enrolled')->default(0);
        });

        // Reverse of the up()
        DB::statement('update `members` set is_active=is_enrolled');

        Schema::table('members', function(Blueprint $table) { 
            $table->dropColumn('joined_at');
            $table->dropColumn('is_enrolled');
        });
    }
}
