<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class tblRepairMethodCost extends Model
{
    protected $table = 'tblRepair_method_cost';

    public function repairMethod()
    {
        return $this->belongsTo('App\Models\mstRepairMethod');
    }

    public function organization()
    {
        return $this->belongsTo('App\Models\tblOrganization');
    }
}
