<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class MemberDecoration extends Model
{	
	protected $primaryKey = 'awd_id';
	protected $dates = ['deleted_at', 'date'];
    
    public function decoration()
    {
        return $this->hasOne('App\Decoration', 'dec_id', 'dec_id');
    }
    
    public function member()
    {
        return $this->belongsTo('App\Member', 'regt_num', 'regt_num');
    }
    
}