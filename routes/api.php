<?php

use Illuminate\Http\Request;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

// Route::get('/user', function (Request $request) {
    // return $request->user();
// })->middleware('auth:api');

// Dashboard API
Route::resource('dashboard', 'DashboardController', ['only' => ['index', 'show']]);

// Member API -- note that Search alias route MUST go before the resourceful route
Route::get('member/search', 'MemberController@search');
Route::get('member/{memberId}/status', 'MemberController@status');
Route::get('member/{memberId}/picture', 'MemberPictureController@exists');
Route::get('member/{memberId}/picture/new', 'MemberPictureController@chunkCheck');
Route::post('member/{memberId}/picture/new', 'MemberPictureController@store');
Route::delete('member/{memberId}/picture', 'MemberPictureController@destroy');
Route::resource('member', 'MemberController', ['only' => ['index', 'store', 'show', 'update', 'destroy']]);
Route::resource('member.posting', 'MemberPostingController', ['only' => ['index', 'store', 'show']]);
Route::resource('member.decoration', 'MemberDecorationController', ['only' => ['index', 'store', 'show', 'destroy']]);

// Route::resource('api/member.documents', 'MemberDocumentController');
// Route::resource('api/awards', 'AwardsController');
// Route::resource('api/systemuser', 'SystemUserController');

// Activtity API
Route::get('activity/search', 'ActivityController@search');
Route::get('activity/{activityId}/awol', 'AttendanceController@awol');		// Get the AWOLs
Route::resource('activity', 'ActivityController', ['only' => ['index', 'store', 'show', 'update', 'destroy']]);
Route::resource('activity.roll', 'AttendanceController', ['only' => ['index', 'store', 'show', 'update', 'destroy']]);

// Decorations API

Route::get('decoration/{decorationId}/badge', 'DecorationBadgeController@exists');                                          // TBA: repurpose
Route::get('decoration/{decorationId}/badge/new', 'DecorationBadgeController@chunkCheck');
Route::post('decoration/{decorationId}/badge/new', 'DecorationBadgeController@store');
Route::delete('decoration/{decorationId}/badge', 'DecorationBadgeController@destroy');
Route::resource('decoration', 'DecorationController', ['only' => ['index', 'store', 'show', 'update', 'destroy']]);

// Admin API
Route::resource('admin', 'AdminController', ['only' => ['index', 'store', 'show', 'update', 'destroy']]);
 
// Ref data routes
Route::get('refdata', 'RefDataController@all');
Route::get('refdata/{key}', 'RefDataController@get');

