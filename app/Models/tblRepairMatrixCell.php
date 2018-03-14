<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use DB, Auth, Helper;

class tblRepairMatrixCell extends Model
{
    protected $table = 'tblRepair_matrix_cell';
	
	public function crackValue()
	{
		return $this->hasOne('App\Models\tblRepairMatrixCellValue', 'repair_matrix_cell_id')->where('parameter_id', 'crack_ratio');
	}

	public function rutValue()
	{
		return $this->hasOne('App\Models\tblRepairMatrixCellValue', 'repair_matrix_cell_id')->where('parameter_id', 'rutting_depth');
	}
	
	public function repairMethodValue()
	{
		return $this->hasOne('App\Models\tblRepairMatrixCellValue', 'repair_matrix_cell_id')->where('parameter_id', 'repair_method');
	}
	
	public function roadTypeValue()
	{
		return $this->hasOne('App\Models\tblRepairMatrixCellValue', 'repair_matrix_cell_id')->where('parameter_id', 'road_type');
	}
	
	public function roadClassValue()
	{
		return $this->hasOne('App\Models\tblRepairMatrixCellValue', 'repair_matrix_cell_id')->where('parameter_id', 'road_class');
	}

	public function surfaceValue()
	{
		return $this->hasOne('App\Models\tblRepairMatrixCellValue', 'repair_matrix_cell_id')->where('parameter_id', 'surface');
	}
	
	// public function parameters()
	// {
	// 	return $this->belongsToMany('App\Models\tblParameter', 'tblRepair_matrix_cell_values', 'repair_matrix_cell_id', 'parameter_id');
	// }
	
	public static function boot() {
        parent::boot();

        // tblRepairMatrixCell::deleting(function($cell) {
        //      $cell->repairMatrixCellValue()->delete();
        // });
    }
	
	static function getInfoCell($default_repair_matrix_id, $type, $crack_from, $crack_to, $rut_from = FALSE, $rut_to = FALSE, $user_id = -1)
	{
		if ($type == 'as')
		{
			$type = 1;
		}
		else if ($type == 'bst')
		{
			$type = 2;
		}
		else if ($type == 'cc')
		{
			$type = 3;
		}
		$result = tblRepairMatrixCell::where('repair_matrix_id', $default_repair_matrix_id)
									->where('type', $type)
									->where('crack_from', $crack_from)
									->where('crack_to', $crack_to);
											
		if ($rut_from !== FALSE && $rut_to !== FALSE)
		{
			$result = $result->where('rut_from', $rut_from)->where('rut_to', $rut_to);
		}
		
		if ($user_id != -1)
		{
			$result = $result->where('user_id', $user_id);
		}
		else
		{
			$result = $result->whereNull('user_id');
		}
		
		$result = $result->orderBy('created_at', 'desc')
						->first();
		return $result;
	}

	function saveRelation($repair_method, $road_type, $road_class, $surface, $row, $col)
	{
		$crack;
		$rut;
		// if ($surface == 3)
		// {
		// 	$crack = $col;
		// 	$rut = $row;
		// }
		// else
		// {
			$crack = $row;
			$rut = $col;
		// }
		
		$relation = new tblRepairMatrixCellValue;
		$relation->parameter_id = 'crack_ratio';
		$relation->value = $crack;

		$this->crackValue()->save($relation);

		$relation = new tblRepairMatrixCellValue;
		$relation->parameter_id = 'rutting_depth';
		$relation->value = $rut;
		$this->rutValue()->save($relation);

		$relation = new tblRepairMatrixCellValue;
		$relation->parameter_id = 'repair_method';
		$relation->value = $repair_method;
		$this->repairMethodValue()->save($relation);

		$relation = new tblRepairMatrixCellValue;
		$relation->parameter_id = 'road_type';
		$relation->value = $road_type;
		$this->roadTypeValue()->save($relation);

		$relation = new tblRepairMatrixCellValue;
		$relation->parameter_id = 'road_class';
		$relation->value = $road_class;
		$this->roadClassValue()->save($relation);

		$relation = new tblRepairMatrixCellValue;
		$relation->parameter_id = 'surface';
		$relation->value = $surface;
		$this->surfaceValue()->save($relation);
	}
}
