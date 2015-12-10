<?php

use Illuminate\Database\Seeder;

class FlaresUserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('system_users')->insert([
            [
                'user_id' => '1', 
                'forums_username' => 'YeungA', 
                'access_level' => '500', 
                'last_login_time' => date('Y-m-d H:i:s'), 
                'fallback_pwd' => '###########'
            ]
        ]);
    }
}
