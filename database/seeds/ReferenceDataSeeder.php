<?php

use Illuminate\Database\Seeder;

class ReferenceDataSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('ref_misc')->insert([
            ['name' => 'ROLL_SYMBOLS', 'value' => '/,A,S,L'],
            ['name' => 'PROFILE_UNKNOWN_IMAGE_PATH', 'value' => '/assets/img/anon.png'],
            ['name' => 'BADGE_UNKNOWN_IMAGE_PATH', 'value' => '/assets/img/unknownbadge.jpg'],
            ['name' => 'DECORATIONS_TIER_LIST', 'value' => 'A,B,C,D,E'],
            ['name' => 'DECORATIONS_TIER_A', 'value' => 'Offical AAC'],
            ['name' => 'DECORATIONS_TIER_B', 'value' => 'Unit Achievement'],
            ['name' => 'DECORATIONS_TIER_C', 'value' => 'Training'],
            ['name' => 'DECORATIONS_TIER_D', 'value' => 'Activity Participation'],
            ['name' => 'DECORATIONS_TIER_E', 'value' => 'Other Participation'],
        ]);
        
        DB::table('ref_platoons')->insert([
            ['abbr' => '1PL', 'name' => 'Senior Platoon', 'pos' => '10'],
            ['abbr' => '2PL', 'name' => 'Intermediate Platoon', 'pos' => '20'],
            ['abbr' => '3PL', 'name' => 'Recruit Platoon', 'pos' => '30'],
            ['abbr' => 'PNR', 'name' => 'Pioneers', 'pos' => '40'],
            ['abbr' => 'HQ', 'name' => 'Headquarters', 'pos' => '50'],
            ['abbr' => 'VAS', 'name' => 'Volunteer Adult Staff', 'pos' => '100'],
            ['abbr' => 'ACS', 'name' => 'Adult Cadet Staff', 'pos' => '110'],
            ['abbr' => 'D', 'name' => 'Delta Platoon', 'pos' => '200'],
        ]);
        
        DB::table('ref_postings')->insert([
            ['abbr' => 'ACS', 'name' => 'Adult Cadet Staff', 'pos' => '110'],
            ['abbr' => 'ADJT', 'name' => 'Adjutant', 'pos' => '111'],
            ['abbr' => 'ADMINO', 'name' => 'Admin Officer', 'pos' => '114'],
            ['abbr' => 'CDTTRG', 'name' => 'Cadet Training Officer', 'pos' => '50'],
            ['abbr' => 'CLK', 'name' => 'Clerk', 'pos' => '21'],
            ['abbr' => 'COY2IC', 'name' => 'Cadet Company 2IC', 'pos' => '60'],
            ['abbr' => 'COYCOMD', 'name' => 'Cadet Company Commander', 'pos' => '65'],
            ['abbr' => 'CQMS', 'name' => 'Company Quartermaster Sergeant', 'pos' => '51'],
            ['abbr' => 'CSM', 'name' => 'Comany Sergeant Major', 'pos' => '55'],
            ['abbr' => 'MBR', 'name' => 'Member', 'pos' => '0'],
            ['abbr' => 'MED', 'name' => 'Medic', 'pos' => '23'],
            ['abbr' => 'MEDOFFR', 'name' => 'Medical Officer', 'pos' => '113'],
            ['abbr' => 'OC', 'name' => 'Officer Commanding', 'pos' => '120'],
            ['abbr' => 'PLCOMD', 'name' => 'Platoon Commander', 'pos' => '40'],
            ['abbr' => 'PLSGT', 'name' => 'Platoon Sergeant', 'pos' => '30'],
            ['abbr' => 'PNR', 'name' => 'Pioneer', 'pos' => '1'],
            ['abbr' => 'PNRSGT', 'name' => 'Pioneer Sergeant', 'pos' => '31'],
            ['abbr' => 'QASST', 'name' => 'Quartermaster Storeman', 'pos' => '11'],
            ['abbr' => 'QM', 'name' => 'Quartermaster', 'pos' => '115'],
            ['abbr' => 'SEC2IC', 'name' => 'Section 2IC', 'pos' => '10'],
            ['abbr' => 'SECO', 'name' => 'Section Commander', 'pos' => '20'],
            ['abbr' => 'SIG', 'name' => 'Signaller', 'pos' => '22'],
            ['abbr' => 'TRGOFFR', 'name' => 'Training Offier', 'pos' => '112'],
            ['abbr' => 'VAS', 'name' => 'Volunteer Adult Staff', 'pos' => '100'],
        ]);
        
        DB::table('ref_ranks')->insert([
            ['abbr' => 'CDTREC', 'name' => 'Recruit', 'pos' => '10'],
            ['abbr' => 'CDT', 'name' => 'Cadet', 'pos' => '20'],
            ['abbr' => 'CDTCPL', 'name' => 'Corporal', 'pos' => '40'],
            ['abbr' => 'CDTLCPL', 'name' => 'Lance Corporal', 'pos' => '30'],
            ['abbr' => 'CDTSGT', 'name' => 'Sergeant', 'pos' => '50'],
            ['abbr' => 'CDTWO1', 'name' => 'Warrant Officer Class 1', 'pos' => '65'],
            ['abbr' => 'CDTWO2', 'name' => 'Warrant Officer Class 2', 'pos' => '60'],
            ['abbr' => 'CPL (AAC)', 'name' => 'Corporal (AAC)', 'pos' => '130'],
            ['abbr' => 'CUO', 'name' => 'Cadet Under Officer', 'pos' => '70'],
            ['abbr' => 'LCPL (AAC)', 'name' => 'Lance Corporal (AAC)', 'pos' => '120'],
            ['abbr' => '2LT (AAC)', 'name' => 'Second Lieutenant (AAC)', 'pos' => '200'],
            ['abbr' => 'LT (AAC)', 'name' => 'Lieutenant (AAC)', 'pos' => '210'],
            ['abbr' => 'MAJ (AAC)', 'name' => 'Major (AAC)', 'pos' => '220'],
            ['abbr' => 'RAH', 'name' => 'Registered Adult Helper', 'pos' => '100'],
            ['abbr' => 'SGT (AAC)', 'name' => 'Sergeant (AAC)', 'pos' => '140'],
            ['abbr' => 'UA', 'name' => 'Unit Assistant', 'pos' => '110'],
            ['abbr' => 'VAS', 'name' => 'Volunteer Adult Staff', 'pos' => '100'],
            ['abbr' => 'CAPT (AAC)', 'name' => 'Captain (AAC)', 'pos' => '210'],
            ['abbr' => 'WO1 (AAC)', 'name' => 'Warrant Officer Class 1 (AAC)', 'pos' => '155'],
            ['abbr' => 'WO2 (AAC)', 'name' => 'Warrant Officer Class 2 (AAC)', 'pos' => '150'],
        ]);
    }
}
