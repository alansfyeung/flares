<?php

use Illuminate\Database\Seeder;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('users')->insert([
            [
                'user_id' => '1', 
                'username' => 'SystemDefault', 
                'access_level' => '99', 
                'last_login_time' => date('Y-m-d H:i:s'), 
                'password' => '###########'
            ]
        ]);
    }
}