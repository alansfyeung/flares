<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class MemberPicture extends Model
{
    protected $table = 'member_picture';
	protected $primaryKey = 'img_id';
	protected $guarded = ['img_id', 'created_at', 'updated_at'];
	
	// Default values
	protected $attributes = array(
	   'mime_type' => 'image/png',
	);
	
	// Relationships
	public function member(){
		return $this->belongsTo('App\Member', 'regt_num');
	}

}
