<?php

use Illuminate\Http\Request;

/*
|--------------------------------------------------------------------------
| API (Client Credentials) Routes
|--------------------------------------------------------------------------
|
| For API requests that use a token from the OAuth client_credentials flow
|
*/

Route::group(['as' => 'usersso::', 'middleware' => 'clientCredentials:manage-sso'], function() {
    Route::get('usersso/me', 'UserSSOController@me')->name('me');
    Route::post('usersso/{userId}/link', 'UserSSOController@provisionSSO')->name('link');
    Route::post('usersso', 'UserSSOController@store');
    Route::delete('usersso', 'UserSSOController@destroy');
});

Route::get('member/{memberId}/approval', 'DecorationApprovalController@indexMember');