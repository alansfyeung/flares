<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Member;
use App\Http\Requests;
use App\Http\Controllers\Controller;
use App\Http\Custom\ResponseCodes;


class DashboardController extends Controller
{
    public function index()
    {
        // Return all statistics
		return response()->json([
			'member' => $this->numMembers()
		]);
    }

    public function show($category)
    {
        $blender = [];
		switch ($category){
			case 'member':
			case 'members':
				$blender['member'] = $this->numMembers();
				return response()->json($blender);
		}
    }
	
	private function numMembers(){
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
