<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Activity;
use App\Http\Requests;
use App\Http\Controllers\Controller;

class ActivityController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return Response
     */
    public function index()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  Request  $request
     * @return Response
     */
    public function store(Request $request)
    {
		$recordId = 0;
		$error = [];
		
		if ($request->has('activity')){
			$postDataActivity = $request->input('activity', []);
			try {
				$activity = Activity::create($postDataActivity);
				$recordId = $activity->acty_id;
			} 
			catch (Exception $ex){
				$error = ['code' => 'EX', 'reason' => $ex->getMessage()];
			}
		} 
		else {
			$error = ['code' => 'EMPTY', 'reason' => 'Post data not provided in required format'];
		}
		
		return response()->json([
			'recordId' => $recordId,
			'error' => $error
		]);
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return Response
     */
    public function show(Request $request, $id)
    {
		// detail -- [ high | med | low ]
		$detailLevel = $request->input('detail', 'low');
		if ($detailLevel == 'high'){	
			$activity = Activity::with('attendances')->firstOrFail($id);
		}
		else {
			$activity = Activity::findOrFail($id);
			return response()->json($activity->toArray());
		}
		
		return abort(400);		// Probably a bad request		
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
}
