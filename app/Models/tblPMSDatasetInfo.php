<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class tblPMSDatasetInfo extends Model
{
    protected $table  = 'tblPMS_dataset_info';

    public function latestSegment()
    {
        return $this->hasOne('App\Models\tblSegmentHistory', 'segment_id', 'segment_id')->latest();
    }

    public function sectioning()
    {
    	return $this->belongsTo('App\Models\tblPMSSectioning', 'PMS_Dataset_id');
    }

    public function sb()
    {
        return $this->belongsTo('App\Models\tblOrganization', 'sb_id');
    }

    public function scopeGetSectionDataWithYear($query, $ri_flg, $pc_flg, $mh_flg, $year)
    {
    	return $query->where('ri_flg', $ri_flg)->where('pc_flg', $pc_flg)->where('mh_flg', $mh_flg)->where('year_of_dataset', $year);
    }

    public function scopeGetSectionDataByRMB($query, $rmb_id)
    {
        return $query->whereHas('sb', function($sb_query) use ($rmb_id) {
            // $sb_query->whereHas('rmb', function($rmb_query) use ($rmb_id) {
            //     $rmb_query->where('id', $rmb_id);
            // });
            $sb_query->where('parent_id', $rmb_id);
        });
    }

}
