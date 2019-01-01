<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

/*
|--------------------------------------------------------------------------
| API (Client Credentials) Routes
|--------------------------------------------------------------------------
|
| For API requests that use a token from the OAuth client_credentials flow
| -- Important: All requests to these routes must include a HTTP header
| -- X-Api-UsesClientCredentials = 1
|
*/

Route::get('/', function () {
    return response()->json(['version' => 'Flares API (client credentials)'], 200);
});

Route::group(['middleware' => 'clientCredentials:manage-sso'], function() {
    Route::post('usersso/{userId}/link', 'UserSSOController@provisionSSO')->name('link');
    Route::resource('usersso', 'UserSSOController', ['only' => ['store', 'destroy']]);
});

Route::group(['middleware' => 'clientCredentials:submit-decorations'], function() {
    Route::resource('approval', 'DecorationApprovalController', ['only' => ['index', 'store']]);
});

Route::group(['middleware' => 'clientCredentials:sync-members'], function() {
    Route::post('membersync', 'MemberSyncController@sync');
    Route::post('membersync/presync', 'MemberSyncController@presync');
});