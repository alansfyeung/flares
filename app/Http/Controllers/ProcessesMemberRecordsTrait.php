<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use DB;
use App\Member;
use App\Http\Requests;
use App\Http\Controllers\Controller;

trait ProcessesMemberRecordsTrait
{	
	//==================================================
	// Statistical functionality
	//==================================================

	/**
     * Returns the completion status for this member record
     *
     * @param  int  $id
     * @return Response
     */
    public function status(Request $request, $id)
	{
		return response()->json([
			'completionPercentage' => 100
		]);
	}
	
	protected function keywordLikeRegtNum($keyword)
	{
		return preg_match('/206\d{3,5}F?/', $keyword);
	}
	
	protected function keywordExtractRank($keyword)
	{
		$ranks = DB::table('ref_ranks')->orderBy('pos', 'asc')->get();
		foreach ($ranks as $rank){
			$clean = preg_replace('/\s+/', '', $keyword);
			$clean = strtoupper($clean);
			$abbr = strtoupper($rank->abbr);
			
			// maybe direct match
			if ($abbr == $clean){
				return $rank->abbr;
			}
			// maybe in format "CDTCPL"
			if (strlen($abbr) > 3 && substr($abbr, 3) === $clean){
				return $rank->abbr;
			}
			// maybe in format "CAPT (AAC)"
			if (preg_replace('/\(AAC\)/', '', $abbr) === $clean){
				return $rank->abbr;
			}
			
		}
		return null;
	}

	protected function keywordLikeRank($keyword)
	{
		$extractedKeyword = $this->keywordExtractRank($keyword);
		return $extractedKeyword != null;
	}


	//==================================================
	// Onboarding functionality
	//==================================================
	 
	/**
     * Retrieve the default settings based on a Context name
     *
     * @param  string $contextName
     * @return Array
     */ 
	protected function getContextDefaults($contextName = 'newRecruitment')
	{
		// Allowed $contextName values are: 
        // CTX_NEW_RECRUITMENT newRecruitment, 
        // CTX_NEW_TRANSFER newTransfer, 
        // CTX_NEW_DAH newVolunteerStaff, 
        // CTX_NEW_ACS newAdultCadetStaff
		
		$thisYear = substr(date('Y'), 2, 2);
		$thisCycle = intval(date('n')) < 7 ? 1 : 2;
		
		if ($contextName == 'newRecruitment'){
			return [
				'newPlatoon' => '1PL',
				'newPosting' => 'MBR',
				'newRank' => 'CDT',
				'thisYear' => $thisYear,
				'thisCycle' => $thisCycle,
				'generateForumsAccounts' => true
			];
		}
		
		if ($contextName == 'newVolunteerStaff'){
			return [
				'newPlatoon' => 'VAS',
				'newPosting' => 'VAS',
				'newRank' => 'CDTREC',
				'thisYear' => $thisYear,
				'thisCycle' => $thisCycle,
				'generateForumsAccounts' => false
			];
		}
		
		if ($contextName == 'newAdultCadetStaff'){
			return [
				'newPlatoon' => 'ACS',
				'newPosting' => 'ACS',
				'newRank' => 'UA',
				'thisYear' => $thisYear,
				'thisCycle' => $thisCycle,
				'generateForumsAccounts' => false
			];
		}
		
		// Default: Return as newRecruitment
		return [
			'newPlatoon' => '3PL',
			'newPosting' => 'MBR',
			'newRank' => 'CDTREC',
			'thisYear' => $thisYear,
			'thisCycle' => $thisCycle,
			'generateForumsAccounts' => false
		];
		
	}
	
	/**
     * Generate a regimental number
     *
     * @param  Array $opts
     * @return String
     */ 
	protected function generateStandardRegtNumber($opts = 0)
	{
		/* 
		 * Regimental numbers take the following format
		 * 206  prefix for all 
		 * 115  (1st of 2015) if after 2010; 81 (first of 2008) if pre-2010
		 * 00  denoting their order in the roll
		 * [F] if female  -- this function doesn't take notice of those
		 */
		
		// Extract the overrides
		if (is_array($opts)){
			extract($opts);
		}
		
		$prefix = '206' . $thisCycle . $thisYear;
		
		// Lookup the index for this number
		$res = DB::table('ref_misc')->where('name', 'regtNumLast')->where('cond', $prefix)->first();
		if (sizeof($res) > 0){
			$refDataRowId = $res->id;
			$lastIndex = intval($res->value);
			$nextIndex = $lastIndex + 1;
		}
		else {
			$nextIndex = 0;
		}
		
		// Check for no conflict - try a few
		$proposedRegtNum = $prefix . str_pad($nextIndex, 2, '0', STR_PAD_LEFT);
		$counterNoConflict = 0;
		while ($counterNoConflict < 10){
			$res = Member::select()->whereIn('regt_num', [$proposedRegtNum, $proposedRegtNum . 'F'])->first();
			// $res = DB::table('members')->whereIn('regt_num', [$proposedRegtNum, $proposedRegtNum . 'F'])->first();
			if (sizeof($res) > 0){
				// Is conflicted; try next index
				$counterNoConflict++;
				$proposedRegtNum = $prefix . str_pad(++$nextIndex, 2, '0', STR_PAD_LEFT);
				continue;
			}
			break;
		}
		
		if ($counterNoConflict >= 10){	// The no conflict attempts didn't work
			return false;
		}
		
		if (isset($refDataRowId)){
			// Update the ref_misc table with this latest regtNumLast index
			$res = DB::table('ref_misc')->where('id', $refDataRowId)->update(['value' => $nextIndex]);			
		}
		else {
			// Insert it cos it doesn't exist yet
			$res = DB::table('ref_misc')->insert(['name' => 'regtNumLast', 'value' => $nextIndex, 'cond' => $prefix]);
		}
		
		
		return $proposedRegtNum;
	}
	
	
	/**
     * Generate a regimental number
     *
     * @param  String $id
     * @param  Array $opts
     * @return String
     */ 
	protected function generateInitialPostingRecord($id, $opts = null)
	{
		/*
		 * Place as: 
		 * Platoon=3PL, Rank=CDTREC, Posting=MBR
		 */
		 
		// TODO check for overrides to the above values in $opts
		$effectiveDate = date('Y-m-d');
		$promoAuth = 'OC';
		$recordedBy = '1';				// TODO: Temp, replce this recorded by
		
		if (is_array($opts)){
			// overwrite vars above with the overrides
			extract($opts);
            
            // Cleanup
            if (is_array($newPlatoon)){
                $newPlatoon = $newPlatoon['abbr'];
            }
            if (is_array($newPosting)){
                $newPosting = $newPosting['abbr'];
            }
            if (is_array($newRank)){
                $newRank = $newRank['abbr'];
            }
		}
        
		$postingRecord = [
			'regt_num' => $id,
			'effective_date' => $effectiveDate,
			'new_platoon' => $newPlatoon,
			'new_posting' => $newPosting,
			'new_rank' => $newRank,
			'promo_auth' => $promoAuth,
			'recorded_by' => $recordedBy
		];
        
		$id = DB::table('posting_promo')->insertGetId($postingRecord);		
		return $id;
	}
	
	/**
     * Generate a PostingPromo record representing the discharge
     *
     * @param  String $id
     * @param  Array $opts
     * @return String
     */ 
	protected function generateDischargePostingRecord($id, $opts = 0)
	{
		/*
		 * We need to add a promo_posting record that is ticked as "is_discharge"
		 */
		
		$effectiveDate = date('Y-m-d');
		$promoAuth = 'OC';
		$recordedBy = 1;				// TODO: Temp, replce this value dynamically 
		$dischargeRank = null;
		
		if (is_array($opts)){
			// overwrite vars above with the overrides
			extract($opts);
		} 
		 
		$postingRecord = [
			'is_discharge' => 1,
			'regt_num' => $id,
			'effective_date' => $effectiveDate,
			'new_rank' => $dischargeRank,
			'promo_auth' => 'OC',
			'recorded_by' => $recordedBy
		];
		$id = DB::table('posting_promo')->insertGetId($postingRecord);		// TODO - use the PostingPromo model to do this
		return $id;
	}
	
    
    /**
     * Transform a flattened list of members
     *
     * @param  Array &$members
     * @return void
     */ 
    protected function transformMembersPostingPromo(&$members)
    {
        foreach ($members as &$member){
            // var_dump($member);
            if (!empty($member['current_posting'])){
                $member['current_posting'] = [
                    'effective_date' => $member['current_posting']['effective_date'],
                    'posting' => $member['current_posting']['new_posting'],
                    'is_discharge' => $member['current_posting']['is_discharge']
                ];
                // $member->current_posting = [
                    // 'effective_date' => $member->current_posting->effective_date,
                    // 'posting' => $member->current_posting->new_posting,
                    // 'is_discharge' => $member->current_posting->is_discharge
                // ];
            }
            if (!empty($member['current_rank'])){
                $member['current_rank'] = [
                    'effective_date' => $member['current_rank']['effective_date'],
                    'rank' => $member['current_rank']['new_rank'],
                    'is_acting' => $member['current_rank']['is_acting']
                ];
            }
            if (!empty($member['current_platoon'])){
                $member['current_platoon'] = [
                    'effective_date' => $member['current_platoon']['effective_date'],
                    'platoon' => $member['current_platoon']['new_platoon']
                ];
            }
        }
	}
    
    
	protected function generateForumsAccount(){
		
	}
	
	
}
