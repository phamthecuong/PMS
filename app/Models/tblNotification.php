<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class tblNotification extends Model
{
    protected $table = 'tblNotification';
    
    public function tblDeterioration()
    {
        return $this->belongsTo('App\Models\tblDeterioration', 'reference_id');
    }
}
