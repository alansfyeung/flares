<?php

use Illuminate\Database\Seeder;

use Illuminate\Database\Eloquent\Model;

class Truncate extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        Model::unguard();
        
        DB::select('SET FOREIGN_KEY_CHECKS = 0;');
        
        DB::table('posting_promo')->truncate();
        DB::table('activities')->truncate();
        DB::table('attendance')->truncate();
        DB::table('leave')->truncate();
        DB::table('member_idcards')->truncate();
        DB::table('member_payments')->truncate();
        DB::table('member_pictures')->truncate();
        DB::table('members')->truncate();
        DB::table('ref_misc')->truncate();
        DB::table('ref_platoons')->truncate();
        DB::table('ref_postings')->truncate();
        DB::table('ref_ranks')->truncate();
        DB::table('users')->truncate();
        
        DB::select('SET FOREIGN_KEY_CHECKS = 1;');
        
        Model::reguard();
    }
}
