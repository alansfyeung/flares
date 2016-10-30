<?php

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        Model::unguard();
        // $this->call(UserTableSeeder::class);
        $this->call(ReferenceDataSeeder::class);
        $this->call(FlaresUserSeeder::class);
        $this->call(MembersTableLiteSeeder::class);
        Model::reguard();    
    }
}
