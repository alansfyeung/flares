<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class MemberPicture extends Model
{
    protected $table = 'member_picture';
	protected $primaryKey = 'img_id';
	
	protected $guarded = ['img_id', 'created_at', 'updated_at'];
	
	protected $attributes = array(
	   'mime_type' => 'image/png',
	);

}
