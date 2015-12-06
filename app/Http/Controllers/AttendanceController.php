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
	const ERR_POSTDATA_MISSING = 4001;
	const ERR_DB_PERSIST = 5001;
	
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
        
        $atts->each(function($item){
            // bind extra info
            $isBlank = ($item->recorded_value === '0' || $item->recorded_value === 0);
            $item->is_blank = $isBlank;
        });
		
		return response()->json([
            'count' => $atts->count(),
            'roll' => $atts->toArray()
        ]);
    }
	
	/**
     * Show the AWOLs for activity
     *
     * @param  int  $activityId
     * @return Response
     */
    public function awol($activityId)
    {
        // Select all attendance records which are 
		$activity = Activity::with(['attendances' => function($query){
			$query->where('recorded_value', 'A')->orderBy('created_at', 'desc');
		}])->where('acty_id', $activityId)->firstOrFail();
		
		$atts = $activity->attendances;
		$atts->reject(function($item){
			// If this att is the ultimate att, then keep it
			return $item->att_id != $this->chainLookupUltimate($item)->att_id;
		});
		
		return response()->json([
			'count' => $atts->count(),
			'awol' => $atts->toArray()
		]);
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
		
        // Check for multiple attendance objects in the payload
		// Input payload must contain an array, even for POSTing single objects
		if ($request->has('attendance') && is_array($request->input('attendance'))){	
			DB::beginTransaction();
			try {
				$activity = Activity::findOrFail($activityId);
				foreach ($request->input('attendance') as $postDataAtt){
					$att = $activity->attendances()->save(Attendance::create($postDataAtt));
					// $att->recorded_by = $recordedBy;  		// recorded_by is not needed for empty att recs
					// $att->save();
					$recordIds[] = $att->att_id;
				}
				DB::commit();
                return response()->json([
                    'recordId' => $recordIds
                ]);
			}
			catch (\Exception $ex){
				$error = ['code' => $ex->getCode(), 'reason' => $ex->getMessage()];
				$recordIds = [];
				DB::rollBack();
			}
		}
		else {
			$error = ['code' => 'NO_POSTDATA', 'reason' => 'The post data was provided in the wrong format'];
		}
		
        return response()->json([
            'error' => $error
        ], 400);
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return Response
     */
    public function show($activityId, $attId)
    {
        // Locate the specific attendance record
		$att = Attendance::findOrFail($attId);
		
		// Do a backwards lookup 
		$precedingAtts = [];
		$this->chainLookupBackwards($att, $precedingAtts);
		
		// Do a forwards lookup
		$supersedingAtts = [];
		$this->chainLookupForwards($att, $supersedingAtts);
		
		// Get the very last attendance record
		$ultimate = count($supersedingAtts) > 0 ? end($supersedingAtts) : $att;
		
		// How many in the chain
		$chainCount = count($precedingAtts) + 1 + count($supersedingAtts);
		
		return response()->json([
			'self' => $att,
			'ultimate' => $ultimate,
			'preceding' => $precedingAtts,
			'superseding' => $supersedingAtts,
			'count' => $chainCount
		]);
		
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  Request  $request
     * @param  int  $id
     * @return Response
     */
    public function update(Request $request, $activityId, $attId)
    {
		$oldId = 0;
		$newId = 0;

		// ?action = [ late | sms | leave | update ]
		$action = $request->query('action', 'update');
		
		// TODO: Set the correct "Recorded By" as a system user
		$recordedBy =  'YeungA';  	// This value should be set by the SESSION
		$postData = $request->input('attendance', []);
		$comments = $request->input('comments', '');
		
		try {
			$activity = Activity::findOrFail($activityId);
			$requestedAtt = Attendance::findOrFail($attId);
			
			if ($activity->acty_id != $requestedAtt->acty_id){
				throw new Exception("Requested att record $attId does not belong to activity $activityId", 'ERR_MISMATCH');
			}
			
			if ($action == 'update'){
				if (array_key_exists('recorded_value', $postData)){
					$recordedValue = $postData['recorded_value'];		// value to be recorded
					$ultimateAtt = $this->chainLookupUltimate($requestedAtt);
					$oldId = $ultimateAtt->att_id;
					if ($ultimateAtt->recorded_value == '0'){
						// Case 1: If the attendance record is "blank" i.e. recorded_value = '0'
						// Update the existing record
						$ultimateAtt->comments = $comments;
						$ultimateAtt->recorded_value = $recordedValue;
						$ultimateAtt->recorded_by = $recordedBy;
						$ultimateAtt->save();
						$newId = $ultimateAtt->att_id;
					}
					else {
						// Case 2: The attendance record was already filled. 
						// Make a superseding one
						$newAtt = new Attendance();
						$newAtt->regt_num = $ultimateAtt->regt_num;
						$newAtt->prev_att_id = $ultimateAtt->att_id;
						$newAtt->recorded_value = $recordedValue;
						$newAtt->recorded_by = $recordedBy;
						$savedAtt = $activity->attendances()->save($newAtt);	
						
						$newId = $savedAtt->att_id;
					}									
				}
				else {
					// Payload didn't specify an attendance value. return an error
					throw new \Exception('No attendance value in payload', self::ERR_POSTDATA_MISSING);
				}
			}
			else {
				$oldId = $requestedAtt->att_id;
				if ($action == 'late'){
					// Case 3: If the update mode is "lateness"
					// Update the existing record
					if (array_key_exists('is_late', $postData)){
						$requestedAtt->is_late = $postData['is_late'] == '1' ? 1 : 0;
						$requestedAtt->save();
						$newId = $requestedAtt->att_id;
					}
					else {
						throw new \Exception('Expected is_late value in post data', self::ERR_POSTDATA_MISSING);
					}
				}
				elseif ($action == 'leave'){
					// Case 4: If the update mode is "linking a leave record"
					// Update the existing record
					// $leaveId = $request->input('leave', 0);
					// if ($leaveId){
						// $requestedAtt->leave_id = ;
						// $requestedAtt->save();
						// $newId = $requestedAtt->att_id;						
					// }
					// else {
						// $error = ['code' => 'EX', 'reason' => $ex->getMessage()];
					// }
				}
				elseif ($action == 'sms'){
					// Case 5: Updating the SMS sent status
					// Update the existing record
					foreach (['is_sms_sent' => 0, 'sms_timestamp' => date('Y-m-d'), 'sms_mobile' => 'unknown', 'sms_failure' => 'N/A' ] as $smsKey => $defaultValue){
						if (array_key_exists($smsKey, $postData)){
							$ultimateAtt->$smsKey = $postData[$smsKey];
							// $ultimateAtt->is_sms_sent = $request->input('smsSent', 0);
							// $ultimateAtt->sms_timestamp = $request->input('smsTimestamp', date('Y-m-d'));
							// $ultimateAtt->sms_mobile = $request->input('smsMobile', 'unknown');
							// $ultimateAtt->sms_failure = $request->input('smsFailure', 'N/A');						
						}
						else {
							$ultimateAtt->$smsKey = $defaultValue;
						}
					}
					$ultimateAtt->save();
					$newId = $ultimateAtt->att_id;
				}
					
			}
            
            return response()->json([
                'attId' => $attId,
                'requested' => $requestedAtt,
                'oldRecordId' => $oldId,
                'recordId' => $newId
            ]);
		}
		catch (\Exception $ex){
            return response()->json([
                'error' => ['code' => $ex->getCode(), 'reason' => $ex->getMessage()]
            ], 400);
		}
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return Response
     */
    public function destroy($activityId, $attId)
    {
		$deleted = false;
		
        $activity = Activity::findOrFail($activityId);
		$att = $activity->attendances()->where('att_id', $attId)->firstOrFail();
        
        // Only delete if roll value is NOT marked
		if ($att->recorded_value == '0'){
			$deleted = $att->delete();
            return response()->json([
                'success' => $deleted
            ]);
		}
		else {
			// Don't delete
            return response()->json([
                'error' => ['code' => self::ERR_DB_PERSIST, 'reason' => 'No-op: value was already recorded for attendance', 'recorded' => $att->recorded_value]
            ]);
		}
		
    }
	
	
	// ===========
	// private functions
	
	/**
     * Recursively trace back prev_att_id records
     *
     * @param  Attendance  $att
     * @param  array  $foundAtts
     */
	private function chainLookupBackwards($att, array &$foundAtts)
	{
		// var_dump($att);
		// var_dump($att->prev_att->get());
		if ($prevAtt = $att->prev_att){
			$foundAtts[] = $prevAtt;
			$this->chainLookupBackwards($prevAtt, $foundAtts);
			return;
		}
	}
	
	/**
     * Recursively trace forward prev_att_id records
     *
     * @param  Attendance  $att
     * @param  array  $foundAtts
     */
	private function chainLookupForwards($att, array &$foundAtts)
	{
		// if ($futureAtt = Attendance::where('prev_att_id', $att->att_id)->first()){
		if ($futureAtt = $att->future_att){
			$foundAtts[] = $futureAtt;
			$this->chainLookupForwards($futureAtt, $foundAtts);
			return;
		}
	}
	
	/**
     * Recursively trace forward and return the ultimate att record in this sequence
     *
     * @param  Attendance $att
     * @return $att
     */
	private function chainLookupUltimate($att)
	{
		// if ($futureAtt = Attendance::where('prev_att_id', $att->att_id)->first()){
		if ($futureAtt = $att->future_att){
			return $this->chainLookupUltimate($futureAtt);
		}
		return $att;
	}
	
}
