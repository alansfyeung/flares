<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use DB;
use App\Activity;
use App\Attendance;
use App\Http\Requests;
use App\Http\Controllers\Controller;

class AttendanceController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return Response
     */
    public function index($activityId)
    {
		$activity = Activity::findOrFail($activityId);
		$atts = $activity->attendances;		// assume this returns a Collection
		
		$atts->reject(function($item) use ($atts){
			// Check if this item has successors
			foreach ($atts as $att){
				if ($att->prev_at_id == $item->att_id){
					return true;		// If it has a successor, mark for removal it
				}
			}
		});
		
		return response()->json($atts->toArray());
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  Request  $request
     * @return Response
     */
    public function store(Request $request, $activityId)
    {
		$recordIds = [];
		$error = [];
		
		// TODO: Set the correct "Recorded By" as a system user
		$recordedBy = 'YeungA';
		
        // Check for multiple attendance objects in the payload
		// Input payload must contain an array, even for POSTing single objects
		if ($request->has('attendance') && is_array($request->input('attendance'))){	
			DB::beginTransaction();
			try {
				$activity = Activity::findOrFail($activityId);
				foreach ($request->input('attendance') as $postDataAtt){
					$postDataAtt['recorded_by'] = $recordedBy;
					$att = $activity->attendances()->save(Attendance::create($postDataAtt));
					$recordIds[] = $att->att_id;
				}
				DB::commit();
			}
			catch (Exception $ex){
				$error = ['code' => 'EX', 'reason' => $ex->getMessage()];
				$recordIds = [];
				DB::rollBack();
			}
		}
		else {
			$error = ['code' => 'NO_POSTDATA', 'reason' => 'The post data was provided in the wrong format'];
		}
		
		return response()->json([
			'recordId' => $recordIds,
			'error' => $error
		]);
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return Response
     */
    public function show($id)
    {
        //
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
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return Response
     */
    public function destroy($id)
    {
        //
    }
	
	
	// ===========
	// private functions
	
	// /**
     // * Recursively trace back prev_att_id records and remove them from the collection
     // *
     // * @param  int  $id
     // * @param  int  $id
     // * @return Response
     // */
	// private function findPreviousAttendance(Collection $atts, $id)
	// {
		
	// }
	
	
}
