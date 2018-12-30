<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

use App\User;
use App\Http\Requests;
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
        // Check for access to create new users
        if (($accessError = $this->checkAccessErrors($request))) {
            return response()->json([
                'error' => $accessError,
            ], 403);
        }

        if ($request->has('user')) {

            // Simple validations
            $this->validate($request, [
                'user.forums_username' => 'required',
                'user.access_level' => 'sometimes|max:'.User::ACCESS_ASSIGN,     // Their userlevel must not be higher than assign/approve
            ]);

            $postDataUser = $request->input('user', []);

            // Check that it has a forums username
            // if (!array_key_exists('forums_username', $postDataUser) || empty($postDataUser['forums_username'])) {
            //     return response()->json([
            //         'error' => ['code' => ResponseCodes::ERR_POSTDATA_MISSING, 'reason' => 'Forums username field required for SSO'],
            //     ], 403);
            // }

            // Conventionally, start their usernames with 'f.', and if the provided username doesn't, then prepend it. 
            if (array_key_exists('username', $postDataUser)) {
                if (substr($postDataUser['username'], 0, 2) != 'f.') {
                    $postDataUser['username'] = 'f.'.$postDataUser['username'];
                }
            } else {
                $postDataUser['username'] = 'f.'.$postDataUser['forums_username'];
            }

            // Strip out the password field in case it's in $postDataUser
            if (array_key_exists('password', $postDataUser)){
                unset($postDataUser['password']);
            }
            
            try {
                $newUser = User::create($postDataUser);
                return response()->json([
                    'id' => $newUser->user_id,
                    'forums_username' => $newUser->forums_username, 
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
     * @return Response
     */
    public function destroy(Request $request, $id)
    {
        // Check for access to delete users
        if (($accessError = $this->checkAccessErrors($request))) {
            return response()->json([
                'error' => $accessError,
            ], 403);
        }

        $deleted = false;
        try {
            $member = Member::findOrFail($id);
            $member->access_level = User::ACCESS_NONE;
            $deleted = $member->save();
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
    public function provisionSSO(Request $request, $id)
    {
        // Are they allowed to provision?
        if (($accessError = $this->checkAccessErrors($request))) {
            return response()->json([
                'error' => $accessError,
            ], 403);
        }

        $user = User::findOrFail($id);
        try {

            $linkToken = md5($id . str_random(32));

            $sso = $user->userSSO()->create([
                'sso_token' => $linkToken,
                'is_redirect' => 1,
                'expiry_date' => new Date('Y-m-d H:i:s', strtotime('+ 1 week')),
            ]);

            // Figure out the full URL for redeeming this 
            $url = route('sso', ['token' => $linkToken]);

            return response()->json([
                'token' => $linkToken,
                'link' => $url,
            ]);
        } catch (\Exception $ex) {
            return response()->json([
                'error' => ['code' => $ex->getCode(), 'reason' => $ex->getMessage()]
            ], 500);
        }
    }

    public function consumeSSO(Request $request, $token) 
    {
        $sso = UserSSO::where('sso_token', $token)->get();

        // If non-existent or expired, then return an error
        if (empty($sso) || (strtotime($sso->expires_at) < time())) {
            if (!empty($sso) && !empty($sso->sso_token)) {
                // Get rid of the SSO link, to keep it consistent with other 'sso records that can no longer be used'
                $sso->sso_token = null;
                $sso->save(); 
            } 
            if ($request->ajax()) {
                response()->json([
                    'error' => ['code' => ResponseCodes::ERR_LINK_INVALID, 'reason' => 'The link you provided is invalid or expired'],
                ]);
            } else {
                abort(400, 'Bad SSO token');
            }
        }
        
        // Log this user in, then unset the sso token so it can't be used again
        Auth::loginUsingId($sso->user_id);
        $sso->sso_token = null;
        $sso->save();

        if ($request->ajax()) {
            response()->json([
                'success' => true,
            ]);
        } else {
            if ($sso->is_redirect) {
                redirect()->route('dashboard');
            } else {
                response('', 204);      // 204 no content
            }
        }

    }

    private function checkAccessErrors(Request $request) {
        // Check for access to delete users
        $requester = $request->user();
        if (empty($requester) || empty($requester->access_level) || $requester->access_level < User::ACCESS_ADMIN) {
            return ['code' => ResponseCodes::ERR_P_INSUFFICIENT, 'reason' => 'Only ACCESS_ADMIN or above can create or modify users'];
        }
        // Specifically check for SSO scope before actioning
        if (!$requester->tokenCan(SSO_SCOPE_NAME)) {
            return ['code' => ResponseCodes::ERR_P_OAUTH_SCOPE, 'reason' => 'OAuth token requires scope '.SSO_SCOPE_NAME.' to perform this action'];
        }
        return null;        // No issues!
    }
    
}
