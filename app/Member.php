<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Member extends Model
{
    protected $table = 'master';
	protected $primaryKey = 'regt_num';
	protected $dates = ['deleted_at'];
	public $incrementing = false;
	
	// Mass fillability
	protected $guarded = ['is_fully_enrolled', 'coms_username', 'coms_id', 'forums_username', 'forums_userid', 'photo_url'];
	
	
	// Scopes
	public function postings(){
		return $this->hasMany('App\PostingPromo', 'regt_num'); 
	}
	
}
