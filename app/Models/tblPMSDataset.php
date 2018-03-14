<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\tblPMSDatasetInfo;

class tblPMSDataset extends Model
{
    protected $table  = 'tblPMS_dataset';

    public static function boot()
    {
        parent::boot();

        tblPMSDataset::deleted(function($rec) {
		    tblPMSDatasetInfo::where('PMS_Dataset_id', $rec->id)->delete();
		});
    }
}
