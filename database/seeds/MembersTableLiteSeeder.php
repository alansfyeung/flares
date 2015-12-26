<?php

use Illuminate\Database\Seeder;

class MembersTableLiteSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        // Member insert
        $json = '[{"regt_num":"20610203","first_name":"Sebastian","last_name":"Bird","sex":"M","dob":"1997-08-30","forums_username":null,"forums_userid":null,"coms_username":null,"coms_id":null,"member_mobile":"","member_email":"selby_rulz@hotmail.com","street_addr":"1 Ashley Grove","suburb":" Gordon","state":"NSW","postcode":"2072","home_phone":"8901 3657","school":"Marist College","parent_email":"NA","parent_mobile":"0400 243 283","parent_type":"","parent_custodial":"","parent_preferred_comm":null,"med_allergies":"NKA","med_cond":"Nil","sdr":"Full","is_med_lifethreat":"0","is_med_hmp":"0","is_qual_mb":"0","is_qual_s303":"0","is_qual_gf":"0","created_at":"2015-08-22 15:35:05","is_active":"1","is_fully_enrolled":"0","updated_at":"0000-00-00 00:00:00","deleted_at":null},{"regt_num":"20611206","first_name":"Remy","last_name":"Danoy","sex":"M","dob":"1997-06-15","forums_username":null,"forums_userid":null,"coms_username":null,"coms_id":null,"member_mobile":"","member_email":"jc.dana@zspace.com.au","street_addr":"12 Bennett Place","suburb":" Maroubra","state":"NSW","postcode":"2035","home_phone":"9212 6777","school":"","parent_email":"NA","parent_mobile":"0407 220 965","parent_type":"","parent_custodial":"","parent_preferred_comm":null,"med_allergies":"NA","med_cond":"NA","sdr":"NA","is_med_lifethreat":"0","is_med_hmp":"0","is_qual_mb":"0","is_qual_s303":"0","is_qual_gf":"0","created_at":"2015-08-22 15:35:05","is_active":"1","is_fully_enrolled":"0","updated_at":"0000-00-00 00:00:00","deleted_at":null},{"regt_num":"20611407","first_name":"Reagan","last_name":"Chu","sex":"M","dob":"1998-08-20","forums_username":"ChuR","forums_userid":"548","coms_username":null,"coms_id":null,"member_mobile":"0406 766 297","member_email":"pandapoacher24@gmail.com","street_addr":"7 \/ 15-17 Milner Road","suburb":" Artarmon","state":"NSW","postcode":"2064","home_phone":"9412 2609","school":"St Pius X College","parent_email":"Janesuchu2006@yahoo.com.au","parent_mobile":"0412 989 939","parent_type":"Mother","parent_custodial":"Mother - only guardian","parent_preferred_comm":null,"med_allergies":"NKA","med_cond":"Nil","sdr":"Full","is_med_lifethreat":"0","is_med_hmp":"0","is_qual_mb":"0","is_qual_s303":"0","is_qual_gf":"0","created_at":"2015-08-22 15:35:05","is_active":"1","is_fully_enrolled":"0","updated_at":"2015-08-30 02:07:33","deleted_at":null}]';
        
        $decoded = json_decode($json);
        array_walk($decoded, function(&$object){
            $object = (array) $object;
        });
        
        DB::table('members')->insert($decoded);
        
        // Create rank records for these people
        $rankRecords = [
            [
                'regt_num' => '20610203',
                'effective_date' => '2015-12-25',
                'new_platoon' => '3PL',
                'new_posting' => 'MBR', 
                'new_rank' => 'CDTREC', 
                'promo_auth' => 'OC',
                'recorded_by' => '1'
            ],
            [
                'regt_num' => '20611206',
                'effective_date' => '2014-12-25',
                'new_platoon' => '3PL',
                'new_posting' => 'MBR', 
                'new_rank' => 'CDTREC', 
                'promo_auth' => 'OC',
                'recorded_by' => '1'
            ], 
            [
                'regt_num' => '20611206',
                'effective_date' => '2015-12-25',
                'new_platoon' => '3PL',
                'new_posting' => 'SECO', 
                'new_rank' => 'CDTCPL', 
                'promo_auth' => 'OC',
                'recorded_by' => '1'
            ],
            [
                'regt_num' => '20611407',
                'effective_date' => '2015-12-25',
                'new_platoon' => '3PL',
                'new_posting' => 'MBR', 
                'new_rank' => 'CDTREC', 
                'promo_auth' => 'OC',
                'recorded_by' => '1'
            ]
        ];
        
        DB::table('posting_promo')->insert($rankRecords);
        
    }
}
