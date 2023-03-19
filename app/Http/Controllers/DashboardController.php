<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

use App\Member;
use App\Decoration;
use App\MemberDecoration;
use App\Http\Controllers\Controller;

class DashboardController extends Controller
{
    public function index()
    {
        $numDecorations = Decoration::all()->count();
        $numDecorationsAwarded = MemberDecoration::all()->count();
        $numMembersActive = Member::all()->where('is_enrolled', 1)->count();
        $numMembersTotal = Member::withTrashed()->count();
        $numNewMembersThisYear = Member::all()->where('created_at', '>=', date('Y-01-01'))->count();
        $numNewMembersThisMonth = Member::all()->where('created_at', '>=', date('Y-m-01'))->count();
        $latestMember = Member::query()
            ->whereRaw('CAST(regt_num AS UNSIGNED) = (' . Member::query()->selectRaw('MAX(CAST(regt_num AS UNSIGNED))')->toSql() . ')')
            ->first();
        
		return response()->json([
			'member' => [
                'numActive' => $numMembersActive,
                'numNewThisYear' => $numNewMembersThisYear,
                'numNewThisMonth' => $numNewMembersThisMonth,
                'numTotal' => $numMembersTotal,
                'latestRegtNumber' => $latestMember ? $latestMember->regt_num : 'Unknown',
            ],
            'decoration' => [
                'num' => $numDecorations,
                'numAwarded' => $numDecorationsAwarded,
            ],
		]);
    }

    public function activityLog(Request $request)
    {
        $limit = intval($request->query('limit'));
        $offset = intval($request->query('offset'));
        
        // Retrieve and combine
        $decorationApprovalsQuery = DB::table('decoration_approvals')
            ->select(DB::raw("'APPR' as log_type"), 'decoration_approvals.dec_appr_id as log_id', 'decoration_approvals.decision_date as log_date')
            ->addSelect(DB::raw("(CASE WHEN decoration_approvals.is_approved <> 0 THEN 'Approved' ELSE 'Declined' END) as log_outcome"))
            ->addSelect(DB::raw("CONCAT('Dec: ', decorations.shortcode, ', Member: ', decoration_approvals.regt_num, ', Appr: ', users.username) as log_text"))
            ->join('users', 'users.user_id', '=', 'decoration_approvals.user_id')
            ->join('decorations', 'decorations.dec_id', '=', 'decoration_approvals.dec_id')
            ->whereNotNull('decoration_approvals.is_approved');
        $membersQuery = DB::table('members')
            ->select(DB::raw("'MBR' as log_type"), 'members.regt_num as log_id', 'members.created_at as log_date', DB::raw("'Added' as log_outcome"))
            ->addSelect(DB::raw("CONCAT('Forums name ', members.forums_username) as log_text"));

        $query = $membersQuery;
        $query->union($decorationApprovalsQuery);

        $query->orderBy('log_date', 'desc');
        if ($limit > 0){
            $query->take($limit);
        }
        else {
            $query->take(100);      // Should provide a default for take. 
        }
        if ($offset > 0){
            $query->skip($offset);
        }

        return response()->json($query->get());
    }
    


}
