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
    public function index(Request $request)
	{
		
		// discharged -- [ none | include | only ]
		$withDischarged = $request->input('discharged', 'none');
		if ($withDischarged == 'include'){
			$query = Member::withTrashed();
		}
		elseif ($withDischarged == 'only'){
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
    public function store(Request $request)
	{
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
		
		if ($request->has('member')){
			// Deal with the member data
			$postDataMember = $request->input('member');
			
			DB::beginTransaction();
			try {
				// Get their regimental number
				$newRegtNum = $this->generateStandardRegtNumber($context) . ($postDataMember['sex'] == 'F' ? 'F' : '');	
				
				if (!empty($newRegtNum)){
					
					// Assign regt num, create new record, and generate initial posting
					$postDataMember['regt_num'] = $newRegtNum;
					$newMember = Member::create($postDataMember);
					
					if ($newMember->regt_num > 0){
						$recordId = $newMember->regt_num;
						$initialPostingId = $this->generateInitialPostingRecord($recordId, $context);	
					}
					else {
						$idNotPossibleError = [
							'code' => 'CANNOT_SAVE_REGT_NUM', 
							'valueExpected' => $newRegtNum, 
							'valueActual' => $newMember->regt_num, 
							'reason' => 'Looks like the database rejected this regt num'];
					}
					
				}
				else {
					$idNotPossibleError = ['code' => 'NO_REGT_NUM', 'reason' => 'Could not generate a regt num'];
				}
				
			}
			catch (Exception $ex){
				DB::rollBack();
				$idNotPossibleError = ['code' => 'EX', 'reason' => $ex->getMessage()];
			}	
			DB::commit();
	
			return response()->json([
				'recordId' => $recordId,
				'initialPostingId' => $initialPostingId,
				'error' => $idNotPossibleError
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
    public function show(Request $request, $id)
	{
		// Fetch the first match
		// detail -- [ high | med | low ]
		$detailLevel = $request->input('detail', 'low');
		
		// Todo: Add $detailLevel == med
		
		if ($detailLevel == 'high'){	
			// Todo: to add more "eager loaded" relationshions, add extra args to Member::with()
			$member = Member::with('postings')->where('regt_num', $id)->withTrashed()->firstOrFail();
			return response()->json($member->toArray());
		}
		else {
			//  when $detailLevel == low
			$member = Member::where('regt_num', $id)->withTrashed()->firstOrFail();
			return response()->json($member->toArray()); 
		}

		return abort(400);		// Probably a bad request
    }


    /**
     * Update the specified resource in storage.
     *
     * @param  Request  $request
     * @param  int  $id
     * @return Response
     */
    public function update(Request $request, $id)
	{
        $postData = $request->all();
		$updated = 0;
		$updateNotPossibleError = [];
		
		if (is_array($postData) && array_key_exists('member', $postData)){
			try {
				$updateMember = $postData['member'];
				$updated = Member::updateOrCreate(['regt_num' => $id], $updateMember);
			}
			catch (Exception $ex){
				$updateNotPossibleError = ['code' => 'EX', 'reason' => $ex->getMessage()];
			}
			
			return response()->json([
				'recordId' => $updated,
				'error' => $updateNotPossibleError
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
    public function destroy(Request $request, $id)
	{
		// remove -- [ discharge | permanent ]
		$removeMode = $request->input('remove', 'discharge');
		$updated = 0;
		$deletionNotPossibleError = [];
		
		try {
			if ($removeMode == 'permanent'){
				$deletionMember = Member::where('regt_num', $id)->withTrashed()->firstOrFail();
				
				// Todo: future permissions check
				$permissionsCheck = true;
				if ($permissionsCheck || $deletionMember->is_active === 0){		// Allow anybody to delete inactive records
					foreach ($deletionMember->postings as $posting){
						$posting->delete();
					}
					$deleted = $deletionMember->forceDelete();
				}
				else {
					$deletionNotPossibleError = ['code' => 'PERM', 'reason' => 'You don\'t have permission to permanently delete this record'];
				}
			}
			else {
				// Soft delete -- read overrides from context
				$deleted = Member::findOrFail($id)->delete();
			}
		}
		catch (Exception $ex){
			$deletionNotPossibleError = ['code' => 'EX', 'reason' => $ex->getMessage()];
		}
		
		return response()->json([
			'success' => $deleted,
			'deletionMode' => $removeMode,
			'deletionError' => $deletionNotPossibleError
		]);
		
		return abort(400);		// $request should've been an array of arrays..        
    }
	
	

	
}
