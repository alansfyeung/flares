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


/* Dashboard */
Route::get('/', function () {
    return view('dashboard.index');
})->name('dashboard');

/*
 * FLARES view router. Each view bootstraps its own 
 * Angular 1 app. 
 */
Route::group(['as' => 'member::'], function () {

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
    
    Route::get('member', function () {
        return view('member.view');
    });
    
    // Route::get('member/decorations', function () {
        // return view('member.view-decorations');
    // })->name('view-decorations');
    Route::get('member/decoration/edit', function () {
        return view('member.edit-decoration');
    })->name('editDecoration');
    Route::get('member/decorations/new', function () {
        return view('member.assign-decoration');
    })->name('assignDecoration');
    
});

Route::group(['as' => 'approval::'], function () {

    Route::get('approval', function () {
        return view('member.approve-decoration');
    })->name('approveDecoration');

});


Route::group(['as' => 'activity::'], function () {
    
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

Route::group(['as' => 'decoration::'], function () {
    
    Route::get('decorations', function () {
        return view('decoration.index');
    })->name('index');
    Route::get('decorations/new', function () {
        return view('decoration.new');
    })->name('new');
    
    Route::get('decoration', function () {
        return view('decoration.view');
    })->name('view');
    
});

Route::group(['as' => 'user::'], function() {
    Route::get('users', 'UserController@indexTable');
    Route::get('users/patokens', 'PersonalAccessTokenController@view');
    Route::post('users/patokens', 'PersonalAccessTokenController@generateAndView');
});