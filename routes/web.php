<?php

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| This file is where you may define all of the routes that are handled
| by your application. Just tell Laravel the URIs it should respond
| to using a Closure or controller method. Build something great!
|
*/

/**
 * Authentication (including OAuth) routes
 */
Auth::routes();
Route::group(['prefix' => 'auth'], function () {
    Route::get('sso/{token}', 'UserSSOController@consumeSSO')->name('sso');
});

/**
 * Public routes
 */
Route::group(['as' => 'public::', 'prefix' => 'public'], function () {
    Route::get('decorations', 'DecorationPublicController@index')->name('decorationList');
    Route::get('decorations/{shortcode}', 'DecorationPublicController@show')->name('decorationDetails');
});

/** 
 * Image and other media content endpoints (separate this from the concerns of the API)
 */
Route::group(['as' => 'media::', 'prefix' => 'media'], function () { 
    Route::get('member/{memberId}/picture', 'MemberPictureController@show')->name('memberPicture');
    Route::get('decoration/{decorationId}/badge', 'DecorationBadgeController@show')->name('decorationBadge');
});