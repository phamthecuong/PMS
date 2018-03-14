<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class tblBudgetSimulationOrganization extends Model
{
    protected $table  = 'tblBudget_simulation_organization';
	
	public function organizations()
	{
		return $this->belongsTo('App\Models\tblOrganization', 'organization_id');
	}
}
