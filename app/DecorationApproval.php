<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class DecorationApproval extends Model
{
	protected $primaryKey = 'dec_appr_id';
	protected $guarded = ['dec_appr_id', 'is_approved', 'justification', 'user_id', 'decision_date'];       // Don't allow decision data fields to be mass assigned.
	
	// Relationships
	public function requester()
	{
		return $this->belongsTo('App\Member', 'regt_num');
    }
    public function requested_decoration() 
    {
        return $this->belongsTo('App\Decoration', 'dec_id');
    }
    public function approver()
	{
		return $this->belongsTo('App\User', 'user_id');
    }
}
