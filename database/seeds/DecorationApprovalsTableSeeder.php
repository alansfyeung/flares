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
                'dec_id' => '9',
                'request_comment' => null,
                'date' => '2018-01-03',
            ],
            [
                'regt_num' => '20610123F',
                'dec_id' => '25',
                'request_comment' => 'I got this on ceremonial parade last year',
                'date' => '2018-04-15',
            ],
            [
                'regt_num' => '20610123F',
                'dec_id' => '34',
                'request_comment' => 'Recruit instructor is cool',
                'date' => '2018-02-01',
            ],
        ];
        DB::table('decoration_approvals')->insert($approvals);

    }
}
