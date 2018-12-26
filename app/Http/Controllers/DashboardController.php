<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Member;
use App\Decoration;
use App\Http\Requests;
use App\Http\Controllers\Controller;
use App\Http\Custom\ResponseCodes;


class DashboardController extends Controller
{
    public function index()
    {
        // Return all statistics
		return response()->json([
			'member' => $this->numMembers(),
            'decoration' => $this->numDecorations(),
		]);
    }

    public function activity()
    {
        // To be continued
        return response()->json([]);
    }

    /** 
     * Return only a specific dashboard stat, by name
     *
     * @return Response
     */
    public function show($categoryName)
    {
        $stats = [];
		switch ($categoryName){
			case 'member':
			case 'members':
				$stats['member'] = $this->numMembers();
				return response()->json($stats);
		}
    }
    
    private function numDecorations()
    {
        return [
            'num' => Decoration::all()->count(),
        ];
    }
	
	private function numMembers()
    {
		$numActive = Member::all()->where('is_enrolled', 1)->count();
		$numInactive = Member::all()->where('is_enrolled', 0)->count();
		$numDischarged = Member::onlyTrashed()->count();
		$numTotal = Member::withTrashed()->count();
		return [
			'numActive' => $numActive,
			'numInactive' => $numInactive,
			'numDischarged' => $numDischarged,
			'numTotal' => $numTotal
		];
	}
	

}
