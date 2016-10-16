<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Promotion extends Model
{
    protected $table = 'posting_promo';
	protected $primaryKey = 'promo_id';
	
	// Relationships
	public function member(){
		return $this->belongsTo('App\Member', 'regt_num');
	}
	
}
