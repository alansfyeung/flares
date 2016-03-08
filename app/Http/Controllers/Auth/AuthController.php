<?php

namespace App\Http\Controllers\Auth;

use Validator;
use App\FlaresUser;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Foundation\Auth\ThrottlesLogins;
use Illuminate\Foundation\Auth\AuthenticatesUsers;

class AuthController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Registration & Login Controller
    |--------------------------------------------------------------------------
    |
    | This controller handles the registration of new users, as well as the
    | authentication of existing users. By default, this controller uses
    | a simple trait to add these behaviors. Why don't you explore it?
    |
    */

    use AuthenticatesUsers, ThrottlesLogins;

    protected $redirectPath = '/';
    protected $username = 'username';        // overrides the trait default of username

    /**
     * Create a new authentication controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('guest', ['except' => 'getLogout']);
    }


    
    /**
     * FLARES
     * Capture and handle post-authenticated
     *
     * @param  \Illuminate\Http\Request
     * @param  \App\FlaresUser
     * @return \Illuminate\Http\Response
     */ 
    protected function authenticated(\Illuminate\Http\Request $request, $user)
    {
        // Record their login time
        $user->last_login_time = date('Y-m-d H:i:s');
        $user->save();
        
        return redirect()->intended($this->redirectPath());
    }

}
