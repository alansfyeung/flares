<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreatePostingPromoTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('posting_promo', function(Blueprint $table){
            $table->increments('posting_id');
            $table->string('regt_num', 10);  
            $table->dateTime('effective_date'); 
            $table->string('new_platoon', 10)->nullable();
            $table->string('new_posting', 10)->nullable();
            $table->string('new_rank', 10)->nullable();
            $table->tinyInteger('is_acting')->default(0);
            $table->string('promo_auth', 10)->nullable();
            $table->tinyInteger('is_discharge')->default(0);
            $table->unsignedInteger('recorded_by');
            $table->timestamps();
            
            $table->foreign('regt_num')->references('regt_num')->on('members')->onDelete('cascade');
            $table->foreign('new_platoon')->references('abbr')->on('ref_platoons')->onDelete('no action');
            $table->foreign('new_posting')->references('abbr')->on('ref_postings')->onDelete('no action');
            $table->foreign('new_rank')->references('abbr')->on('ref_ranks')->onDelete('no action');
            $table->foreign('recorded_by')->references('user_id')->on('system_users')->onDelete('no action');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        if (Schema::hasTable('posting_promo')){
            Schema::table('posting_promo', function ($table) {
                $table->dropForeign('posting_promo_regt_num_foreign');
                $table->dropForeign('posting_promo_new_platoon_foreign');
                $table->dropForeign('posting_promo_new_posting_foreign');
                $table->dropForeign('posting_promo_new_rank_foreign');
                $table->dropForeign('posting_promo_recorded_by_foreign');
            });
            Schema::drop('posting_promo');
        }
    }
}
