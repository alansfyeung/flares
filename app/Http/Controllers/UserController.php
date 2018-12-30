<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use DB;
use Hash;
use App\User;
use App\Http\Requests;
use App\Http\Controllers\Controller;
use App\Http\Custom\ResponseCodes;

class UserController extends Controller
{
    /**
     * Display a HTML page listing of the resource
     */
    public function indexTable()
    {
        $users = User::all();
        return view('user.index', ['users' => $users]);
    }

    /**
     * Display a listing of the resource.
     *
     * @return Response
     */
    public function index()
    {
        $users = User::all();
        return response()->json([
            'users' => $users->toArray()
        ]);
    }
    
    /**
     * Store a newly created resource in storage.
     *
     * @param  Request  $request
     * @return Response
     */
    public function store(Request $request)
    {
        // Check for access to create new users
        $requester = $request->user();
        if (empty($requester) || empty($requester->access_level) || $requester->access_level < User::ACCESS_ADMIN) {
            return response()->json([
                'error' => ['code' => ResponseCodes::ERR_P_INSUFFICIENT, 'reason' => 'Only ACCESS_ADMIN or above can create or modify users'],
            ], 403);
        }

        if ($request->has('user')) {
            
            DB::beginTransaction();
            try {
                $postDataUser = $request->input('user', []);
                // Strip out the password field in case it's in $postDataUser
                if (array_key_exists('password', $postDataUser)){
                    unset($postDataUser['password']);
                }
                
                $newUser = User::create($postDataUser);
                
                if (!$request->has('password')){
                    throw new \Exception("No password was provided in the postdata - cannot set password.", ResponseCodes::ERR_POSTDATA_MISSING);
                }
                // Hash the password 
                $passwordPlain = $request->password;
                $newUser->password = Hash::make($passwordPlain);
                $newUser->save();
                
                DB::commit();
                return response()->json([
                    'recordId' => $newUser->user_id,
                ]);
            } catch (\Exception $ex) {
                DB::rollBack();
                return response()->json([
                    'error' => ['code' => $ex->getCode(), 'reason' => $ex->getMessage()]
                ], 500);
            }
              
        }
        
        return response()->json([
            'error' => ['code' => ResponseCodes::ERR_POSTDATA_MISSING, 'reason' => 'New user postdata missing'],
        ], 400);
		
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return Response
     */
    public function show($id)
    {
        $user = User::findOrFail($id);
        return response()->json([
            'user' => $user->toArray()
        ]);
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
        // Check for access to edit new users
        $requester = $request->user();
        if (empty($requester) || empty($requester->access_level) || $requester->access_level < User::ACCESS_ADMIN) {
            return response()->json([
                'error' => ['code' => ResponseCodes::ERR_P_INSUFFICIENT, 'reason' => 'Only ACCESS_ADMIN or above can create or modify users'],
            ], 403);
        }

        $updated = 0;
        $passwordUpdated = false;
		
		try {
			if ($request->has('user')){
				$postDataUpdate = $request->user;
				$updated = User::updateOrCreate(['user_id' => $id], $postDataUpdate);
			}
            if ($request->has('password')){
                $passwordPlain = $request->password;
                
                // Hash the password 
                $passwordPlain = $request->password;
                $user = User::findOrFail($id);
                $user->password = Hash::make($passwordPlain);
                $user->save();
                
                $updated = $user->user_id;
                $passwordUpdated = true;
            }
            
			if ($updated == 0) {         // presume it didn't update?
				throw new \Exception('Post data incorrectly formatted', ResponseCodes::ERR_POSTDATA_FORMAT);
			}
            
			return response()->json([
                'recordId' => $updated,
                'passwordUpdated' => $passwordUpdated
            ]);
		} catch (\Exception $ex){
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
    public function destroy($id)
    {
        // Check for access to edit new users
        $requester = $request->user();
        if (empty($requester) || empty($requester->access_level) || $requester->access_level < User::ACCESS_ADMIN) {
            return response()->json([
                'error' => ['code' => ResponseCodes::ERR_P_INSUFFICIENT, 'reason' => 'Only ACCESS_ADMIN or above can create or modify users'],
            ], 403);
        }

        $deleted = 0;   
        try {
            $deleted = Member::findOrFail($id)->delete();
            return response()->json([
				'success' => $deleted
			]);            
        } catch (\Exception $ex){
            return response()->json([
                'error' => ['code' => $ex->getCode(), 'reason' => $ex->getMessage()]
            ], 403);
        }
    }
    
}
