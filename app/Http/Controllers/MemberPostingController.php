<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use DB;
use App\Member;
use App\Promotion;
use App\Http\Requests;
use App\Http\Controllers\Controller;

class MemberPostingController extends BaseMemberController
{
    public function index($memberId)
    {
		$memberPostingRecords = Member::where('regt_num', $memberId)->withTrashed()->get();
        return response()->json($memberPostingRecords->postings->toArray());
    }

    public function store(Request $request, $memberId)
    {
		$postingRecordId = 0;
		$postingNotPossibleError = [];
		
		$context = $request->input('context', []);
		// $member = $request->input('member', []);
		// if ($request->has('member') && array_key_exists('regt_num', $member)){
			// $postingRecordId = $this->generateDischargePostingRecord($member['regt_num'], $context);
		// }
		if ($memberId){
			$postingRecordId = $this->generateDischargePostingRecord($memberId, $context);
		}
		else {
			$postingNotPossibleError = ['code' => 'NO_REGT_NUM', 'reason' => 'Did not provide a member ID'];;
		}
		
		return response()->json([
			'success' => $postingRecordId,
			'regtNumError' => $postingNotPossibleError
		]);
    }

    public function show($memberId, $postingId)
    {
		/* 
		 * This method is totally redundant (as it's a lookup by postingpromo id) 
		 */
        $postingRecord = Promtion::findOrFail($postingId);
        return response()->json($postingRecord->toArray());
    }
	
}
