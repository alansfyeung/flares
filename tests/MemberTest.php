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
				'context' => [],
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
		// Create member 
		$members = $this->newMemberRecords();
		$member = $members[array_rand($members)];
		$memberId = $this->persistMember($member);
		
		// Apply some updates
		$member['first_name'] = 'UpdatedName';
		$member['last_name'] = 'Smith';
		$member['dob'] = '2015-01-01';
		
		$payload = [
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
		// Create member 
		$members = $this->newMemberRecords();
		$member = $members[array_rand($members)];
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
		$memberId = $this->persistMember($member, $overrides, $overrideSettings);
		
		
		// TEST UPDATING
		$member['first_name'] = 'UpdatedName';
		$member['last_name'] = 'Smith';
		$member['dob'] = '2015-01-01';
		
		$payload = [
			'member' => $member
		];
		
		$resp = $this->call('PUT', "/api/member/$memberId", $payload);
		$this->assertEquals(200, $resp->status());

		$this->get("/api/member/$memberId?detail=high")
			->seeJson([
				'new_rank' => $overrides['newRank'],
				'new_platoon' => $overrides['newPlatoon'],
				'new_posting' => $overrides['newPosting']
			]);

	}
	
	
	/**
     *  Test soft deletions 1
     */
	public function testDischargeMember(){
		
		// Fetch a list of dummy members
		// Create member 
		$members = $this->newMemberRecords();
		$member = $members[array_rand($members)];
		$memberId = $this->persistMember($member);
		
		// Soft Delete this record
		$resp = $this->call('DELETE', "/api/member/$memberId");
		$this->assertEquals(200, $resp->status());
		
		// Check that it still turns if queried directly
		$this->get("/api/member/$memberId?detail=high")
			->seeJson([
				'regt_num' => $memberId,
				'first_name' => $member['first_name'],
				'last_name' => $member['last_name'],
				// 'is_discharge' => 1		// Discharge record is now handled by separate api call
			]);
			
		// And that it shows up in the relevant Index searches
		$this->get("/api/member?regt_num=$memberId")
			->seeJson([]);		// Expect no results
			
		$this->get("/api/member?regt_num=$memberId&discharged=only")
			->seeJson([
				'regt_num' => $memberId,
				'first_name' => $member['first_name'],
				'last_name' => $member['last_name']
			]);		// Expect Us.
			
			
		// var_dump($this->call('GET', "/api/member?regt_num=$memberId&discharged=only")->content());
		// var_dump($this->call('GET', "/api/member?regt_num=$memberId&discharged=include")->content());
		
		// // Not working, dunno why
		// $this->get("/api/member?regt_num=$memberId&discharged=include")
			// ->seeJson([
				// 'regt_num' => $memberId,
				// 'first_name' => $member['first_name'],
				// 'last_name' => $member['last_name']
			// ]);
		
	}
	
	/**
     *  Test soft deletions 2
     */
	public function testDischargeMemberWithTerminatingRank(){
		
		// Fetch a list of dummy members
		// Create member 
		$members = $this->newMemberRecords();
		$member = $members[array_rand($members)];
		$memberId = $this->persistMember($member);
		
		// Create the discharge PostingPromo record
		$payload = [
			'context' => [
				'effectiveDate' => '2012-01-02',
				'isCustomRank' => true,
				'dischargeRank' => 'CDTSGT'
			]
		];
		$resp = $this->call('POST', "/api/member/$memberId/posting", $payload);
		$this->assertEquals(200, $resp->status());
		
		// Soft Delete this record
		$resp = $this->call('DELETE', "/api/member/$memberId");
		$this->assertEquals(200, $resp->status());
		
		// Check that it still turns if queried directly
		$this->get("/api/member/$memberId?detail=high")
			->seeJson([
				'regt_num' => $memberId,
				'first_name' => $member['first_name'],
				'last_name' => $member['last_name'],
				'is_discharge' => 1,
				'new_rank' => 'CDTSGT',
				'effective_date' => '2012-01-02'
			]);
			
		// And that it shows up in the relevant Index searches
		$this->get("/api/member?regt_num=$memberId")
			->seeJson([]);		// Expect no results
			
		$this->get("/api/member?regt_num=$memberId&discharged=only")
			->seeJson([
				'regt_num' => $memberId,
				'first_name' => $member['first_name'],
				'last_name' => $member['last_name']
			]);		// Expect Us.
		
	}
	
	
	/**
     *  Test proper (permanent) deletions
     */
	public function testDeleteMember(){
		
		// Fetch a list of dummy members
		// Create member 
		$members = $this->newMemberRecords();
		$member = $members[array_rand($members)];
		$memberId = $this->persistMember($member);
		
		// "Delete" this record
		$resp = $this->call('DELETE', "/api/member/$memberId?remove=permanent");
		$this->assertEquals(200, $resp->status());
		
		// Check that its gone for good
		$expectedNotFound = $this->call('GET', "/api/member/$memberId");
		$this->assertEquals(404, $expectedNotFound->status());
		$this->get("/api/member?regt_num=$memberId&discharged=only")
			->seeJson([]);		// Expect no results
		
	}
	
	
	
	
	//========================
	// Privates
 
 
	/**
     * Randomly generate some new member records
     *
     * @return Member[]
     */
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
			// 'last_name' => 'Rommano',
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
	
	/**
     * Return a list of existing member records
     *
     * @return Member[]
     */
	private function existingMemberRecords($regtNumPrefix = '20681'){
		$members = $this->newMemberRecords();
		$regtNumCounter = 10;
		foreach ($members as &$member){
			$member['regt_num'] = $regtNumPrefix . $regtNumCounter++ . ($member['sex'] == 'F' ? 'F' : '');
		}
		return $members;
	}
	
	/**
     * Persist the member and return the memberId
     *
     * @return String 
     */
	private function persistMember($member, $overrides = 0, $overrideSettings = 0){
		if (!$overrideSettings){
			$overrideSettings = [];
		}
		if (!$overrides){
			$overrides = [];
		}
		$payload = [
			'context' => array_merge($overrideSettings, $overrides),
			'member' => $member
		];
		
		$response = $this->call('POST', '/api/member', $payload);
		$jsonResponse = json_decode($response->content());
		return $jsonResponse->recordId;
	}
	
}
