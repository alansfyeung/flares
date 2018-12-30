<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class DecorationApproval extends Model
{
	protected $primaryKey = 'dec_appr_id';
	protected $guarded = ['dec_appr_id'];
	
	// Relationships
	public function requester()
	{
		return $this->belongsTo('App\Member', 'regt_num');
    }
    public function requestedDecoration() 
    {
        return $this->belongsTo('App\Decoration', 'dec_id');
    }
    public function approver()
	{
		return $this->belongsTo('App\User', 'user_id');
    }
}