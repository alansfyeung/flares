<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

use App\User;
use App\UserSSO;
use App\Http\Controllers\Controller;
use App\Http\Custom\ResponseCodes;

class UserSSOController extends Controller
{

    const SSO_SCOPE_NAME = 'manage-sso';

    /**
     * Store a newly created resource in storage.
     *
     * @param  Request  $request
     * @return Response
     */
    public function store(Request $request)
    {
        if ($request->has('user')) {

            // Simple validations
            $this->validate($request, [
                'user.forums_username' => 'required',
                'user.access_level' => 'sometimes|max:'.User::ACCESS_CREATE,     // Their userlevel must not be higher than create
            ]);

            $postDataUser = $request->input('user', []);

            // Conventionally, start their usernames with 'f.', and if the provided username doesn't, then prepend it. 
            if (!empty($postDataUser['username'])) {
                if (substr($postDataUser['username'], 0, 2) != 'f.') {
                    $postDataUser['username'] = 'f.'.$postDataUser['username'];
                }
            } else {
                $postDataUser['username'] = 'f.'.$postDataUser['forums_username'];
            }

            if (empty($postDataUser['access_level'])) {
                $postDataUser['access_level'] = User::ACCESS_ASSIGN;
            }
            $postDataUser['allow_sso'] = 1;
            
            try {
                // Check if this user actually was just deactivated or something
                $existingUser = User::where('username', $postDataUser['username'])->first();
                if ($existingUser) {
                    $existingUser->access_level = $postDataUser['access_level'];
                    $existingUser->save();
                    $user = $existingUser;
                } 
                else {
                    $newUser = new User($postDataUser);
                    $newUser->password = 'x';  // This field is guarded
                    $newUser->save();
                    $user = $newUser;
                }
                return response()->json([
                    'user_id' => $user->user_id,
                    'username' => $user->username, 
                    'forums_username' => $user->forums_username, 
                    'access_level' => $user->access_level, 
                ]);
            } catch (\Exception $ex) {
                return response()->json([
                    'error' => ['code' => $ex->getCode(), 'reason' => $ex->getMessage()]
                ], 500);
            }
              
        } else {
            return response()->json([
                'error' => ['code' => ResponseCodes::ERR_POSTDATA_MISSING, 'reason' => 'New user postdata missing'],
            ], 400);
        }
    }

    /**
     * Disable the SSO user. Note that this will just prevent them from SSO-ing, but their user record will remain.
     *
     * @param  int  $id
     * @param  Request  $request
     * @return Response
     */
    public function destroy(Request $request, $id)
    {
        $deleted = false;
        try {
            $user = User::findOrFail($id);
            $user->access_level = User::ACCESS_NONE;
            $deleted = $user->save();
            return response()->json([
				'success' => $deleted,
			]);
        } catch (\Exception $ex){
            return response()->json([
                'error' => ['code' => $ex->getCode(), 'reason' => $ex->getMessage()]
            ], 403);
        }
    }

    /**
     * Provision a link to for Single-Sign On
     *
     * @param  int  $id
     * @return Response
     */
    public function provisionSSO(Request $request, $userId)
    {
        $user = User::findOrFail($userId);
        // Is this user actually an SSO user?
        if (empty($user->allow_sso) || $user->allow_sso != 1) {
            return response()->json([
                'error' => ['code' => ResponseCodes::ERR_P_ENTITY_NOT_ALLOWED, 'reason' => 'The user is not enabled for SSO'],
            ], 422);
        }

        try {

            // If user has an unused unexpired link, then use that. Otherwise, create a new one
            $existingSSO = UserSSO::where('user_id', $user->user_id)
                            ->whereNotNull('sso_token')
                            ->where('expires_at', '>', date('Y-m-d H:i:s'))
                            ->first();
            if (!empty($existingSSO)) {
                return response()->json([
                    'token' => $existingSSO->sso_token,
                    'link' => route('sso', ['token' => $existingSSO->sso_token]),
                ]);
            }

            $linkToken = md5($userId . str_random(32));

            $sso = $user->userSSO()->create([
                'sso_token' => $linkToken,
                'is_redirect' => 1,
                'expires_at' => date('Y-m-d H:i:s', strtotime('+ 1 hour')),
            ]);

            return response()->json([
                'token' => $linkToken,
                'link' => route('sso', ['token' => $linkToken]),
            ]);
        } catch (\Exception $ex) {
            return response()->json([
                'error' => ['code' => $ex->getCode(), 'reason' => $ex->getMessage()]
            ], 500);
        }
    }

    public function consumeSSO(Request $request, $token) 
    {
        $sso = UserSSO::where('sso_token', $token)->whereNotNull('sso_token')->first();

        // If non-existent or expired, then return an error
        if (empty($sso) || (strtotime($sso->expires_at) < time())) {
            if (!empty($sso) && !empty($sso->sso_token)) {
                // Get rid of the SSO link, to keep it consistent with other 'sso records that can no longer be used'
                $sso->sso_token = null;
                $sso->save(); 
            } else {
                if ($request->expectsJson()) {
                    return response()->json([
                        'error' => ['code' => ResponseCodes::ERR_LINK_INVALID, 'reason' => 'The link you provided is invalid or expired'],
                    ]);
                } else {
                    abort(400, 'Bad SSO token: The link you provided is invalid or expired');
                    // return response('Bad SSO token', 400);
                }
            }
        }
        
        // Log this user in, then unset the sso token so it can't be used again
        Auth::loginUsingId($sso->user_id);
        $sso->sso_token = null;
        $sso->save();

        if ($request->expectsJson()) {
            return response()->json([
                'success' => true,
            ]);
        } else {
            if ($sso->is_redirect) {
                return redirect()->route('dashboard');
            } else {
                return response('', 204);      // 204 no content
            }
        }

    }

    private function checkAccessErrors(Request $request) 
    {
        // Check for access to delete users
        $requester = $request->user();
        if (empty($requester) || empty($requester->access_level) || $requester->access_level < User::ACCESS_ADMIN) {
            return ['code' => ResponseCodes::ERR_P_INSUFFICIENT, 'reason' => 'Only ACCESS_ADMIN or above can create or modify users'];
        }
        // Specifically check for SSO scope before actioning
        if (!$requester->tokenCan(self::SSO_SCOPE_NAME)) {
            return ['code' => ResponseCodes::ERR_P_OAUTH_SCOPE, 'reason' => 'OAuth token requires scope '.self::SSO_SCOPE_NAME.' to perform this action'];
        }
        return null;        // No issues!
    }
    
}
