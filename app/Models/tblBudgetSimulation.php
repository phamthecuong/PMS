<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Traits\UuidForKey;
use App, DB, Config, Helper, Auth;
use App\Models\tblConditionRank;
use App\Models\tblRepairMatrixCell;

class tblBudgetSimulation extends Model
{
	use UuidForKey;
	
    protected $table  = 'tblBudget_simulation';

    protected $appends = ['organization_name', 'route_name'];
	
	public function budgetSimulationRoads()
	{
		return $this->hasMany('App\Models\tblBudgetSimulationRoad', 'budget_simulation_id');
	}
	
	public function deterioration()
	{
		return $this->belongsTo('App\Models\tblDeterioration', 'deterioration_id');
	}
	
	public function roads()
	{
		return $this->belongsToMany('App\Models\tblBranch', 'tblBudget_simulation_road', 'budget_simulation_id', 'road_id');
	}
	
	public function organizations()
	{
		return $this->belongsToMany('App\Models\tblOrganization', 'tblBudget_simulation_organization', 'budget_simulation_id', 'organization_id');
	}
	
	public function repairMatrix()
	{
		return $this->belongsTo('App\Models\tblRepairMatrix', 'default_repair_matrix_id');
	}
	
	public function getInfoOrganization($session_id = -1)
	{
		$text = '';
		$organizations;
		if ($session_id != -1)
		{
			$organizations = tblBudgetSimulation::find($session_id)->organizations;
		}
		else
		{
			$organizations = $this->organizations;	
		}
		
		foreach ($organizations as $p)
		{
			$text .= (Config::get('app.locale') == 'en') ? ', '.$p->name_en : ', '.$p->name_vn;	
		}
		$text = ltrim($text,' ,');
		
		return $text;
	}
	
	public function getInfoRoad($session_id = -1)
	{
		$text = '';
		$roads;
		if ($session_id != -1)
		{
			$roads = tblBudgetSimulation::find($session_id)->roads;
		}
		else
		{
			$roads = $this->roads;
		}
		
		foreach ($roads as $p)
		{
			$text .= (Config::get('app.locale') == 'en') ? ', '.$p->name_en : ', '.$p->name_vn;
		}
		$text = ltrim($text,' ,');
		
		return $text;
	}
	
	static function getDataRepairMatrix($session_id, $type = '')
	{
		$user_id = Auth::user()->id;
		$default = tblBudgetSimulation::find($session_id);
		$json_not_default = $default->deterioration->condition_rank;
		$array_not_default = tblDeterioration::getDataRepairMatrix($json_not_default);
		
		// create thead
		array_unshift($array_not_default['rut'], NULL);
		$table_head = '<tr>';
		if ($type == 'cc')
		{
			$table_head = '<tr>';
			$table_body = '<tr>';
			for ($i = 0; $i < count($array_not_default['crack']); $i++)
			{
				$value = $array_not_default['crack'][$i];
				$table_head .= '<th>'.$value.'</th>';
				$table_body .= '<td></td>';
			}
		}
		else
		{
			for ($i = 0; $i < count($array_not_default['rut']); $i++)
			{
				$value = $array_not_default['rut'][$i];
				$table_head .= '<th>'.$value.'</th>';
			}
		}
		$table_head .= '</tr>';
		
		if (isset($default->default_repair_matrix_id))
		{
			$check = tblRepairMatrixCell::where('user_id', $user_id)->where('repair_matrix_id', $default->default_repair_matrix_id)->count();
			$array = Helper::convertJsonConditionRank($json_not_default);
			$matrix_cell_type = 1;
			if ($type == 'as')
			{
				$matrix_cell_type = 1;
			}
			else if ($type == 'bst')
			{
				$matrix_cell_type = 2;
			}
			else if ($type == 'cc')
			{
				$matrix_cell_type = 3;
			}
		}
		
		$table_body = '';
		if ($type != 'cc')
		{
			if (!isset($default->default_repair_matrix_id))
			{
				$body = array();
				$i = 0;
				
				foreach ($array_not_default['crack'] as $key => $value)
				{
					$body[$i] = array($value);
					$body[$i] = array_pad($body[$i], count($array_not_default['rut']), NULl);
					$i++;
				}
				
				for ($i = 0; $i < count($body); $i++)
				{
					$table_body .= '<tr>';
					for ($j = 0; $j < count($body[$i]); $j++)
					{
						$value = $body[$i][$j]; 
						if ($j == 0)
						{
							$table_body .= '<th>'.$value.'</th>';
						}
						else
						{
							$table_body .= '<td></td>';
						}	
					}
					// $table_body .= '</tr>';
				}
			}
			else
			{
				$body = array();
				for ($i = 0; $i < count($array['crack']); $i++)
				{
					$data_crack = $array['crack'][$i];
					if ($data_crack['from'] == NULL)
					{
						$data_crack['from'] = 0;
					}
					else if ($data_crack['to'] == NULL)
					{
						$data_crack['to'] = 0;
					}
					$table_body .= '<tr>';
					
					$text = '';
					// H.ANH  2016.12.29  add the case when start value is not 0
					if ($data_crack['from'] == 0 && $data_crack['to'] == 0)
					// if ($data_crack['from'] == $data_crack['to'])
					// end modification
					{
						$text = "Crack=" . $data_crack['from'];
					}
					else if (isset($data_crack['from']) && isset($data_crack['to']) && $data_crack['from'] <= $data_crack['to'])
					{
						$text = ($data_crack['from'] != 0) ? $data_crack['from']."&le;C&lt;".$data_crack['to'] : $data_crack['from']."&lt;C&lt;".$data_crack['to'];
					}
					else if (isset($data_crack['from']) && (!isset($data_crack['to']) || $data_crack['to'] == 0))
					{
						$text = $data_crack['from']."&le;C";
					}
					else if (!isset($data_crack['from']) && isset($data_crack['to']))
					{
						$text = "C&lt;".$data_crack['to'];
					}
					
					for ($j = -1; $j < count($array['rut']); $j++)
					{
						if ($j == -1)
						{
							$table_body .= '<th>'.$text.'</th>';
							continue;
						}
						
						$data_rut = $array['rut'][$j];
						if ($data_rut['from'] == NULL)
						{
							$data_rut['from'] = 0;
						}
						else if ($data_rut['to'] == NULL)
						{
							$data_rut['to'] = 0;
						}
						
						$cell_default_for_user;
						if ($check > 0)
						{
							$cell_default_for_user = tblRepairMatrixCell::getInfoCell($default->default_repair_matrix_id, $matrix_cell_type, $data_crack['from'], $data_crack['to'], $data_rut['from'], $data_rut['to'], $user_id);
						}
						else
						{
							$cell_default_for_user = tblRepairMatrixCell::getInfoCell($default->default_repair_matrix_id, $matrix_cell_type, $data_crack['from'], $data_crack['to'], $data_rut['from'], $data_rut['to']);
						}

						if (isset($cell_default_for_user->id))
						{
							if (isset($cell_default_for_user->repair_method_id))
							{
								$data_set = mstRepairMethod::find($cell_default_for_user->repair_method_id);
								$color = $data_set->color;
								$method_id = $data_set->id;
								$table_body .= "<td style='background-color: $color;' class='$method_id' 
												data-rut-from='$data_rut[from]' data-rut-to='$data_rut[to]' 
												data-crack-from='$data_crack[from]' data-crack-to='$data_crack[to]'></td>";
							}
							else
							{
								$table_body .= "<td class='0' data-rut-from='$data_rut[from]' data-rut-to='$data_rut[to]' data-crack-from='$data_crack[from]' data-crack-to='$data_crack[to]'></td>";
							}
						}
						else
						{
							$table_body .= "<td class='0' data-rut-from='$data_rut[from]' data-rut-to='$data_rut[to]' data-crack-from='$data_crack[from]' data-crack-to='$data_crack[to]'></td>";
						}
					}
				}
			}
		} 
		else
		{
			$table_body = '<tr>';
			if (!isset($default->default_repair_matrix_id))
			{
				for ($i = 0; $i < count($array_not_default['crack']); $i++)
				{
					$value = $array_not_default['crack'][$i];
					$table_body .= '<td></td>';
				}
			}
			else
			{
				for ($i = 0; $i < count($array['crack']); $i++)
				{
					$value = $array['crack'][$i];
					if ($value['from'] == NULL)
					{
						$value['from'] == 0;
					}
					else if ($value['to'] == NULL)
					{
						$value['to'] = 0;
					}
					
					$cell_default_for_user;
					if ($check > 0)
					{
						$cell_default_for_user = tblRepairMatrixCell::getInfoCell($default->default_repair_matrix_id, $matrix_cell_type, $value['from'], $value['to'], FALSE, FALSE, $user_id);
					}
					else
					{
						$cell_default_for_user = tblRepairMatrixCell::getInfoCell($default->default_repair_matrix_id, $matrix_cell_type, $value['from'], $value['to'], FALSE, FALSE);
					}									
					
					if (isset($cell_default_for_user->id))
					{
						if (isset($cell_default_for_user->repair_method_id))
						{
							$data_set = mstRepairMethod::find($cell_default_for_user->repair_method_id);
							$color = $data_set->color;
							$method_id = $data_set->id;
							$table_body .= "<td style='background-color: $color;' class='$method_id' data-crack-from='$value[from]' data-crack-to='$value[to]'></td>";
						}
						else
						{
							$table_body .= "<td class='0' data-crack-from='$value[from]' data-crack-to='$value[to]'></td>";
						}
					}
					else
					{
						$table_body .= "<td class='0' data-crack-from='$value[from]' data-crack-to='$value[to]'></td>";
					}
				}
			}
			
			$table_body .= '</tr>';
		}
		
		return array(
			'head' => $table_head,
			'body' => $table_body
		);
	}
	
	static function convertHtml($source)
	{
		$array = Helper::getInfoRepairMethod($source);
		$result = "<div class='col-md-8 row'>";
		$result .= "<div class='col-md-2' onclick="."changeDefaultColor('#ffffff'".','. "'0');".">";
		$result .= "<div class='repair_method' style='border: 1px solid #ddd; background: #fff;'></div>";
		$result .= "<p style='text-align: center'>".trans('budget.not_apply')."</p>";
		$result .= "</div>";
		for ($i = 0; $i < count($array); $i++)
		{
			$data = $array[$i];
			$result .= "<div class='col-md-2' onclick="."changeDefaultColor('$data[color]'".','. "'$data[id]');".">";
			$result .= "<div class='repair_method' style='border: 1px solid $data[color]; background: $data[color];'></div>";
			$result .= "<p style='text-align: center'>$data[name]</p>";
			$result .= "</div>";
		}
		$result .= "</div>";
		
		return $result;
	}
	
	public function getAllRepiarMethod()
	{
		return  $this->repairMatrix->repairMethods;	
	}
	
	public function getAllRepairMethodToArray($has_all = FALSE, $get_field = 'name')
	{
		$repair_methods = $this->getAllRepiarMethod();
		
		$result = array();
		if ($has_all !== FALSE)
		{
			$result+= array(
				'' => $has_all,
			);
		}
		
		$name = (Config::get('app.locale')) ? 'name_en' : 'name_vn';
		
		foreach ($repair_methods as $method)
		{
			if ($get_field == 'name')
			{
				$result += array(
					$method->id => $method->$name,
				);
			}
			else
			{
				$result += array(
					$method->id => array(
						'name' => $method->$name,
						'color' => $method->$get_field
					)
				);
			}
		}
		
		return $result;
	}

	public function getCurrentRisk()
	{
		$source = public_path('application/process/budget_simulation/'.$this->id.'/output2/risk.csv');
		if (file_exists($source))
		{
			$file = fopen("$source","r");

			$i = 1;		
			$current_risk = -1;
			while(!feof($file) && $i < 3)
			{
				$data = fgetcsv($file);
				$current_risk = trim($data[1]);
				$i++;
			}
			
			fclose($file);
			return $current_risk*100;	
		}
		else
		{
			return 0;
		}
	}

	function getOrganizationNameAttribute()
	{
		$lang = \App::isLocale('en') ? 'en' : 'vn';
		return implode(', ', $this->organizations->pluck("name_{$lang}")->toArray());
	}

	function getRouteNameAttribute()
	{
		$lang = \App::isLocale('en') ? 'en' : 'vn';
		return implode(', ', $this->roads->pluck("name_{$lang}")->toArray());
	}
}
