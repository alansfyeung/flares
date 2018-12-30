<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class UserSSO extends Model
{
    protected $table = 'user_sso';
    protected $primaryKey = 'sso_id';
    protected $fillable = ['user_id', 'sso_token', 'is_redirect', 'expires_at'];
	
	// Relationships
	public function user() {
		return $this->belongsTo('App\User', 'user_id');
	}
	
}
