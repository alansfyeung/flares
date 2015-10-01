<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Member extends Model
{
	use SoftDeletes;
	
    protected $table = 'member';
	protected $primaryKey = 'regt_num';
	protected $dates = ['deleted_at'];
	
	// Mass fillability
	protected $guarded = ['is_fully_enrolled', 'coms_username', 'coms_id', 'forums_username', 'forums_userid', 'photo_url'];
	
	// Disable any auto-increment business
	public $incrementing = false;
	
	
	// Scopes
	public function postings(){
		return $this->hasMany('App\PostingPromo', 'regt_num'); 
	}
	public function pictures(){
		return $this->hasMany('App\MemberPicture', 'regt_num'); 
	}
	
}