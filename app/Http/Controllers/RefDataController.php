<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use DB;
use App\Http\Requests;
use App\Http\Controllers\Controller;
use App\Http\Custom\ResponseCodes;

class RefDataController extends Controller
{
	public function all()
    {
		$refData = [];
		$refData['platoons'] = DB::table('ref_platoons')->orderBy('pos', 'asc')->get();
		$refData['ranks'] = DB::table('ref_ranks')->orderBy('pos', 'asc')->get();
		$refData['postings'] = DB::table('ref_postings')->orderBy('pos', 'asc')->get();
                
        
        // Manual 
        // TODO: chuck into database
		$refData['sexes'] = ['M', 'F'];
        
        /// Types are: newRecruitment, newTransfer, newVolunteerStaff, newAdultCadetStaff
        $refData['onboardingTypes'] = [		
			['id' => 'newRecruitment', 'name' => 'New Recruitment'],
			['id' => 'newTransfer', 'name' => 'New Transfer'],
			['id' => 'newVolunteerStaff', 'name' => 'Volunteer Staff member'],
			['id' => 'newAdultCadetStaff', 'name' => 'Adult Staff member'],
		];
		$refData['intakes'] = [
			['id' => '1', 'name' => '1st Trg Cycle'],
			['id' => '2', 'name' => '2nd Trg Cycle'],
		];
        
		return response()->json($refData);
	}
    
    public function misc(Request $request)
    {
		$query = DB::table('ref_misc');
		if ($request->has('name')) {
			$query->where('name', $request->input('name'));
		}
		return response()->json([
			'misc' => $query->get()
		]);
	}
    
    public function get($key)
    {
        if (method_exists($this, $key)) {
            return $this->$key();
        }
        return response('', 404);
    }
    
    private function decorationTiers()
    {
        $tierData = DB::table('ref_misc')->where('name', 'like', 'DECORATIONS_TIER_%')->get();
        $tierListIndex = $tierData->search(function($item, $key) {
            return $item->name == 'DECORATIONS_TIER_LIST';
        });
        if ($tierListIndex !== false){
            $tierListValues = explode(',', $tierData[$tierListIndex]->value);
            $tierListKeyNames = array_map(function($keyValue) {
                return "DECORATIONS_TIER_$keyValue";
            }, $tierListValues);
            
            $tierListReference = array_combine($tierListKeyNames, $tierListValues);
            
            $tiers = [];
            foreach ($tierData as $tierDataItem){
                if (in_array($tierDataItem->name, array_keys($tierListReference))){
                    $tiers[] = [
                        'tier' => $tierListReference[$tierDataItem->name],
                        'tierName' => $tierDataItem->value
                    ];
                }
            }
            return response()->json($tiers);
        }
        return response('', 404);
    }
	
    private function platoons()
    {
		return response()->json(DB::table('ref_platoons')->orderBy('pos', 'asc')->get());
    }
	
	private function ranks()
    {
		return response()->json(DB::table('ref_ranks')->orderBy('pos', 'asc')->get());
	}
	
	private function postings()
    {
		return response()->json(DB::table('ref_postings')->orderBy('pos', 'asc')->get());
	}
    
    private function activity()
    {
        $types = ['Unit Parade', 'Bivouac', 'Induction', 'Ceremonial', 'AFX', 'Course', 'Activity'];
        $presets = ['Tuesday Night'];
        $year = date('Y');
        for ($i = 1; $i <= 4; $i++){
            $presets[] = "0$i-$year";
        }
        
        return response()->json([
            'types' => $types,
            'presets' => $presets
        ]);
    }

}
