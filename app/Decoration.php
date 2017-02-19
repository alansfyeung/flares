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
        'badge_blob',            // Prevent icon stuff from being mass assigned
        'badge_uri',             // currently unused
        'badge_w', 
        'badge_h', 
        'badge_mime_type',   
        'created_at', 
        'updated_at'
    ];
    
    protected $hidden = [ 
        'badge_blob',
        'badge_uri',
        'badge_w', 
        'badge_h',
        'badge_mime_type', 
    ];
    
    public function related()
    {
        return $this->hasMany('App\Decoration', 'parent_id');
    }
    
    public function scopeOrdered($query)
    {
        return $query
            ->orderBy('tier', 'asc')
            ->orderBy('precedence', 'asc')
            ->orderBy('parent_order', 'asc');
    }
    
    
}