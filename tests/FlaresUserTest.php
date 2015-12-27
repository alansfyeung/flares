<?php

use Illuminate\Foundation\Testing\WithoutMiddleware;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Foundation\Testing\DatabaseTransactions;

use Hash;
use App\FlaresUser;

class FlaresUserTest extends TestCase
{
	
	use DatabaseTransactions;
	use WithoutMiddleware;
	
	protected $model = App\FlaresUser::class;
	
    /**
     *  Create some FlaresUsers
     */
    public function testCreateUsers()
    {	
		$users = $this->newRecords();
		foreach ($users as $user){
            $password = $user['password'];
			$payload = [
				'user' => $user,
                'password' => $password
			];
			$response = $this->call('POST', '/api/flaresuser', $payload);
			$this->assertEquals(200, $response->status());
			
			$jsonResponse = json_decode($response->content());
			$this->assertNotEmpty($jsonResponse);
			
			$userId = $jsonResponse->recordId;
			// $this->assertRegExp('/^206\d{4,5}F?$/', $memberId);
			
			$this->get("/api/flaresuser/$userId")
				->seeJson([
					'user_id' => $userId
				]);
            
            
            // Todo: Test authenticating with a password
            // TBA
            
            
		}
	}
    
    /**
     *  Edit users
     *  persistUser() will save a single user and return the user_id
     *  Test by applying some edits to it
     */
    public function testEditUserWithoutPassword()
    {	
		$users = $this->newRecords(1);
        $userId = $this->persistUser($user);
        
        
		// Apply some updates
		$user['email'] = 'UpdatedName';
		$user['forums_username'] = 'Smith';
		$user['access_level'] = '40';
		
		$payload = [
			'user' => $user
		];
		$resp = $this->call('PUT', "/api/flaresuser/$userId", $payload);
		$this->assertEquals(200, $resp->status());
        
        // Todo: expect $resp->content() is json
        // and that it contains passwordUpdated => false
		
		$this->get("/api/flaresuser/$userId")
			->seeJson([
				'user_id' => $userId,
				'email' => $user['email'],
				'forums_username' => $user['forums_username'],
				'access_level' => $user['access_level']
			]);
	}
    
    /**
     *  Edit user password
     */
    public function testEditUserWithPassword()
    {	
		$users = $this->newRecords(1);
        $userId = $this->persistUser($user);  
		
		$payload = [
			'password' => str_random(10)
		];
		$this->call('PUT', "/api/flaresuser/$userId", $payload)
            ->assertResponseStatus(200);
		// $this->assertEquals(200, $resp->status());
		
        // Todo: expect $resp->content() is json
        // and that it contains passwordUpdated => false
		
		// Todo: Test authenticating with a password
        
	}
    
    /**
     * Persist the user and return the user_id 
     *
     * @return String 
     */
	private function persistUser($user, $overrides = 0, $overrideSettings = 0)
    {
		if (!$overrideSettings){
			$overrideSettings = [];
		}
		if (!$overrides){
			$overrides = [];
		}
		$payload = [
			'user' => $user
		];
		$response = $this->call('POST', '/api/flaresuser', $payload);
		$jsonResponse = json_decode($response->content());

        if (property_exists($jsonResponse, 'error')){
            $this->fail('Cannot persist user: '.$jsonResponse->error->reason);
        }
        
		return $jsonResponse->recordId;
	}
    
    /**
     * Persist the user and return the user_id 
     *
     * @return String 
     */
	private function testPassword($username, $expectedPassword)
    {
        // Make a post to the /auth/login endpoint
        // Method 1: See if it takes us back to /auth/login page aka unsuccessful
        // Method 2: If we can see the dashboard then successful...
        
        $payload = [
            'username' => $username, 
            'password' => $expectedPassword, 
            'remember' => 1
        ];
        $this->post('/api/flaresuser', $payload)->assertRedirectedToRoute('dashboard');
        
    }
    
}
