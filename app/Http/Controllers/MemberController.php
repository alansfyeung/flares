<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use DB;
use App\Member;
use App\Http\Requests;
use App\Http\Controllers\Controller;

class MemberController extends MemberControllerBase
{
    /**
     * Display a listing of the resource.
     *
     * @return Response
     */
    public function index(Request $request){
		
		// discharged -- [ none | include | only ]
		$withDischarged = $request->input('discharged', 'none');
		if ($withDischarged == 'include'){
			$query = Member::withTrashed();
		}
		if ($withDischarged == 'only'){
			$query = Member::onlyTrashed();
		}
		else {
			$query = Member::select();
		}
		
		foreach($request->only('rank', 'last_name', 'first_name', 'regt_num') as $name => $input){
			$input = trim($input);
			if (!empty($input)){
				$query->where($name, 'like', "$input%");
				$query->orderBy($name, 'asc');
			}
		}
		foreach($request->only('sex') as $name => $input){			// ('sex', 'is_active')
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
     * Store a newly created resource in storage.
     *
     * @param  Request  $request
     * @return Response
     */
    public function store(Request $request){
		$recordId = 0;
		$initialPostingId = 0;
		$idNotPossibleError = [];
		
		// Deal with the context data
		$context = $this->getContextDefaults();
		if ($request->has('context')){
			$postDataContext = $request->input('context');
			if (array_key_exists('name', $postDataContext)){
				$contextDefaults = $this->getContextDefaults($postDataContext['name']);
			}
			else {
				$contextDefaults = $this->getContextDefaults();
			}
			
			if (array_key_exists('hasOverrides', $postDataContext)){
				$massOverrideKeys = ['newRank', 'newPlatoon', 'newPosting'];
				foreach ($massOverrideKeys as $overrideKey){
					if (array_key_exists($overrideKey, $postDataContext)){
						$contextDefaults[$overrideKey] = $postDataContext[$overrideKey];
					}
				}
				if (array_key_exists('thisYear', $postDataContext)){
					$contextDefaults['thisYear'] = substr($postDataContext['thisYear'], 2, 2);
				}
				if (array_key_exists('thisCycle', $postDataContext)){
					$contextDefaults['thisCycle'] = substr($postDataContext['thisCycle'], 0, 1);
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
		if ($request->has('member')){
			$postDataMember = $request->input('member');
			try {
				// Get their regimental number
				$newRegtNum = $this->generateStandardRegtNumber($context) . ($postDataMember['sex'] == 'F' ? 'F' : '');	
				
				if (!empty($newRegtNum)){
					
					// Assign regt num, create new record, and generate initial posting
					$postDataMember['regt_num'] = $newRegtNum;
					
					$newMember = Member::create($postDataMember);
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
		
		// Fetch the first match
		// detail -- [ high | med | low ]
		$member = Member::withTrashed()->where('regt_num', $id)->firstOrFail();
		$detailLevel = $request->input('detail', 'low');
		
		if ($detailLevel == 'high' || $detailLevel == 'med'){	
			$ret = $member->toArray();
			$ret['posting_promo'] = $member->postings->toArray();
			return response()->json($ret);
		}
		else {
			//  when $detailLevel == low)
			return response()->json($member->toArray()); 
		}

		return abort(404);		// Didn't find any record
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
	 * Defaults to a soft delete; add ?remove=permanent to do hard delete
     *
     * @param  int  $id
     * @return Response
     */
    public function destroy(Request $request, $id){
		// remove -- [ discharge | permanent ]
		$removeMode = $request->input('remove', 'discharge');
		$updated = 0;
		$deletionNotPossibleError = [];
		
		try {
			if ($removeMode == 'permanent'){
				$deletionMember = Member::findOrFail($id);
				foreach ($deletionMember->postings as $posting){
					$posting->delete();
				}
				$deleted = $deletionMember->forceDelete();
			}
			else {
				// Soft delete -- read overrides from context
				$context = $request->input('context', []);
				$this->generateDischargePostingRecord($id, $context);
				$deleted = Member::findOrFail($id)->delete();
			}			
		}
		catch (Exception $ex){
			$deletionNotPossible = ['code' => 'EX', 'reason' => $ex->getMessage()];
		}
		
		return response()->json([
			'success' => $deleted,
			'deletionMode' => $removeMode,
			'deletionError' => $deletionNotPossibleError
		]);
		
		return abort(400);		// $request should've been an array of arrays..        
    }
	
	
}
