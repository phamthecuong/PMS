<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

use Ramsey\Uuid\Uuid;
use App\Traits\UuidForKey;
use App, DB, Config, Helper;

class tblDeterioration extends Model
{
	use SoftDeletes;
	protected $dates = ['deleted_at'];

	use UuidForKey;
	
    protected $table = 'tblDeterioration';

    public function organizations()
    {
    	return $this->belongsTo('App\Models\tblOrganization', 'organization_id');
    }
	
	// public function organizations()
	// {
		// return $this->belongsToMany('App\Models\tblOrganization', 'tblDeterioration_organization', 'deterioration_id', 'organization_id');
	// }
	
	/**
	 * get info Deterionation complete (status == 1)
	 * 
	 * @param  $has_filed  string  filed need take
	 * @param  $order   string  type sort
	 * @param  $has_all  string  title with value - 1 if select option
	 * @return array  Returns array info detrionation 
	 */
	static function allToComplete($has_filed = 'id', $order = 'asc', $has_all = FALSE)
	{
		$data = tblDeterioration::where('dataset_flg', 1);
		if ($has_filed !== 'id')
		{
			$data = $data->orderBy($has_filed, $order)->groupBy($has_filed);
		}
		$data = $data->get();
		
		$dataset = array();
		if ($has_all !== FALSE)
		{
			// $dataset+= array(
			// 	-1 => $has_all,
			// );
			$dataset[] = [
				'name' => $has_all,
				'value' => -1
			];
		}
		foreach ($data as $r)
		{
			// $dataset+= array(
			// 	$r->id => $r->$has_filed,
			// );
			$dataset[] = [
				'name' => $r->$has_filed,
				'value' => $r->id
			];
		}
		return $dataset;
	}
	
	// static function getYearDeterioration($session_id)
	// {
	// 	$year = tblDeterioration::where('id', $session_id)->first();
	// 	return $year->year_of_dataset;
	// }
	
	static function getRegionByYearOfDataset($year)
	{
		$regions = tblDeterioration::where('year_of_dataset', $year)
									->where('dataset_flg', 1)
									->groupBy('organization_id')
									->get();
		$result = array();
		foreach ($regions as $region)
		{
			if (isset($region->organizations))
			{
				$result[] = array(
					'value' => $region->organizations->id,
					'name' => (Config::get('app.locale') == 'en') ? $region->organizations->name_en : $region->organizations->name_vn,
				);
			}
			else
			{
				continue;
			}
		}
		
		return $result;
	}

	static public function AllToOption()
	{
		$deterioration = App\Models\tblDeterioration::get();
		foreach($deterioration as $row)
		{
			 $array[] = ['name'=> $row->year_of_dataset];
		}
		return $array;
	}

	/**
	 * convert json from condition_rank in tblDeterioration to array
	 * @param       json   $json   json from condition_rank
	 * @return      array    Returns array criterion Crack, rut 
	 */
	static function getDataRepairMatrix($json)
	{
		$data = Helper::convertJsonConditionRank($json);
		$array_rut = array();
		$array_crack = array();
		$array_iri = array();
		foreach ($data as $key => $value)
		{
			if ($key == 'rut')
			{
				foreach ($value as $k => $v)
				{
					// H.ANH  2016.12.29  add the case when start value is not 0
					if ($v['from'] == 0 && $v['to'] == 0)
					// if ($v['from'] == $v['to'])
					// end modification
					{
						$array_rut[] = "Rut=" . $v['from'];
					}
					else if (isset($v['from']) && isset($v['to']) && $v['from'] < $v['to'])
					{
						$array_rut[] = ($v['from'] != 0) ? $v['from']."&le;Rut&lt;".$v['to'] : $v['from']."&lt;Rut&lt;".$v['to'];
					}
					else if (isset($v['from']) && (!isset($v['to']) || $v['to'] == 0))
					{
						$array_rut[] = $v['from']."&le;Rut";
					}
					else if (!isset($v['from']) && isset($v['to']))
					{
						$array_rut[] = "Rut&lt;".$v['to'];
					}					
				}
			}
			else if ($key == 'crack')
			{
				foreach ($value as $k => $v)
				{
					// H.ANH  2016.12.29  add the case when start value is not 0
					if ($v['from'] == 0 && $v['to'] == 0)
					// if ($v['from'] == $v['to'])
					// end modification
					{
						$array_crack[] = "Crack=" . $v['from'];
					}
					else if (isset($v['from']) && isset($v['to']) && $v['from'] < $v['to'])
					{
						$array_crack[] = ($v['from'] != 0) ? $v['from']."&le;C&lt;".$v['to'] : $v['from']."&lt;C&lt;".$v['to'];
					}
					else if (isset($v['from']) && (!isset($v['to']) || $v['to'] == 0))
					{
						$array_crack[] = $v['from']."&le;C";
					}
					else if (!isset($v['from']) && isset($v['to']))
					{
						$array_crack[] = "C&lt;".$v['to'];
					}			
				}
			}
			else if ($key == 'iri')
			{
				foreach ($value as $k => $v)
				{
					// H.ANH  2016.12.29  add the case when start value is not 0
					if ($v['from'] == 0 && $v['to'] == 0)
					// if ($v['from'] == $v['to'])
					// end modification
					{
						$array_iri[] = "IRI=" . $v['from'];
					}
					else if (isset($v['from']) && isset($v['to']) && $v['from'] < $v['to'])
					{
						$array_iri[] = ($v['from'] != 0) ? $v['from']."&le;I&lt;".$v['to'] : $v['from']."&lt;I&lt;".$v['to'];
					}
					else if (isset($v['from']) && (!isset($v['to']) || $v['to'] == 0))
					{
						$array_iri[] = $v['from']."&le;I";
					}
					else if (!isset($v['from']) && isset($v['to']))
					{
						$array_iri[] = "I&lt;".$v['to'];
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
