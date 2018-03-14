<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class tblRepairMatrixCellValue extends Model
{
    protected $table= 'tblRepair_matrix_cell_values';
	
	public function parameter()
	{
		return $this->beLongsTo('App\Models\tblParameter', 'parameter_id');
	}
	
	public function repairMethod()
	{
		return $this->beLongsTo('App\Models\mstRepairMethod', 'value');
	}

	public function cellCrack()
	{
		return $this->beLongsTo('App\Models\tblRepairMatrixCell')->where('parameter_id', 'crack_ratio');
	}

	public function rutValue()
	{
		return $this->beLongsTo('App\Models\tblRepairMatrixCell')->where('parameter_id', 'rutting_depth');
	}
	
	public function repairMethodValue()
	{
		return $this->beLongsTo('App\Models\tblRepairMatrixCell')->where('parameter_id', 'repair_method');
	}
	
	public function roadTypeValue()
	{
		return $this->beLongsTo('App\Models\tblRepairMatrixCell')->where('parameter_id', 'road_type');
	}
	
	public function roadClassValue()
	{
		return $this->beLongsTo('App\Models\tblRepairMatrixCell')->where('parameter_id', 'road_class');
	}

	public function surfaceValue()
	{
		return $this->beLongsTo('App\Models\tblRepairMatrixCell')->where('parameter_id', 'surface');
	}
	
}
