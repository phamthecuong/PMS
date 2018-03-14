<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class tblTVVehicleDetails extends Model
{
    protected $table = 'tblTVVehicle_details';

    public function section()
    {
    	return $this->belongsTo('App\Models\tblSectiondataTV', 'sectiondata_TV_id');
    }
}
