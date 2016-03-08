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
        DB::table('users')->insert([
            [
                'user_id' => '1', 
                'username' => 'SystemDefault', 
                'email' => 'alan.yeung@206acu.org.au',
                'access_level' => '500', 
                'last_login_time' => date('Y-m-d H:i:s'), 
                'password' => bcrypt(config('app.flares.fallback_password')),
            ]
        ]);
    }
}
