<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateMembersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('members', function(Blueprint $table){
            $table->string('regt_num', 10);
            $table->string('first_name');
            $table->string('last_name');
            $table->string('sex', 1)->nullable();
            $table->date('dob')->nullable();
            $table->string('forums_username')->unique()->nullable();
            $table->integer('forums_userid')->unique()->nullable();
            $table->string('coms_username')->nullable();
            $table->integer('coms_id')->unique()->nullable();
            $table->string('role_class')->nullable();
            $table->string('member_mobile', 12)->nullable();
            $table->string('member_email', 255)->nullable();
            $table->string('street_addr')->nullable();
            $table->string('suburb')->nullable();
            $table->string('state', 3)->nullable();
            $table->integer('postcode')->nullable();
            $table->string('home_phone', 12)->nullable();
            $table->string('school')->nullable();
            $table->string('parent_email', 255)->nullable();
            $table->string('parent_mobile', 12)->nullable();
            $table->string('parent_type')->nullable();
            $table->string('parent_custodial')->nullable();
            $table->string('parent_preferred_comm')->nullable();
            $table->string('med_allergies')->nullable();
            $table->string('med_cond')->nullable();
            $table->string('sdr')->nullable();
            $table->tinyInteger('is_med_lifethreat')->default(0);
            $table->tinyInteger('is_med_hmp')->default(0);
            $table->tinyInteger('is_qual_mb')->default(0);
            $table->tinyInteger('is_qual_s303')->default(0);
            $table->tinyInteger('is_qual_gf')->default(0);
            $table->tinyInteger('is_active')->default(0);
            $table->tinyInteger('is_fully_enrolled')->default(0);
            $table->timestamps();
            $table->softDeletes();
            
            $table->primary('regt_num');
        });
        
        Schema::create('member_pictures', function(Blueprint $table){
            $table->increments('img_id');
            $table->string('regt_num');
            $table->binary('photo_blob');
            $table->text('caption')->nullable();
            $table->string('file_name')->nullable();
            $table->string('mime_type');
            $table->bigInteger('file_size');
            $table->timestamps();
            
            $table->foreign('regt_num')->references('regt_num')->on('members');
        });
     
        Schema::create('member_idcards', function(Blueprint $table){
            $table->increments('idcard_id');
            $table->string('regt_num');
            $table->tinyInteger('is_idcard_printed')->default(0);
            $table->date('expiry')->nullable();
            $table->tinyInteger('is_with_bn')->default(0);
            $table->string('serial_num');
            $table->text('remarks');
            $table->unsignedInteger('prev_idcard_id')->nullable();
            $table->timestamps();
            
            $table->foreign('regt_num')->references('regt_num')->on('members');
            $table->foreign('prev_idcard_id')->references('idcard_id')->on('member_idcards');
        });
        
        Schema::create('member_payments', function(Blueprint $table){
            $table->increments('pymt_id');
            $table->string('regt_num');
            $table->string('item');
            $table->string('pymt_type', 3);
            $table->string('pymt_class', 5);
            $table->decimal('amount', 5, 2);
            $table->string('refund_reason')->nullable();
            $table->unsignedInteger('recorded_by');
            $table->text('comment');
            $table->timestamps();
            
            $table->foreign('regt_num')->references('regt_num')->on('members');
            $table->foreign('recorded_by')->references('user_id')->on('users');
        });
        
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {   
        if (Schema::hasTable('member_pictures')){
            Schema::table('member_pictures', function ($table) {
                $table->dropForeign('member_pictures_regt_num_foreign');
            });
            Schema::drop('member_pictures');
        }
        
        if (Schema::hasTable('member_idcards')){
            Schema::table('member_idcards', function ($table) {
                $table->dropForeign('member_idcards_regt_num_foreign');
                $table->dropForeign('member_idcards_prev_idcard_id_foreign');
            });
            Schema::drop('member_idcards');
        }
        
        if (Schema::hasTable('member_payments')){
            Schema::table('member_payments', function ($table) {
                $table->dropForeign('member_payments_regt_num_foreign');
                $table->dropForeign('member_payments_recorded_by_foreign');
            });
            Schema::drop('member_payments');
        }
        
        Schema::drop('members');
    }
}
