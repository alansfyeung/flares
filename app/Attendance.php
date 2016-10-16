<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Attendance extends Model
{
    protected $table = 'attendance';
	protected $primaryKey = 'att_id';
	protected $guarded = ['att_id', 'recorded_by'];
	
	// Relationships
	public function member()
	{
		return $this->belongsTo('App\Member', 'regt_num');
	}
	public function activity()
	{
		return $this->belongsTo('App\Activity', 'acty_id');
	}
	public function prev_att()
	{
		return $this->hasOne('App\Attendance', 'att_id', 'prev_att_id');		// Previously related record
	}
	public function future_att()
	{
		return $this->hasOne('App\Attendance', 'prev_att_id', 'att_id');		// Future related record
	}
	
}
