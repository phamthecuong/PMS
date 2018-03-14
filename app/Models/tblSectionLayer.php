<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class tblSectionLayer extends Model
{
    protected $table = 'tblSection_layer';

    public function pavementLayer()
    {
    	return $this->belongsTo('App\Models\mstPavementLayer', 'layer_id');
    }

    public function materialType()
    {
    	return $this->belongsTo('App\Models\mstPavementType', 'material_type_id');
    }

    public function rmdSection()
    {
    	return $this->belongsTo('App\Models\tblSectiondataRMD', 'sectiondata_id');
    }

    public function mhSection()
    {
    	return $this->belongsTo('App\Models\tblSectiondataMH', 'sectiondata_id');
    }

    static public function boot()
    {
    	tblSectionLayer::saving(function($rec) {
            if (\Auth::user())
            {
                if ($rec->id)
                {
                    $rec->updated_by = \Auth::user()->id;
                }
                else
                {
                    $rec->created_by = \Auth::user()->id;
                }
            }
        });
    }
}
