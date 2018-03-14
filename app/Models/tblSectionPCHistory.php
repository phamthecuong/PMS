<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Helper;
class tblSectionPCHistory extends Model
{
    //
    protected $table = 'tblSection_PC_history';

    public function sectionPC()
    {
        return $this->belongsTo('App\Models\tblSectionPC', 'section_id');
    }

    public function sectionPCDate()
    {
    	return $this->belongsTo('App\Models\tblSectionPC', 'date_y', 'date_y');
    }
    
    public function tblOrganization()
    {
        return $this->belongsTo('App\Models\tblOrganization', 'SB_id');
    }

    public function tblBranch()
    {
        return $this->belongsTo('App\Models\tblBranch', 'branch_id');
    }
    
}
