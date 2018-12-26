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

Auth::routes();
Route::get('auth/api-oauth', function () {				// Member search page
    return view('auth.authorize-api');
});

/* Dashboard */
Route::group(['middleware' => 'auth'], function () {

    Route::get('/', ['as' => 'dashboard', function () {
        return view('dashboard');
    }]);
});

/*
 * FLARES view router. Each view bootstraps its own 
 * Angular 1 app. 
 */
Route::group(['as' => 'member::', 'middleware' => 'auth'], function () {

    // Route::get('members', 'PagePresenter@memberSearch');
    // Route::get('members/new', 'PagePresenter@memberNew');
    Route::get('members', function () {				// Member search page
        return view('member.index');
    });
    Route::get('members/new', function () {         // Simple 1 page form
        return view('member.new-single');
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
    
    // Route::get('member/decorations', function(){
        // return view('member.view-decorations');
    // })->name('view-decorations');
    Route::get('member/decoration/edit', function(){
        return view('member.edit-decoration');
    })->name('edit-decoration');
    Route::get('member/decorations/new', function(){
        return view('member.assign-decoration');
    })->name('assign-decoration');
    
});

Route::group(['as' => 'activity::', 'middleware' => 'auth'], function () {
    
    Route::get('activities', function () {		// dashboard-like overview for all activities
        return view('activity.activityOverview');
    });
    Route::get('activities/new', function () {		// Create new activity
        return view('activity.newActivity');
    });
    Route::get('activities/search', function () {		// dedicated search screen with omni bar
        return view('activity.activitySearch');
    });
    Route::get('activities/awol', function () {		// All activities AWOL dashboard
        return view('activity.awol');
    });
    
    Route::get('activity', function () {
        return view('activity.viewActivity');				// View/edit activity details & nom roll
    });
    Route::get('activity/roll', function () {
        return view('activity.activityRoll');				// Mark the roll and view parade states
    });
});

Route::group(['as' => 'decoration::', 'middleware' => 'auth'], function (){
    
    Route::get('decorations', function (){
        return view('decoration.index');
    })->name('index');
    Route::get('decorations/new', function (){
        return view('decoration.new');
    })->name('new');
    
    Route::get('decoration', function (){
        return view('decoration.view');
    })->name('view');
    
});


/**
 * Public routes
 */
Route::group(['as' => 'public::', 'prefix' => 'public'], function () {
    Route::get('decorations', 'DecorationPublicController@index')->name('decoration-list');
    Route::get('decorations/{shortcode}', 'DecorationPublicController@show')->name('decoration-details');
});

/** 
 * Image and other media content endpoints (separate this from the concerns of the API)
 */
Route::group(['as' => 'media::', 'prefix' => 'media'], function () { 
    Route::get('member/{memberId}/picture', 'MemberPictureController@show')->name('member-picture');
    Route::get('decoration/{decorationId}/badge', 'DecorationBadgeController@show')->name('decoration-badge');
});
