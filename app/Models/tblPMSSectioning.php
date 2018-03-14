<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class tblPMSSectioning extends Model
{
    protected $table  = 'tblPMS_sectioning';

    public function datasets()
    {
    	return $this->hasMany('App\Models\tblPMSDatasetInfo', 'PMS_Dataset_id');
    }

    public function infos()
    {
    	return $this->hasMany('App\Models\tblPMSSectioningInfo', 'PMS_section_id');
    }
    
    public function branch()
    {
    	return $this->belongsTo('App\Models\tblBranch', 'branch_id');
    }
}
