<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Config;

class tblRepairMatrixMethod extends Model
{
	use SoftDeletes;
	
    protected $table = 'tblRepair_matrix_method';
	protected $dates = array('deleted_at');
	
	public function repairMethod()
	{
		return $this->belongsTo('App\Models\mstRepairMethod', 'repair_method_id');
	}
	
	static function getRepairMethod($repair_matrix_id)
	{
		$sort = (Config::get('app.locale') == 'en') ? 'name_en' : 'name_vn';
		$default = mstRepairMethod::get()->sortBy($sort);
		$array_default = array();
		foreach ($default as $p)
		{
			$array_default['matrix_'.$p->id] = array(
				'name' => (Config::get('app.locale') == 'en') ? $p->name_en : $p->name_vn,
				'value' => $p->id,
				'selected' => FALSE
			);
		}
		
		if ($repair_matrix_id != -1)
		{
			$repair_method = $data = tblRepairMatrixMethod::where('repair_matrix_id', $repair_matrix_id)->get();
			$array_repair_method = array();
			foreach ($repair_method as $p)
			{
				$array_repair_method['matrix_'.$p->repair_method_id] = array(
					'name' => (Config::get('app.locale') == 'en') ? $p->repairMethod->name_en : $p->repairMethod->name_vn,
					'value' => $p->repair_method_id,
					'selected' => FALSE
				);
			}
			
			foreach ($array_default as $key => $value)
			{
				if (in_array($value, $array_repair_method))
				{
					$array_default[$key]['selected'] = TRUE;
				}
			}
		}
		
		return $array_default;
	}
}
