<?php

/*
|--------------------------------------------------------------------------
| Application Routes
|--------------------------------------------------------------------------
|
| Here is where you can register all of the routes for an application.
| It's a breeze. Simply tell Laravel the URIs it should respond to
| and give it the controller to call when that URI is requested.
|
*/


// Route::group(['middleware' => 'auth'], function () {			

	/* Dashboard -- Home page */
	Route::get('/', function () {
		return view('dashboard');
	});
	
	
	/* FLARES views */
	Route::group(['as' => 'members::'], function(){
		// Route::get('members', 'PagePresenter@memberSearch');
		// Route::get('members/new', 'PagePresenter@memberNew');
		Route::get('members', function(){				// Member search page
			return view('members.search');
		});
		Route::get('members/stats', function(){			// Member Stats
			return view('members.stats');
		});
		Route::get('members/mass', function(){		// Member Mass Actions
			return view('members.massactions');
		});	
		Route::get('members/reports', function(){		// Member Mass Actions
			return view('members.reports');
		});
	});
	
	Route::group(['as' => 'member::'], function(){
		Route::get('members/new', function(){			// Member bulk add
			return view('member.newMember');
		});
		Route::get('member', function(){
			return view('member.viewMember');
		});
	});
	
	Route::group(['as' => 'activities::'], function(){
		Route::get('activities', function(){		// dashboard-like overview for all activities
			return view('activities.overview');
		});
		Route::get('activities/awol', function(){		// All activities AWOL dashboard
			return view('activities.awol');
		});
	});
	
	Route::group(['as' => 'activity::'], function(){
		Route::get('activities/new', function(){		// Create new activity
			return view('activity.newActivity');
		});
		Route::get('activity', function(){
			return view('activity.viewActivity');				// View/edit activity details & nom roll
		});
		Route::get('activity/roll', function(){
			return view('activity.roll');				// Mark the roll
		});
	});
	
	/* 
	 * FLARES Resource Controller ( access via AJAX )
	 */
	Route::group(['as' => 'res::'], function(){
	
		// Dashboard API
		Route::resource('api/dashboard', 'DashboardController', ['only' => ['index', 'show']]);
		
		// Member API -- note that Search alias route MUST go before the resourceful route
		Route::get('api/member/search', 'MemberController@index');		// search endpoint (alias of 'index')
		Route::get('api/member/{memberId}/status', 'MemberController@status');
		Route::get('api/member/{memberId}/picture', 'MemberPictureController@show');
		Route::get('api/member/{memberId}/picture/exists', 'MemberPictureController@exists');
		Route::get('api/member/{memberId}/picture/new', 'MemberPictureController@chunkCheck');
		Route::post('api/member/{memberId}/picture/new', 'MemberPictureController@store');
		Route::delete('api/member/{memberId}/picture', 'MemberPictureController@destroy');
		Route::resource('api/member', 'MemberController', ['only' => ['index', 'store', 'show', 'update', 'destroy']]);
		Route::resource('api/member.posting', 'MemberPostingController', ['only' => ['index', 'store', 'show']]);
		
		// Route::resource('api/member.documents', 'MemberDocumentController');
		// Route::resource('api/awards', 'AwardsController');
		// Route::resource('api/systemuser', 'SystemUserController');
		
		// Activtity API
		Route::get('api/activity/search', 'ActivityController@index');		// search endpoint (alias of 'index')
		Route::get('api/activity/{activityId}/awol', 'AttendanceController@awol');		// Get the AWOLs
		Route::resource('api/activity', 'ActivityController', ['only' => ['index', 'store', 'show', 'update', 'destroy']]);
		Route::resource('api/activity.roll', 'AttendanceController', ['only' => ['index', 'store', 'show', 'update', 'destroy']]);
		
		 
		// Ref data routes
		Route::get('api/refdata', 'RefDataController@all');
		Route::get('api/refdata/misc', 'RefDataController@misc');
		Route::get('api/refdata/platoons', 'RefDataController@platoons');
		Route::get('api/refdata/ranks', 'RefDataController@ranks');
		Route::get('api/refdata/postings', 'RefDataController@postings');
		Route::get('api/refdata/activity', 'RefDataController@activity');

	});
	 
	// Rendering views to screen
	// Route::get('v/{view}', 'Presenter@render');
	 
	// View all members
	// Route::get('', );
	 
	// Specific individual member
	// Route::get('member/{action}/{id?}', );
	 
// });
