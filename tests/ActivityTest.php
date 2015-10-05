<?php

use Illuminate\Foundation\Testing\WithoutMiddleware;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Foundation\Testing\DatabaseTransactions;

class ActivityTest extends TestCase
{
	
	use DatabaseTransactions;
	use WithoutMiddleware;
	
	protected $model = App\Activity::class;
	
	/**
     *  Test multiple member creation via Resource MemberController
     */
    public function testCreateActivity()
	{
		// $activities = $this->newRecords(3);
		$activity = $this->newRecords(1);
		// foreach ($activities as &$activity){
			$response = $this->call('POST', '/api/activity', ['activity' => $activity]);
			$this->assertEquals(200, $response->status());
				
			$jsonResponse = json_decode($response->content());
			$this->assertNotEmpty($jsonResponse);
			
			$activityId = $jsonResponse->recordId;
			$activity['att_id'] = $activityId;
			
			// var_dump($this->call('GET', "/api/activity/$activityId")->content());			
			
			$this->get("/api/activity/$activityId")
				->seeJson([
					'name' => $activity['name'],
					'type' => $activity['type'],
					'start_date' => $activity['start_date'],
					'end_date' => $activity['end_date'],
					'desc' => $activity['desc']
				]);
		// }
		// return $activities;
		return $activity;
	}
	
	/** 
	 *	Test assigning the roll to an activity. Process is:
	 * - Create the activity
	 * - Add roll -> make lots of empty attendance records
	 * - Check roll integrity
	 */
	public function testAssignRollToActivity()
	{
		$activity = $this->testCreateActivity();
		$activityId = $activity['att_id'];
		
		// Select a person to be the recorder
		$systemUser = App\SystemUser::select('forums_username')->get()->random();
		$this->assertEquals(1, count($systemUser), 'No System Users were found');		// Ensure a system user
		
		// Select a bunch of random regt nums
		$numAttTests = 3;
		$members = App\Member::select('regt_num')->get()->random($numAttTests)->all();
		$members = array_values($members);	// re-order the array; the random() keeps their original indices
		$this->assertEquals($numAttTests, count($members), "Couldn't retrieve at least $numAttTests member records");		// Maybe there weren't enough member records
		
		// Make attendance records
		$attRecords = $this->newRecords($numAttTests, App\Attendance::class);
		foreach ($attRecords as $i => &$att){
			$att['acty_id'] = $activityId;
			// $att['recorded_by'] = $systemUser->forums_username;			// This is done by server side
			$att['regt_num'] = $members[$i]->regt_num;
			$response = $this->call('POST', "/api/activity/$activityId/roll", ['attendance' => [$att]]);
			$this->assertEquals(200, $response->status(), 'POST for att record was not 200 OK');
			
			$jsonResponse = json_decode($response->content());
			
			
			$att['att_id'] = $jsonResponse->recordId[0];			// expected to return an array format;
		}
		
		// Test/debug
		// $tmp = $this->call('GET', "/api/activity/$activityId/roll");
		// var_dump(json_decode($tmp->content()));
		
		// Check to see that the roll's up
		$response = $this->call('GET', "/api/activity/$activityId/roll");
		$this->assertEquals(200, $response->status(), 'GET for roll was not 200 OK');
		
		$roll = json_decode($response->content(), true);
		$this->assertEquals(count($attRecords), count($roll), 'Unexpected (different) number of att records');
		
		$this->assertTrue(is_array($roll));
		$overallFound = true;
		foreach ($attRecords as $localAtt){
			$localAttFound = false;
			foreach ($roll as $serverReturnedAtt){
				if ($serverReturnedAtt['att_id'] == $localAtt['att_id']){
					$localAttFound = true;
					break;
				}
			}
			$overallFound = $overallFound && $localAttFound;
		}
		$this->assertTrue($overallFound, 'Could not find the submitted att records when re-retrieved as the roll');
		
		// $this->get("/api/activity/$activityId/roll")->seeJson(['regt_num' => $att['regt_num']]);
		return $attRecords;
	}
 
	/** 
	 * @group rollmarking
	 *	Test marking the roll
	 * - Create the activity
	 * - Add roll -> make lots of empty attendance records
	 * - Mark members off the roll -> update attendance record
	 * - Check roll integrity
	 */
	public function testMarkRollForActivity()
	{
		$attRecords = $this->testAssignRollToActivity();
		$this->assertGreaterThan(0, count($attRecords));
		
		foreach ($attRecords as &$att){
			$attId = $att['att_id'];
			$activityId = $att['acty_id'];
			$att['recorded_value'] = $this->randomAttendanceValue();
			$response = $this->call('PATCH', "/api/activity/$activityId/roll/$attId", ['attendance' => [
				'recorded_value' => $att['recorded_value']
			]]);
			echo '==========='.PHP_EOL;
			var_dump("/api/activity/$activityId/roll/$attId");
			var_dump(json_decode($response->status()));
			var_dump(json_decode($response->content()));
			// Check to see roll was marked
			$test = $this->call('GET', "/api/activity/$activityId/roll/$attId");
			echo '==========='.PHP_EOL;
			var_dump(json_decode($test->content()));
			
			$roll = $this->get("/api/activity/$activityId/roll/$attId")
				->seeJson($att);
		}
		
		return $attRecords;
	}
	
	/** 
	 *	Test updating an attendance record
	 * - Create the activity
	 * - Add roll -> add 1 att record
	 * - Mark roll -> mark as AWOL
	 * - Update record to Leave - check for correct "prev_att_id" behaviour
	 * - Update record to Sick - check for correct "prev_att_id" behaviour
	 * Note: GET on the att record should show the current + full att record history, whilst
	 * INDEX on att records should _only_ show the current record
	 */
	public function testUpdateAttendance()
	{
		$attRecords = $this->testMarkRollForActivity();
		$this->assertGreaterThan(0, count($attRecords));
		
		foreach ($attRecords as &$att){
			$attId = $att['att_id'];
			$activityId = $att['acty_id'];
			$oldAttValue = $att['recorded_value'];
			$newAttValue = $this->randomAttendanceValue();
			while ($newAttValue == $oldAttValue){
				$newAttValue = $this->randomAttendanceValue();
			}
			$att['recorded_value'] = $newAttValue;
			$response = $this->call('PATCH', "/api/activity/$activityId/roll/$attId", ['attendance' => [
				'recorded_value' => $newAttValue
			]]);
			// Check to see roll was marked
			$roll = $this->get("/api/activity/$activityId/roll/$attId")
				->seeJson([
					'record_count' => 2		// There should be exactly 2 records
				])
				->seeJson([						// This should be the newer record
					'acty_id' => $att['acty_id'],						// acty_id and
					'regt_num' => $att['regt_num'],			// regt_num should have copied over
					'recorded_value' => $newAttValue,
					'prev_att_id' => $attId						// should be the former att_id
				]);
		}
		
	}
	
	/** 
	 *	Test AWOL listing
	 * - As per Test Marking the roll
	 * - Check AWOLs appear in the AWOL listing as expected
	 */
	public function testAwolListing()
	{
		$attRecords = $this->testAssignRollToActivity();
		$this->assertGreaterThan(count($attRecords), 0);
		$activityId = $attRecords[0]['acty_id'];
		
		// Expect to see none in the AWOL list
		foreach ($attRecords as $att){
			$attId = $att['att_id'];			
			$response = $this->call('PATCH', "/api/activity/$activityId/roll/$attId", ['attendance' => [
				'recorded_value' => '/'
			]]);
			$jsonResponse = json_decode($response->content());
			$newAttId = $jsonResponse->recordId;
			$this->get("/api/activity/$activityId/awol")
				->dontSee($attId)				// Don't expect the old att record
				->dontSee($newAttId);		// Don't expect this new att record either
		}
		
		
		// Expect to see all in the AWOL list
		foreach ($attRecords as $att){
			$attId = $att['att_id'];			
			$response = $this->call('PATCH', "/api/activity/$activityId/roll/$attId", ['attendance' => [
				'recorded_value' => 'A'
			]]);
			$jsonResponse = json_decode($response->content());
			$newAttId = $jsonResponse->recordId;
			$this->get("/api/activity/$activityId/awol")
				->dontSee($attId)				// Don't expect the old att record
				->seeJson([
					'att_id' => $newAttId,						// Expect this new att record, which is an AWOL.
					'acty_id' => $att['acty_id'],					// acty_id and 
					'regt_num' => $att['regt_num'],			// regt_num should have copied over
				]);		
		}
		
	}
	
	
	/** 
	 *	Test activity deletion 1
	 * - Create the activity
	 * - Delete it -> expect to pass
	 */
	public function testActivityDeletionWithoutRoll()
	{
		$activity = $this->testCreateActivity();
		$this->assertArrayHasKey('att_id', $activity);
		$activityId = $activity['att_id'];
		
		$this->delete("/api/activity/$activityId");
		$response = $this->call('GET', "/api/activity/$activityId");
		$this->assertEquals(404, $response->status());
	}
	
	/** 
	 *	Test activity deletion 2
	 * - Create the activity
	 * - Assign some roll records
	 * - Delete it -> expect to fail
	 */
	public function testActivityDeletionWithRoll()
	{
		$attRecords = $this->testAssignRollToActivity();
		$this->assertGreaterThan(count($attRecords), 0);
		
		$activityId = $attRecords[0]['acty_id'];
		
		$this->call('DELETE', "/api/activity/$activityId");
		$this->assertEquals(403, $response->status());
		
		$this->get("/api/activity/$activityId")
			->seeJson([
				'acty_id' => $activityId
			]);
	}
	
	/** 
	 *	Test attendance deletion 1
	 * - Create the activity
	 * - Assign an attendance record
	 * - Delete it -> expect to pass
	 */
	// public function testAttendanceDeletionWhenBlank(){
		// DESCOPED
	// }
	
	/** 
	 *	Test attendance deletion 2
	 * - Create the activity
	 * - Assign an attendance record
	 * - Mark that attendance record
	 * - Delete it -> expect to fail
	 */
	// public function testAttendanceDeletionWhenFilled(){
		// DESCOPED
	// }
	
	
	// ============================
	// Private
	
	private function randomAttendanceValue()
	{
		$values = ['L', '/', 'S', 'A', 'F'];
		return $values[rand(0, 4)];
	}
	
}