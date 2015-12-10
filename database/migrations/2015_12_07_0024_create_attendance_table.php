<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateAttendanceTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('attendance', function(Blueprint $table){
            $table->increments('att_id');
            $table->string('regt_num', 10);
            $table->unsignedInteger('prev_att_id')->nullable();
            $table->dateTime('date');
            $table->unsignedInteger('acty_id');
            $table->string('recorded_value', 3);
            $table->tinyInteger('is_late');
            $table->unsignedInteger('leave_id');
            $table->tinyInteger('is_sms_sent');
            $table->dateTime('sms_timestamp')->nullable();
            $table->string('sms_mobile', 12)->nullable();
            $table->text('sms_failure')->nullable();
            $table->unsignedInteger('recorded_by')->nullable();
            $table->text('comments')->nullable();
            $table->timestamps();
            
            // todo: FK to leave table
            $table->foreign('regt_num')->references('regt_num')->on('members');
            $table->foreign('prev_att_id')->references('att_id')->on('attendance');     // self-reference
            $table->foreign('acty_id')->references('acty_id')->on('activities');
            // $table->foreign('leave_id')->references_('leave_id')->on('leave');
            $table->foreign('recorded_by')->references('user_id')->on('system_users');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        if (Schema::hasTable('attendance')){
            Schema::table('attendance', function ($table) {
                $table->dropForeign('attendance_regt_num_foreign');
                $table->dropForeign('attendance_prev_att_id_foreign');
                $table->dropForeign('attendance_acty_id_foreign');
                // $table->dropForeign('attendance_leave_id_foreign');
                $table->dropForeign('attendance_recorded_by_foreign');
            });
            Schema::drop('attendance');
        }
    }
}
