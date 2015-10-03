<?php
namespace App\Http\Controllers;

use App\Http\Controllers\Controller;

class PagePresenter extends Controller
{
    /**
     * Render the requested view  --- NOT USED
     *
     * @param  string  $view
     * @return Response
     */
    public function render($view){
		return "You requested $view";
        // return view('user.profile', ['user' => User::findOrFail($id)]);
    }
	
	// public function memberSearch(){
		// return view('members.search');
	// }
	// public function memberNew(){
		// return view('members.new');
	// }
	// public function memberStats(){
		// return view('members.stats');
	// }
	// public function memberMassActions(){
		// return view('members.massactions');
	// }
	
}