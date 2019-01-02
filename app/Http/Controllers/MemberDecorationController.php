<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use Auth;
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

            // Capture the current admin user as the awarder 
            $award->user_id = Auth::id();
            
            // Save it
            $award->save();
                
            // Return ID
			return response()->json([
				'id' => $award->awd_id
			], 201);
		} catch (\Exception $ex) {
            if ($ex->getCode() == '23000') {
                // SQLSTATE[23000]: Integrity constraint violation
                return response()->json([
                    'error' => ['code' => ResponseCodes::ERR_DECORATION_ALREADY_ASSIGNED, 'reason' => 'Decoration was already assigned to the member']
                ], 500);
            } else {
                return response()->json([
                    'error' => ['code' => $ex->getCode(), 'reason' => $ex->getMessage()]
                ], 500);
            }
		}
    }
    
    public function update(Request $request, $memberId, $awardId)
    {
        $data = $request->input('memberDecoration');
        $award = MemberDecoration::findOrFail($awardId);
            
        try {
            // Only certain fields may be updated
            $award->citation = $data['citation'];
            $award->date = $data['date'];
            $award->save();
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
