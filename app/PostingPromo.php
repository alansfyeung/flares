<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class PostingPromo extends Model
{
    protected $table = 'posting_promo';
	protected $primaryKey = 'posting_id';
	protected $dates = ['deleted_at'];
	
	
	
}
