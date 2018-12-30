<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\User;
use App\Http\Requests;
use App\Http\Controllers\Controller;
use App\Http\Custom\ResponseCodes;

class PersonalAccessTokenController extends Controller
{
    protected $scopes = ['manage-sso', 'sync-members'];

    /**
     * Display a page that prompts to generate a PAT
     */
    public function view(Request $request) 
    {
        $authedUser = $request->user();
        if ($authedUser->access_level >= User::ACCESS_ADMIN) {
            $users = User::all();
        } else {
            $users = [ $authedUser ];
        }
        return view('user.patoken', [
            'users' => $users,
            'scopes' => $this->scopes,
        ]);
    }

    /**
     * Generates and displays the PAT with scopes as selected
     */
    public function generateAndView(Request $request) 
    {
        if ($request->has('scopes')) {
            // Maybe the authed user generated it for 
            $authedUser = $request->user();
            if ($request->has('user_id') && $authedUser->access_level >= User::ACCESS_ADMIN) {
                $targetUser = User::findOrFail($request->input('user_id'));
            } else {
                $targetUser = $authedUser;
            }
            $scopes = $request->input('scopes');
            $token = $targetUser->createToken('PA Token', $scopes)->accessToken;
            return view('user.patoken', [
                'user' => $targetUser,
                'appliedScopes' => implode(', ', $scopes),
                'token' => $token,
            ]);
        } else {
            return $this->view($request);
        }
    }

}


