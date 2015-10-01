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
		return view('dashboard.main');
	});
	
	
	/* FLARES views */
	
	Route::group(['as' => 'members::'], function(){
		
		// Member search page
		Route::get('members', 'PagePresenter@memberSearch');
		
		// Member bulk add
		Route::get('members/new', 'PagePresenter@memberNew');
		
	});
	
	Route::group(['as' => 'member::'], function(){
		Route::get('member', function(){
			return view('member.view');
		});
	});
	
	
	
	/* 
	 * FLARES Resource Controller ( access via AJAX )
	 */
	Route::get('api/member/search', 'MemberController@index');		// search endpoint (alias)
	Route::resource('api/member', 'MemberController', ['only' => ['index', 'store', 'show', 'update', 'destroy']]);
	Route::resource('api/member.picture', 'MemberPictureController', ['only' => ['store', 'show', 'destroy']]);
	Route::resource('api/member.posting', 'MemberPostingController', ['only' => ['index', 'store', 'show']]);
	// Route::resource('api/member.documents', 'MemberDocumentController');
	// Route::resource('api/awards', 'AwardsController');
	// Route::resource('api/systemuser', 'SystemUserController');
	 
	// Ref data routes
	Route::get('api/refdata', 'RefDataController@all');
	Route::get('api/refdata/misc', 'RefDataController@misc');
	Route::get('api/refdata/platoons', 'RefDataController@platoons');
	Route::get('api/refdata/ranks', 'RefDataController@ranks');
	Route::get('api/refdata/postings', 'RefDataController@postings');
	
	 
	// Rendering views to screen
	// Route::get('v/{view}', 'Presenter@render');
	 
	// View all members
	// Route::get('', );
	 
	// Specific individual member
	// Route::get('member/{action}/{id?}', );
	 
// });
