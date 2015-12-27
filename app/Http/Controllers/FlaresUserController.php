<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
// use Illuminate\Foundation\Auth\AuthenticatesUsers;

use Hash;
use App\FlaresUser;
use App\Http\Requests;
use App\Http\Controllers\Controller;
use App\Http\Flares\ResponseCodes;

class FlaresUserController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return Response
     */
    public function index()
    {
        $users = FlaresUser::all();
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
        if ($request->has('user')){
            
            DB::beginTransaction();
            try {
                $postDataUser = $request->input('user', []);
                // Strip out the password field in case it's in $postDataUser
                if (array_key_exists('password', $postDataUser)){
                    unset($postDataUser['password']);
                }
                
                $newUser = FlaresUser::create($postDataUser);
                
                if (!$request->has('password')){
                    throw new \Exception("No password was provided in the postdata - cannot set password.", ResponseCodes::ERR_POSTDATA_MISSING);
                }
                // Hash the password 
                $passwordPlain = $request->password;
                $newUser->password = Hash::make($passwordPlain);
                $newUser->save();
                
                DB::commit();
                return response()->json([
                    'recordId' => $newUser->user_id;
                ]);
            } 
            catch (\Exception $ex) {
                return response()->json([
                    'error' => ['code' => $ex->getCode(), 'reason' => $ex->getMessage()]
                ], 500);
            }
              
        }
        
        return response()->json([
            'error' => ['code' => ResponseCodes::ERR_POSTDATA_MISSING, 'reason' => 'New user postdata missing']
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
        $user = FlaresUser::findOrFail($id);
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
        $updated = 0;
        $passwordUpdated = false;
		
		try {
			if ($request->has('user')){
				$postDataUpdate = $request->user;
				$updated = FlaresUser::updateOrCreate(['user_id' => $id], $postDataUpdate);
			}
            if ($request->has('password')){
                $passwordPlain = $request->password;
                
                // Hash the password 
                $passwordPlain = $request->password;
                $user = FlaresUser::findOrFail($id);
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
    public function destroy($id)
    {
        $deleted = 0;   
        try {
            $deleted = Member::findOrFail($id)->delete();
            return response()->json([
				'success' => $deleted
			]);            
        }
        catch (\Exception $ex){
            return response()->json([
                'error' => ['code' => $ex->getCode(), 'reason' => $ex->getMessage()]
            ], 403);
        }
    }
    
}
