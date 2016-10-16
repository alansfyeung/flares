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

// Route::get('/', function () {
    // return view('welcome');
// });


/* Dashboard */
Route::get('/', ['as' => 'dashboard', function () {
    return view('dashboard');
}]);

Route::get('auth/login', 'Auth\AuthController@getLogin');
Route::post('auth/login', 'Auth\AuthController@postLogin');
Route::get('auth/logout', 'Auth\AuthController@getLogout');


/* FLARES views */
Route::group(['as' => 'members::'], function () {
    // Route::get('members', 'PagePresenter@memberSearch');
    // Route::get('members/new', 'PagePresenter@memberNew');
    Route::get('members', function () {				// Member search page
        return view('members.search');
    });
    Route::get('members/new', function () {			// Member bulk add
        return view('members.newMember');
    });
    Route::get('members/stats', function () {			// Member Stats
        return view('members.stats');
    });
    Route::get('members/mass', function () {		// Member Mass Actions
        return view('members.massactions');
    });	
    Route::get('members/reports', function () {		// Member reporting
        return view('members.reports');
    });
});

Route::group(['as' => 'member::'], function (){
    Route::get('member', function(){
        return view('member.viewMember');
    });
});

Route::group(['as' => 'activities::'], function () {
    Route::get('activities', function () {		// dashboard-like overview for all activities
        return view('activities.activityOverview');
    });
    Route::get('activities/new', function () {		// Create new activity
        return view('activities.newActivity');
    });
    Route::get('activities/search', function () {		// dedicated search screen with omni bar
        return view('activities.activitySearch');
    });
    Route::get('activities/awol', function () {		// All activities AWOL dashboard
        return view('activities.awol');
    });
});

Route::group(['as' => 'activity::'], function () {
    Route::get('activity', function () {
        return view('activity.viewActivity');				// View/edit activity details & nom roll
    });
    Route::get('activity/roll', function () {
        return view('activity.activityRoll');				// Mark the roll and view parade states
    });
});