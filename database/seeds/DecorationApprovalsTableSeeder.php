<?php

use Illuminate\Database\Seeder;

class DecorationApprovalsTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $approvals = [
            [
                'regt_num' => '20610112',
                'dec_id' => '10',
                'request_comment' => null,
                'date' => '2018-01-03',
                'created_at' => date('Y-m-d'),
            ],
            [
                'regt_num' => '20610123F',
                'dec_id' => '24',
                'request_comment' => 'I got this on ceremonial parade last year',
                'date' => '2018-04-15',
                'created_at' => date('Y-m-d'),
            ],
            [
                'regt_num' => '20610123F',
                'dec_id' => '33',
                'request_comment' => 'Recruit instructor is cool',
                'date' => '2018-02-01',
                'created_at' => date('Y-m-d'),
            ],
        ];
        DB::table('decoration_approvals')->insert($approvals);

    }
}
