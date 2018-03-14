<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class tblPMSSectioningInfo extends Model
{
    protected $table  = 'tblPMS_sectioning_info';

    public function mhs()
    {
    	return $this->hasMany('App\Models\tblPMSMHInfo', 'PMS_info_id');
    }

    public function pcs()
    {
    	return $this->hasMany('App\Models\tblPMSPCInfo', 'PMS_info_id');
    }
    
    public function ris()
    {
    	return $this->hasMany('App\Models\tblPMSRIInfo', 'PMS_info_id');
    }
}
