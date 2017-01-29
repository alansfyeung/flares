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

    /** 
     * Return only a specific dashboard stat, by categoryName
     *
     * @return Response
     */
    public function show($categoryName)
    {
        $blender = [];
		switch ($categoryName){
			case 'member':
			case 'members':
				$blender['member'] = $this->numMembers();
				return response()->json($blender);
            case '':
			case 'members':
				$blender['member'] = $this->numMembers();
				return response()->json($blender);
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
		$numActive = Member::all()->where('is_active', 1)->count();
		$numInactive = Member::all()->where('is_active', 0)->count();
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
