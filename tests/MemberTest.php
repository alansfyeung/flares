<?php

use Illuminate\Foundation\Testing\WithoutMiddleware;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Foundation\Testing\DatabaseTransactions;

use App\Member;

class MemberTest extends TestCase
{
	
	use DatabaseTransactions;
	use WithoutMiddleware;
	
    /**
     *  Test multiple member creation via Resource MemberController
     */
    public function testCreateMultipleMembers(){
		
		$members = $this->newMemberRecords();
		foreach ($members as $member){
			$payload = [
				'content' => [],
				'member' => $member
			];
			$response = $this->call('POST', '/api/member', $payload);
			$this->assertEquals(200, $response->status());
			
			$jsonResponse = json_decode($response->content());
			$this->assertNotEmpty($jsonResponse);
			
			$memberId = $jsonResponse->recordId;
			$this->assertRegExp('/^206\d{4,5}F?$/', $memberId);
			
			$this->get("/api/member/$memberId")
				->seeJson([
					'regt_num' => $memberId,
					'first_name' => $member['first_name'],
					'last_name' => $member['last_name'],
					'dob' => $member['dob']
				]);
		}

	}
	
   /**
     *  Test member creation with context overrides (e.g. Year/intake, etc)
     */
	public function testUpdateMember(){
		
		// Fetch a list of dummy members
		$members = $this->newMemberRecords();
		$member = $members[array_rand($members)];
		
		// Create member 
		$payload = [
			'content' => [],
			'member' => $member
		];
		$response = $this->call('POST', '/api/member', $payload);
		$this->assertEquals(200, $response->status());

		$jsonResponse = json_decode($response->content());
		$this->assertNotEmpty($jsonResponse);
		
		$memberId = $jsonResponse->recordId;
		
		// Apply some updates
		$member['first_name'] = 'UpdatedName';
		$member['last_name'] = 'Smith';
		$member['dob'] = '2015-01-01';
		
		$payload = [
			'content' => [],
			'member' => $member
		];
		
		$resp = $this->call('PUT', "/api/member/$memberId", $payload);
		$this->assertEquals(200, $resp->status());
		
		$this->get("/api/member/$memberId")
			->seeJson([
				'regt_num' => $memberId,
				'first_name' => $member['first_name'],
				'last_name' => $member['last_name'],
				'dob' => $member['dob']
			]);

	}
	
	
	/**
     *  Test updating a single member
     */
	public function testUpdateMemberWithContextOverrides(){
		
		// Fetch a list of dummy members
		$members = $this->newMemberRecords();
		$member = $members[array_rand($members)];
		
		// Create member 
		$overrideSettings = [
			'name' => 'newAdultCadetStaff',
			'hasOverrides' => true
		];
		$overrides = [
			'thisYear' => '2014',
			'thisCycle' => '2',
			'newPosting' => 'ACS',
			'newPlatoon' => '3PL',
			'newRank' => 'LT (AAC)'
		];
		$payload = [
			'context' => array_merge($overrideSettings, $overrides),
			'member' => $member
		];
		$response = $this->call('POST', '/api/member', $payload);
		
		$this->assertEquals(200, $response->status());
		
		$jsonResponse = json_decode($response->content());
		$this->assertNotEmpty($jsonResponse);
		
		$memberId = $jsonResponse->recordId;
		
		// Apply some updates
		$member['first_name'] = 'UpdatedName';
		$member['last_name'] = 'Smith';
		$member['dob'] = '2015-01-01';
		
		$payload = [
			'content' => [],
			'member' => $member
		];
		
		$resp = $this->call('PUT', "/api/member/$memberId", $payload);
		$this->assertEquals(200, $resp->status());

		$this->get("/api/member/$memberId?detail=full")
			->seeJson([
				'new_rank' => $overrides['newRank'],
				'new_platoon' => $overrides['newPlatoon'],
				'new_posting' => $overrides['newPosting']
			]);

	}
	
	
	/**
     *  Test soft deletions
     */
	public function testDeleteMember(){
		// WIP
	}
	
	
	
	//========================
	// Privates
 
	private function newMemberRecords($howMany = 3){
		// $members = [];
		
		// $members[] = [
			// 'last_name' => 'Jack',
			// 'first_name' => 'Afro',
			// 'dob' => '1998-05-10',
			// 'sex' => 'M',
			// 'school' => 'Penrith Performing Arts High School',
			// 'member_email' => 'afro@jack.com',
			// 'parent_email' => 'papajack@gmail.com'
		// ];
		// $members[] = [
			// 'last_name' => 'Romman',
			// 'first_name' => 'Jessica',
			// 'dob' => '2002-03-11',
			// 'sex' => 'F',
			// 'school' => 'Hurlstone Agricultural High',
			// 'member_email' => 'jessromman@gmail.com',
			// 'parent_email' => 'kyleromman@work.com.au'
		// ];
		
		$members = factory(App\Member::class, 3)->make()->toArray();
		return $members;
	}
	
	private function existingMemberRecords(){
		$members = $this->newMemberRecords();
		$regtNumPrefix = '20681';
		$regtNumCounter = 10;
		foreach ($members as &$member){
			$member['regt_num'] = $regtNumPrefix . $regtNumCounter++ . ($member['sex'] == 'F' ? 'F' : '');
		}
		return $members;
	}
	
}
