<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use DB;

class tblSegmentHistory extends Model
{
    protected $table = 'tblSegment_history';

    protected $appends = ["segment_info"];

    public function latestSB()
    {
        return $this->belongsTo('App\Models\tblOrganizationHistory', 'SB_id', 'organization_id')->latest();
    }
    public function branch()
    {
        return $this->belongsTo('App\Models\tblBranch', 'branch_id');
    }
     public function tblCity_from()
    {
        return $this->belongsTo('App\Models\tblCity', 'prfrom_id');
    }
    
    public function tblCity_to()
    {
        return $this->belongsTo('App\Models\tblCity', 'prto_id');
    }
    
    public function tblDistrict_from()
    {
        return $this->belongsTo('App\Models\tblDistrict', 'distfrom_id');
    }
    
    public function tblDistrict_to()
    {
        return $this->belongsTo('App\Models\tblDistrict', 'distto_id');
    }

    function getSegmentInfoAttribute()
	{
		$lang = (\App::isLocale('en')) ? 'en' : 'vn';
        return 'Km' . $this->km_from . '+' . $this->m_from . ' - ' . 'Km' . $this->km_to . '+' . $this->m_to . ': ' . $this->{"segname_{$lang}"};
	}
}
