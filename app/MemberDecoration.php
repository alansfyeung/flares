<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class MemberDecoration extends Model
{	
	protected $primaryKey = 'awd_id';
	protected $dates = ['deleted_at', 'date'];
}