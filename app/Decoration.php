<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Decoration extends Model
{	
	protected $primaryKey = 'dec_id';
	protected $dates = ['deleted_at', 'date_commence', 'date_conclude'];
    protected $guarded = [
        'dec_id', 
        'icon_blob',            // Prevent icon stuff from being mass assigned
        'icon_mime_type',   
        'icon_uri',             // currently unused
        'icon_w', 
        'icon_h', 
        'created_at', 
        'updated_at'
    ];
}