<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use DB;
use App\Member;
use App\Http\Requests;
use App\Http\Controllers\Controller;

class MemberController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return Response
     */
    public function index(Request $request){
        $query = Member::select();
		
		foreach($request->only('rank', 'last_name', 'first_name', 'regt_num') as $name => $input){
			$input = trim($input);
			if (!empty($input)){
				$query->where($name, 'like', "$input%");
				$query->orderBy($name, 'asc');
			}
		}
		foreach($request->only('sex', 'is_active') as $name => $input){
			$input = trim($input);
			if (!empty($input)){
				$query->where($name, $input);
			}
		}
		
		// return var_dump($input);
		$res = $query->get();
		return response()->json($res);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return Response
     */
    public function create(){
        // =================
		// FLARES doesn't use this
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  Request  $request
     * @return Response
     */
    public function store(Request $request){
		$postData = $request->all();
		$recordId = 0;
		$initialPostingId = 0;
		$idNotPossibleError = [];
		
		// Deal with the context data
		$context = $this->getContextDefaults();
		if (is_array($postData) && array_key_exists('context', $postData)){
			$contextDefaults = $this->getContextDefaults($postData['context']['name']);
			
			if ($postData['context']['hasOverrides']){
				$massOverrideKeys = ['newRank', 'newPlatoon', 'newPosting'];
				foreach ($massOverrideKeys as $overrideKey){
					if (array_key_exists($overrideKey, $postData['context'])){
						$contextDefaults[$overrideKey] = $postData['context'][$overrideKey];
					}
				}
				if (array_key_exists('thisYear', $postData['context'])){
					$contextDefaults['thisYear'] = substr($postData['context']['thisYear'], 2, 2);
				}
				if (array_key_exists('thisCycle', $postData['context'])){
					$contextDefaults['thisCycle'] = substr($postData['context']['thisCycle'], 0, 1);
				}
			}
			
			$context = $contextDefaults;
		}
		
		// if (rand(0,2) > 0){
			// return response()->json([
				// 'recordId' => 0,
				// 'initialPostingId' => 0,
				// 'regtNumError' => ['code' => 'NO_REGT_NUM', 'reason' => 'Test Reason']
			// ]);
		// }
		
		// Deal with the member data
		if (is_array($postData) && array_key_exists('member', $postData)){
			// $newMember = $postData['member'];
			// foreach ($postData['newMembers'] as $newMember){
			try {
				// Get their regimental number
				$newRegtNum = $this->generateStandardRegtNumber($context) . ($postData['member']['sex'] == 'F' ? 'F' : '');	
				
				if (!empty($newRegtNum)){
					
					// Assign regt num, create new record, and generate initial posting
					$postData['member']['regt_num'] = $newRegtNum;
					
					$newMember = Member::create($postData['member']);
					// $newMember = new Member();
					// $newMember->fill();
					// $newMember->createWithRegtNum($newRegtNum);
					
					// print_r($newMember);
					// print_r($newMember->regt_num);
					// exit;
					
					if ($newMember->regt_num > 0){
						$idNotPossibleError = [];
						$recordId = $newRegtNum;
						$initialPostingId = $this->generateInitialPostingRecord($recordId, $context);
					}
					
				}
				else {
					$idNotPossibleError = ['code' => 'NO_REGT_NUM', 'reason' => 'Could not generate a regt num'];
				}
				
			}
			catch (Exception $ex){
				$idNotPossibleError = ['code' => 'EX', 'reason' => $ex->getMessage()];
			}	
			// }
			
			return response()->json([
				'recordId' => $recordId,
				'initialPostingId' => $initialPostingId,
				'regtNumError' => $idNotPossibleError
			]);
		}
		
		return abort(400);		
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return Response
     */
    public function show(Request $request, $id){
		
		$detailLevel = 0;
		$getParams = $request->all();
		if (is_array($getParams) && array_key_exists('detail', $getParams)){
			$detailLevel = $getParams['detail'];
		}
		
		// var_dump($getParams);
		// var_dump($detailLevel);
		
		// Fetch the first match
		$member = Member::findOrFail($id);
		
		if ($detailLevel === 'full'){	
			$ret = $member->toArray();
			$ret['posting_promo'] = $member->postings->toArray();
			return response()->json($ret);
		}
		else {
			return response()->json($member->toArray()); 
		}

		return abort(404);		// Didn't find any record
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return Response
     */
    public function edit($id){
        // =================
		// FLARES doesn't use this
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  Request  $request
     * @param  int  $id
     * @return Response
     */
    public function update(Request $request, $id){
        $postData = $request->all();
		$updated = 0;
		$updateNotPossibleError = [];
		
		if (is_array($postData) && array_key_exists('member', $postData)){
			try {
				$updateMember = $postData['member'];
				// $updated = Member::find($id)->update($updateMember);				
				$updated = Member::updateOrCreate(['regt_num' => $id], $updateMember);
				// $updated = DB::table('master')->where('regt_num', $id)->update($updateMember);
			}
			catch (Exception $ex){
				$updateNotPossibleError = ['code' => 'EX', 'reason' => $ex->getMessage()];
			}
			
			return response()->json([
				'recordId' => $updated,
				'updateError' => $updateNotPossibleError
			]);
		}
		
		return abort(400);	
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return Response
     */
    public function destroy($id){
        $postData = $request->all();
		$updated = [];
		$updateNotPossible = [];
		
		if (is_array($postData) && array_key_exists('member', $postData)){
			$updated[] = Member::find($id)->delete();
			// $updated[] = DB::table('master')->where('regt_num', $id)->update(['is_active' => 0]);
			return response()->json([
				'success' => $updated,
				'regtNumError' => $updateNotPossible
			]);
		}
		
		return abort(400);		// $request should've been an array of arrays..        
    }
	
	
	/* ==================================================
	 *  Onboarding functionality
	 */
	 
	 
	protected function getContextDefaults($contextName = 'newRecruitment'){
		// Allowed $contextName values are: 
		// newRecruitment, newTransfer, newVolunteerStaff, newAdultCadetStaff
		
		$thisYear = substr(date('Y'), 2, 2);
		$thisCycle = intval(date('n')) < 7 ? 1 : 2;
		
		if ($contextName == 'newTransfer'){
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
				'newRank' => 'REC',
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
			'newRank' => 'REC',
			'thisYear' => $thisYear,
			'thisCycle' => $thisCycle,
			'generateForumsAccounts' => false
		];
		
	}
	
	protected function generateStandardRegtNumber($opts = 0){
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
			$res = DB::table('master')->whereIn('regt_num', [$proposedRegtNum, $proposedRegtNum . 'F'])->first();
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
	
	protected function generateInitialPostingRecord($id, $opts = 0){
		/*
		 * Place as: 
		 * Platoon=3PL, Rank=REC, Posting=MBR
		 */
		 
		// TODO check for overrides to the above values in $opts
		$effectiveDate = date('Y-m-d');
		$recordedBy = 'YeungA';
		
		// Extract the overrides
		if (is_array($opts)){
			extract($opts);
		}
		
		$postingRecord = [
			'regt_num' => $id,
			'effective_date' => $effectiveDate,
			'new_platoon' => $newPlatoon,
			'new_posting' => $newPosting,
			'new_rank' => $newRank,
			'promo_auth' => 'OC',
			'recorded_by' => $recordedBy
		];
		$id = DB::table('posting_promo')->insertGetId($postingRecord);		
		return $id;
	}
	
	
	protected function generateForumsAccount(){
		
	}
	
	
}
