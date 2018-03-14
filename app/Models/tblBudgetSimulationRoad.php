<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class tblBudgetSimulationRoad extends Model
{
    protected $table  = 'tblBudget_simulation_road';
	
	public function roads()
	{
		return $this->belongsTo('App\Models\tblRoad', 'road_id');
	}
}
