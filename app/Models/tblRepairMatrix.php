<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use DB, App, Config, Helper;

class tblRepairMatrix extends Model
{
	use SoftDeletes;
	
    protected $table = 'tblRepair_matrix';
	protected $dates = array('created_at', 'deleted_at', 'updated_at');
	
	public function repairMethods()
	{
		return $this->belongsToMany('App\Models\mstRepairMethod', 'tblRepair_matrix_method', 'repair_matrix_id', 'repair_method_id');
	}

	public function userUpdate()
    {
        return $this->belongsTo('App\Models\user', 'updated_by' , 'id');
    }
	
	static function allToOption($has_all = FALSE, $filed_sort = '',$sort = '')
	{
		if ($sort == 'asc')
		{
			$data = tblRepairMatrix::get()->sortBy($filed_sort);
		}
		else if ($sort == 'desc')
		{
			$data = tblRepairMatrix::get()->sortByDesc($filed_sort);			
		}
		else
		{
			$data = tblRepairMatrix::get();	
		}
		
		$dataset = array();
		if ($has_all !== FALSE)
		{
			$dataset+= array(
				-1 => $has_all,
			);
		}
		foreach ($data as $r)
		{
			$dataset+= array(
				$r->id => $r->name,
			);
		}
		
		return $dataset;
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
			$repair_method = $data = tblRepairMatrix::find($repair_matrix_id)->repairMethods;
			$array_repair_method = array();
			foreach ($repair_method as $p)
			{
				$array_repair_method['matrix_'.$p->id] = array(
					'name' => (Config::get('app.locale') == 'en') ? $p->name_en : $p->name_vn,
					'value' => $p->id,
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
	
	/**
	 * convert json from condition_rank in tblDeterioration to array
	 * @param       json   	 $json   json from condition_rank
	 * @param       boolen   $html_unescape   for chart JS
	 * @return      array    Returns array criterion Crack, rut 
	 */
	static function getCrackRut($json, $html_unescape = FALSE)
	{
		$data = Helper::convertJsonConditionRank($json);
		$array_rut = array();
		$array_crack = array();
		$array_iri = array();
		
		$le = "&le;";
		$lt = "&lt;";
		
		if ($html_unescape)
		{
			$le = '≤';
			$lt = '<';
		}
		
		foreach ($data as $key => $value)
		{
			if ($key == 'rut')
			{
				foreach ($value as $k => $v)
				{
					if ($v['from'] == 0 && $v['to'] == 0)
					{
						$array_rut[] = "Rut=" . $v['from'];
					}
					else if (isset($v['from']) && isset($v['to']) && $v['from'] < $v['to'])
					{
						$array_rut[] = $v['from'].$le."Rut".$lt.$v['to'];
					}
					else if (isset($v['from']) && (!isset($v['to']) || $v['to'] == 0))
					{
						$array_rut[] = $v['from'].$le."Rut";
					}
					else if (!isset($v['from']) && isset($v['to']))
					{
						$array_rut[] = "Rut".$lt.$v['to'];
					}					
				}
			}
			else if ($key == 'crack')
			{
				foreach ($value as $k => $v)
				{
					if ($v['from'] == 0 && $v['to'] == 0)
					{
						$array_crack[] = "Crack=" . $v['from'];
					}
					else if (isset($v['from']) && isset($v['to']) && $v['from'] < $v['to'])
					{
						$array_crack[] = $v['from'].$le."C".$lt.$v['to'];
					}
					else if (isset($v['from']) && (!isset($v['to']) || $v['to'] == 0))
					{
						$array_crack[] = $v['from'].$le."C";
					}
					else if (!isset($v['from']) && isset($v['to']))
					{
						$array_crack[] = "C".$lt.$v['to'];
					}			
				}
			}
			else if ($key == 'iri')
			{
				foreach ($value as $k => $v)
				{
					if ($v['from'] == 0 && $v['to'] == 0)
					{
						$array_iri[] = "IRI=" . $v['from'];
					}
					else if (isset($v['from']) && isset($v['to']) && $v['from'] < $v['to'])
					{
						$array_iri[] = $v['from'].$le."I".$lt.$v['to'];
					}
					else if (isset($v['from']) && (!isset($v['to']) || $v['to'] == 0))
					{
						$array_iri[] = $v['from'].$le."I";
					}
					else if (!isset($v['from']) && isset($v['to']))
					{
						$array_iri[] = "I".$lt.$v['to'];
					}			
				}
			}
		}
		return array(
			'rut' => $array_rut,
			'crack' => $array_crack,
			'iri' => $array_iri,
		);
	}
}
