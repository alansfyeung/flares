<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use DB;
use App\Member;
use App\Promotion;
use App\Http\Requests;
use App\Http\Controllers\Controller;
use App\Http\Custom\ResponseCodes;

class MemberPostingController extends Controller
{
    use ProcessesMemberRecordsTrait;
    
    public function index($memberId)
    {
		$memberPostingRecords = Member::where('regt_num', $memberId)->withTrashed()->get();
        return response()->json($memberPostingRecords->postings->toArray());
    }

    public function store(Request $request, $memberId)
    {
		$postingRecordId = 0;
		
		try {
			$context = $request->input('context', []);
			if ($memberId){
				$postingRecordId = $this->generateDischargePostingRecord($memberId, $context);
			}
			else {
				$postingNotPossibleError = ['code' => 'NO_REGT_NUM', 'reason' => 'Did not provide a member ID'];;
			}
			
			return response()->json([
				'recordId' => $postingRecordId
			]);
		}
		catch (\Exception $ex){
			return response()->json([
				'error' => ['code' => $ex->getCode(), 'reason' => $ex->getMessage()]
			], 500);
		}
    }

    public function show($memberId, $postingId)
    {
		/* 
		 * This method is totally redundant (as it's a lookup by postingpromo id) 
		 */
        $postingRecord = Promtion::findOrFail($postingId);
        return response()->json([
			'posting_promo' => $postingRecord
		]);
    }
	
}
