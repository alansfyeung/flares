<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Activity extends Model
{
    protected $table = 'activity';
	protected $primaryKey = 'acty_id';
	protected $guarded = ['acty_id'];
	
	// Relationships
	public function attendances()
	{
		return $this->hasMany('App\Attendance', 'acty_id');
	}
	
}
