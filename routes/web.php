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

Route::get('login', 'Auth\AuthController@getLogin');
Route::post('login', 'Auth\AuthController@postLogin');
Route::get('logout', 'Auth\AuthController@getLogout');


/* 
 * FLARES view router. Each view bootstraps its own 
 * Angular 1 app. 
 */
Route::group(['as' => 'member::'], function (){

    // Route::get('members', 'PagePresenter@memberSearch');
    // Route::get('members/new', 'PagePresenter@memberNew');
    Route::get('members', function () {				// Member search page
        return view('member.search');
    });
    Route::get('members/new', function () {         // Simple 1 page form
        return view('member.new-simple');
    });
    Route::get('members/newmulti', function () {			// Member bulk add
        return view('member.new-multi');
    });
    Route::get('members/stats', function () {			// Member Stats
        return view('member.stats');
    });
    Route::get('members/mass', function () {		// Member Mass Actions
        return view('member.mass-actions');
    });	
    Route::get('members/reports', function () {		// Member reporting
        return view('member.reports');
    });
    
    Route::get('member', function(){
        return view('member.view');
    });
});

Route::group(['as' => 'activity::'], function () {
    
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
    
    Route::get('activity', function () {
        return view('activity.viewActivity');				// View/edit activity details & nom roll
    });
    Route::get('activity/roll', function () {
        return view('activity.activityRoll');				// Mark the roll and view parade states
    });
});

Route::group(['as' => 'decoration::'], function(){
    
    Route::get('decorations', function(){
        return view('decoration.search');
    });
        Route::get('decorations/new', function(){
        return view('decoration.new');
    });
    
    Route::get('decoration', function(){
        return view('decoration.view');
    });
    
});

