<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use DB;
use App\Member;
use App\Decoration;
use App\MemberDecoration;
use App\Http\Requests;
use App\Http\Controllers\Controller;
use App\Http\Custom\ResponseCodes;

class MemberDecorationController extends Controller
{   
    public function index($memberId)
    {
		$member = Member::with('decorations.decoration')->where('regt_num', $memberId)->firstOrFail();
        return response()->json([
            'decorations' => $member->decorations->toArray()
        ]);
    }

    public function show($memberId, $awardId)
    {
        $award = MemberDecoration::findOrFail($awardId);
        return response()->json([
            'memberDecoration' => $award,
			'member' => $award->member,
            'decoration' => $award->award
		]);
    }
    
    public function store(Request $request, $memberId)
    {
		try {
            // Get the input payload
            $data = $request->input('memberDecoration');
            
            // Create MemberDecoration object
            $award = new MemberDecoration();
            $award->regt_num = $memberId;
            $award->dec_id = $data['dec_id'];
            $award->citation = $data['citation'];
            $award->date = $data['date'];
            
            // Save it
            $award->save();
                
            // Return ID
			return response()->json([
				'id' => $award->awd_id
			], 201);
		} catch (\Exception $ex) {
			return response()->json([
				'error' => ['code' => $ex->getCode(), 'reason' => $ex->getMessage()]
			], 500);
		}
    }

    public function destroy($memberId, $awardId)
    {
        try {
            $award = MemberDecoration::findOrFail($awardId);
            $award->delete();
            return response('', 204);
        } catch (\Exception $ex) {
            return response()->json([
				'error' => ['code' => $ex->getCode(), 'reason' => $ex->getMessage()]
			], 500);
        }
    }
	
}
