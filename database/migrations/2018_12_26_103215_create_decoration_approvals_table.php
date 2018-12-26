<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateDecorationApprovalsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('decoration_approvals', function (Blueprint $table) {
            $table->increments('dec_appr_id');
            $table->string('regt_num', 10);
            $table->unsignedInteger('dec_id');
            $table->text('request_comment')->nullable();
            $table->dateTime('date')->nullable();
            $table->tinyInteger('is_approved')->nullable();
            $table->text('justification')->nullable();
            $table->unsignedInteger('user_id')->nullable();
            $table->dateTime('decision_date')->nullable();
            $table->timestamps();

            $table->foreign('regt_num')->references('regt_num')->on('members');
            $table->foreign('dec_id')->references('dec_id')->on('decorations');
            $table->foreign('user_id')->references('user_id')->on('users');

        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        if (Schema::hasTable('decoration_approvals')){
            Schema::table('decoration_approvals', function ($table) {
                $table->dropForeign('decoration_approvals_regt_num_foreign');
                $table->dropForeign('decoration_approvals_dec_id_foreign');
                $table->dropForeign('decoration_approvals_user_id_foreign');
            });
            Schema::drop('decoration_approvals');
        }
    }
}
