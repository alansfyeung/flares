<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Attendance extends Model
{
    protected $table = 'attendance';
	protected $primaryKey = 'att_id';
	protected $guarded = ['att_id'];
	
	// Relationships
	public function member()
	{
		return $this->belongsTo('App\Member');
	}
	
}
