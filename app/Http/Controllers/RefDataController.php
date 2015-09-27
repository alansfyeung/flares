<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use DB;
use App\Http\Requests;
use App\Http\Controllers\Controller;

class RefDataController extends Controller
{
	public function all(){
		$refData = [];
		$refData['platoons'] = DB::table('ref_platoons')->orderBy('pos', 'asc')->get();
		$refData['ranks'] = DB::table('ref_ranks')->orderBy('pos', 'asc')->get();
		$refData['postings'] = DB::table('ref_postings')->orderBy('pos', 'asc')->get();
		return response()->json($refData);
	}
	
    public function misc(){
		return response()->json(DB::table('ref_misc')->get());
	}
	
    public function platoons(){
		return response()->json(DB::table('ref_platoons')->orderBy('pos', 'asc')->get());
    }
	
	public function ranks(){
		return response()->json(DB::table('ref_ranks')->orderBy('pos', 'asc')->get());
	}
	
	public function postings(){
		return response()->json(DB::table('ref_postings')->orderBy('pos', 'asc')->get());
	}
    
}
