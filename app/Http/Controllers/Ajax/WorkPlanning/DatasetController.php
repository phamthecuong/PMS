<?php
namespace App\Http\Controllers\Ajax\WorkPlanning;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\tblDeterioration;
use App\Models\tblBudgetSimulation;
use App\Models\tblBudgetSimulationRoad;
use App\Models\tblBudgetSimulationOrganization;
use App\Models\tblBranch;
use App\Models\tblRepairMatrixCell;
use App\Models\tblRepairMatrix;
use App\Models\tblRepairMatrixCellValue;
use App\Models\mstRoadCategory;
use App\Models\mstRoadClass;
use App\Models\mstRepairMethod;
use App\Models\tblWorkPlanning;
use App\Models\tblWorkPlanningOrganization;
use App\Models\tblOrganization;
use App\Models\tblSegmentHistory;
use App\Models\tblRCategory;
use App\Models\tblRClassification;
use App\Models\tblConditionRank;
use Auth, Helper, Session, Config, Excel, DB;
use Yajra\Datatables\Facades\Datatables;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Carbon\Carbon;
use App\Models\mstSurface;
use App\Models\User;
use App\Models\mstMethodUnit;
use App\Models\tblPlannedSection;

class DatasetController extends Controller
{
	protected $branches = [];

	protected $road_classes = [];

	protected $repair_categories = [];

	protected $repair_classifications = [];

	protected $rmb = [];

	protected $sb = [];

	function __construct()
	{
		$lang = (\Session::get('locale') == 'en') ? 'en' : 'vn';
		
		$records = tblBranch::all();
		foreach ($records as $r)
		{
			$this->branches[intval($r->road_number)][intval($r->branch_number)][intval($r->road_number_supplement)][intval($r->road_category)] = $r->{"name_{$lang}"};
		}

		$records = mstRoadClass::all();
		foreach ($records as $r)
		{
			$this->road_classes[$r->code_id] = $r->{"name_{$lang}"};
		}

		$records = tblRCategory::get();
        foreach ($records as $r) 
        {
            $this->repair_categories[$r->id] = $r->code;
        }

        $records = tblRClassification::get();
        foreach ($records as $r) 
        {
            $this->repair_classifications[$r->id] = $r->code;
        }

        $records = tblOrganization::where('level', 3)->get();
        foreach ($records as $r)
        {
        	$this->sb[$r->id][] = $r->id;
        	$this->sb[$r->id][] = $r->organization_name;
        	$this->rmb[$r->id][] = @$r->parent_id;
        	$this->rmb[$r->id][] = @$r->rmb()->first()->organization_name;
        }
	}

 	public function getListRegion(Request $request)
	{
		$region = tblDeterioration::getRegionByYearOfDataset($request->year);
		$organization_id = Auth::user()->organization_id;
		$level = @tblOrganization::find($organization_id)->level;
		if ($level == 2)
		{
			$region_data = [];
			foreach ($region as $row) 
			{
				if ($row['value'] == $organization_id)
				{
					$region_data[] = $row;
				}
			}

			return response()->json($region_data);
		}
		
		return response()->json($region);
	}
	
	public function createInit(Request $request)
	{
		\DB::beginTransaction();
		try
		{
			$user_id = Auth::user()->id;
		
			$work_planning = new tblWorkPlanning;
			$work_planning->year = $request->year;
			$work_planning->created_by = $user_id;
			$work_planning->save();
			
			foreach ($request->list_region as $key => $value)
			{
				$organization = new tblWorkPlanningOrganization;
				$organization->work_planning_id = $work_planning->id;
				$organization->organization_id = $value;
				$organization->created_by = $user_id;
				$organization->save();
			}

			// $sample = file("../public/application/core/work_planning/Sample.csv");
		
			// foreach ($sample as $row)
			// {
			// 	$input[] = explode(',', $row);
			// }
			$input = $this->_importModuleDataset($request->year, $request->list_region);

			// $input = Helper::trim($input);
			if (!file_exists("../public/application/process/work_planning/" . $work_planning->id . "/data")) 
			{
			    mkdir("../public/application/process/work_planning/" . $work_planning->id . "/data", 0777, true);
			}
			// create file input
			$open = fopen("../public/application/process/work_planning/" . $work_planning->id . "/data/input.csv", 'w');
			foreach ($input as $row)
			{				
			    fputcsv($open, $row);
			}	
			fclose($open);

			\DB::commit();
		}
		catch (\Exception $e)
		{
			dd($e->getMessage());
			\DB::rollBack();
		}


		return response()->json(array(
			'code' => 200,
			'session_id' => $work_planning->id
		));	
	}

	/**
	 * Work Plan Error List
	 * 1: road class invalid
	 * 2: cracking invalid
	 * 3: rutting invalid
	 * 4: iri invalid
	 * 5: pavement type invalid
	 */
	private function _importModuleDataset($pms_year, $list_rmb)
	{
		$road_classes = $this->road_classes;
		$dataset = [];
		$segment_by_year = $this->_getListSegmentByRegionAndYear($pms_year, $list_rmb);
		\App\Models\tblPMSDatasetInfo::whereIn('case', [0, 1, 2, 3, 4])
			->has('sectioning')
			->with('sectioning')
			->where('year_of_dataset', $pms_year)
			->whereNotNull('latest_condition_year')
			->whereNotNull('latest_condition_month')
			->whereNotNull('latest_pavement_type')
			->whereNotNull('latest_cracking_ratio')
			->whereNotNull('latest_rutting_max')
			->whereNotNull('latest_IRI')
			// H.ANH  20170606  temporarily change to sub-bureau condition
			// ->whereIn('segment_id', $segment_by_year)
			// end modification
			->whereIn('sb_id', $segment_by_year)
			->chunk(3000, function($records) use(&$dataset, $road_classes) {
					foreach ($records as $rec) 
					{
						$error = 0;
						if (!isset($road_classes[$rec->road_class_id]))
						{
							$error = 1;
						}
						if ($rec->latest_cracking_ratio < 0 || $rec->latest_cracking_ratio > 100)
						{
							$error = 2;
						}
						if ($rec->latest_rutting_max < 0)
						{
							$error = 3;
						}
						if ($rec->latest_IRI < 0)
						{
							$error = 4;
						}
						if (!in_array($rec->latest_pavement_type, ['AC', 'BST', 'CC']))
						// if (!in_array($rec->latest_pavement_type, ['AC', 'BST']))
						{
							$error = 5;
						}
						$dataset[] = [
							$error,
							$rec->section_id2,
							$rec->road_class_id,
							$rec->construct_year,
							$rec->sectioning->km_from,
							$rec->sectioning->m_from,
							$rec->sectioning->km_to,
							$rec->sectioning->m_to,
							$rec->section_length,
							$rec->number_of_lane,
							$rec->sectioning->direction,
							$rec->sb_id,
							$rec->pavement_width,
							$rec->pavement_thickness,
							implode('/', [$rec->completion_year, $rec->completion_month]),
							$rec->r_category_code,
							$rec->r_classification_code,
							$rec->traffic_survey_year,
							$rec->total_traffic_volume,
							$rec->heavy_traffic,
							implode('/', [$rec->latest_condition_year, $rec->latest_condition_month]),
							$rec->sectioning->lane_pos_no,
							$rec->latest_pavement_type,
							$rec->latest_cracking,
							$rec->latest_patching,
							$rec->latest_pothole,
							$rec->latest_cracking_ratio,
							$rec->latest_rutting_max,
							$rec->latest_rutting_ave,
							$rec->latest_IRI,
							$rec->latest_MCI
						];
					}
				});
		return $dataset;
	}

	private function _getListSegmentByRegionAndYear($pms_year, $list_rmb)
	{
		// H.ANH  20170606  temporarily change 
		// return tblSegmentHistory::whereRaw("(updated_at is null or YEAR(updated_at) <= $pms_year)")
		// 	->whereHas('latestSB', function($q) use($pms_year, $list_rmb) {
  //                   $q->whereRaw("(updated_at is null or YEAR(updated_at) <= $pms_year)")
  //                   	->whereIn('parent_id', $list_rmb);
  //               })
  //           ->groupBy('segment_id')
  //           ->select('segment_id')
  //           ->pluck('segment_id')
  //           ->toArray();
		return tblOrganization::where('level', 3)
			->whereIn('parent_id', $list_rmb)
			->select('id')
			->pluck('id')
			->toArray();
	}

	private function _getCsvData($session_id, $type, $list)
	{ 
		$input_file = 'input';
		if ($type == 2)
		{
			$input_file = 'input_forecast';
		}
		else if (in_array($type, [3, 11]))
		//else if ($type == 3)
		{
			$input_file = 'input_method';
		}
		else if (in_array($type, [4, 5, 6, 7, 8, 9]))
		{
			if ($list == 0)
	        {
	        	$input_file = "input_final";
	        }
	        else if ($list == 1)
	        {
	        	$input_file = "input_proposal";
	        }
	        else
	        {
	        	$input_file = "input_planned";
	        }
		}

		$unit = [];
		$method_final = [];
		if (in_array($type, [4, 5, 6, 7, 8, 9, 11]))
		{
	        $records = \App\Models\mstMethodUnit::get();
	        foreach ($records as $r) 
	        {
	            $unit[$r->id] = $r->code_name;
	        }
			$method_final = $this->_getMethodList($session_id);
		}
		//dd($method_final);
		$branches = $this->branches;
		$road_classes = $this->road_classes;
		$repair_categories = $this->repair_categories;
		$repair_classifications = $this->repair_classifications;
		$rmb = $this->rmb;
		$sb = $this->sb;

		$source = "application/process/work_planning/" . $session_id . "/data/" . $input_file . ".csv";
		$dataset = array();
		$key = -1;

		Helper::getRowDataChunk($source, 5000, function($chunk, &$handle, $iteration) use(&$dataset, &$key, $branches, $road_classes, $repair_categories, $repair_classifications, $type, $unit, $method_final, $rmb, $sb) {
            $i = explode(',', $chunk);
            
            $key++;
            for ($j = 1; $i <= count($i) - 1; $j++)
            {
            	$tmp = str_replace(array("\n", "\r", "\r\n", "\n\r"), '', $dataset[$j]);
                $i[$j] = trim($tmp, '"');
            }
            if (count($i) < 5)
            {
            	return false;
            }
            if (
            	(in_array($type, [0, 2, 3, 4, 5, 6, 7, 8, 9]) && $i[0] != 0) ||
            	($type == 1 && $i[0] == 0)
            )
            {	
            	return false;
            }
	    	
	    	$road_category = substr($i[1], 2, 1);
	    	$road_number = substr($i[1], 3, 3);
    		$road_number_supplement = substr($i[1], 6, 3);
    		$branch_number = substr($i[1], 9, 2);
    		$route_name = $branches[intval($road_number)][intval($branch_number)][intval($road_number_supplement)][intval($road_category)];

    		$tmp = [
    			'id' => $key,
    			'section_id' => $i[1],
    			'route_no' => $road_number,    			
    			'route_name' => $route_name,
    			'branch_number' => $branch_number,
    			'rmb' => @$rmb[$i[11]][1],
    			'rmb_id' => @$rmb[$i[11]][0],
    			'sb' => @$sb[$i[11]][1],
    			'sb_id' => @$sb[$i[11]][0],
    			'road_class' => @$road_classes[$i[2]],
    			'construction_year' => $i[3],
    			'km_from' => $i[4],
    			'm_from' => $i[5],
    			'km_to' => $i[6],
    			'm_to' => $i[7],
    			'direction' => $i[10],
    			'survey_lane' => $i[21],
    			'section_length' => $i[8],
    			'number_of_lanes' => $i[9],
    			'pavement_type' => $i[22],
    			'lane_width' => $i[12],
    			'pavement_thickness' => $i[13],
    			'latest_repair_time' => $i[14],
    			'repair_category' => @$repair_categories[$i[15]],	
    			'repair_classification' => @$repair_classifications[$i[16]],	
    			'traffic_survey_year' => $i[17],
    			'total_traffic_volume' => $i[18],
    			'heavy_traffic' => $i[19],
    			'pc_survey_time' => $i[20],
    			'pc_pavement_type' => $i[22],
    			'cracking' => $i[23],
    			'patching' => $i[24],
    			'pothole' => $i[25],
    			'cracking_ratio' => $i[26],
    			'rut_max' => $i[27],
    			'rut_avg' => $i[28],
    			'iri' => $i[29],
    			'mci' => $i[30],
    		];
    		if ($type == 1)
    		{
    			$tmp+= [
    				'error' => $i[0]
    			];
    		}
    		else if ($type == 2)
    		{
    			for ($j = 31; $j <= 55; $j++)
    			{
    				$tmp+= [
    					$j => $i[$j]
    				];
    			}
    		}
    		else if ($type == 3)
    		{
    			if (isset($i[71])) return;
    			for ($j = 31; $j <= 70; $j++)
    			{
    				$tmp+= [
    					$j => @$i[$j]
    				];
    			}
    		}
    		else if (in_array($type, [4, 5, 6, 7, 8]))
    		{
    			if (!isset($i[71])) return;
    			if ($type == 4 && isset($i[71]) && $i[71] != 1)
    			{
    				return;
    			}
    			if ($type == 5 && isset($i[71]) && $i[71] != 2)
    			{
    				return;
    			}
    			if ($type == 6 && isset($i[71]) && $i[71] != 3)
    			{
    				return;
    			}
    			if ($type == 7 && isset($i[71]) && $i[71] != 4)
    			{
    				return;
    			}
    			if ($type == 8 && isset($i[71]) && $i[71] != 5)
    			{
    				return;
    			}

    			for ($j = 31; $j <= 71; $j++)
    			{
    				$tmp+= [
    					$j => $i[$j]
    				];
    			}
    			// add method info
    			switch ($type)
    			{
    				case 4:
    					if (!empty($i[61]) && !empty($i[66]))
                        {
                            $unit_cost = trim($i[66])/(trim($i[61]) * 1000);
                        }
                        else
                        {
                            $unit_cost = @$method_final[(string)$i[56]][$rmb[$i[11]][0] - 1] / 1000;
                        }
	    				$tmp+= [
							'selected_repair_method' => @$method_final[(string)$i[56]][5],
							'selected_repair_classification' => @$method_final[(string)$i[56]][6],
							'unit_cost' => number_format($unit_cost),
							'selected_quantity_unit' => $i[61],
							'selected_unit_quantity' => @$unit[$method_final[(string)$i[56]][7]],
							'amount' => number_format($i[66]*0.001)
		   				];
		   				
		   				break;

		   			case 5:
		   				if (!empty($i[62]) && !empty($i[67]))
                        {
                            $unit_cost = trim($i[67])/(trim($i[62]) * 1000);
                        }
                        else
                        {
                            $unit_cost = @$method_final[(string)$i[57]][$rmb[$i[11]][0] - 1] / 1000;
                        }
	    				$tmp+= [
							'selected_repair_method' => @$method_final[(string)$i[57]][5],
							'selected_repair_classification' => @$method_final[(string)$i[57]][6],
							'unit_cost' => number_format($unit_cost),
							'selected_quantity_unit' => $i[62],
							'selected_unit_quantity' => @$unit[$method_final[(string)$i[57]][7]],
							'amount' => number_format($i[67]*0.001)
		   				];
		   				break;
    				case 6:
    					if (!empty($i[63]) && !empty($i[68]))
                        {
                            $unit_cost = trim($i[68])/(trim($i[63]) * 1000);
                        }
                        else
                        {
                            $unit_cost = @$method_final[(string)$i[58]][$rmb[$i[11]][0] - 1] / 1000;
                        }
    					$tmp+= [
							'selected_repair_method' => @$method_final[(string)$i[58]][5],
							'selected_repair_classification' => @$method_final[(string)$i[58]][6],
							'unit_cost' => number_format($unit_cost),
							'selected_quantity_unit' => $i[63],
							'selected_unit_quantity' => @$unit[$method_final[(string)$i[58]][7]],
							'amount' => number_format($i[68]*0.001)
		   				];
		   				break;
    				case 7:
    					if (!empty($i[64]) && !empty($i[69]))
                        {
                            $unit_cost = trim($i[69])/(trim($i[64]) * 1000);
                        }
                        else
                        {
                            $unit_cost = @$method_final[(string)$i[59]][$rmb[$i[11]][0] - 1] / 1000;
                        }
	    				$tmp+= [
							'selected_repair_method' => @$method_final[(string)$i[59]][5],
							'selected_repair_classification' => @$method_final[(string)$i[59]][6],
							'unit_cost' => number_format($unit_cost),
							'selected_quantity_unit' => $i[64],
							'selected_unit_quantity' => @$unit[$method_final[(string)$i[59]][7]],
							'amount' => number_format($i[69]*0.001)
		   				];
		   				break;
    				case 8:
    					if (!empty($i[65]) && !empty($i[70]))
                        {
                            $unit_cost = trim($i[70])/(trim($i[65]) * 1000);
                        }
                        else
                        {
                            $unit_cost = @$method_final[(string)$i[60]][$rmb[$i[11]][0] - 1] / 1000;
                        }
	    				$tmp+= [
							'selected_repair_method' => @$method_final[(string)$i[60]][5],
							'selected_repair_classification' => @$method_final[(string)$i[60]][6],
							'unit_cost' => number_format($unit_cost),
							'selected_quantity_unit' => $i[65],
							'selected_unit_quantity' => @$unit[$method_final[(string)$i[60]][7]],
							'amount' => number_format($i[70]*0.001)
		   				];
		   				break;
		   			default:
		   			 	break;
    			}
    			
    		}
    		else if ($type == 9)
    		{
    			if (isset($i[71])) return;
    			for ($j = 31; $j < 71; $j++)
    			{
    				$tmp+= [
    					$j => $i[$j]
    				];
    			}
    		}
    		else if ($type == 11)
    		{
    			if (!isset($i[71]) || (isset($i[71]) && $i[71] != 6)) return;
    			for ($j = 31; $j <= 70; $j++)
    			{
    				$tmp+= [
    					$j => $i[$j]
    				];
    			}
    		}
    		$dataset[] = $tmp;
        }, 99999999);
        
        return collect($dataset);
	}

	private function _getMethodList($session_id)
	{
		$method_final = [];
		$file = public_path("application/process/work_planning/".$session_id."/input/repair/list.csv");

		$fp = fopen($file, 'r');
		// fseek($fp, 3);
		while ($line = fgetcsv($fp)) 
		{
			$method_tmp[] = $line;
		}
		fclose($fp);
      
		foreach ($method_tmp as $row)
        {	
        	$key = $row[0];
        	$method_final[$key] = [$row[1], $row[2], $row[3], $row[4], $row[5]];
        	if (\App::isLocale('en'))
        	{
        		$method_final[$key][] = $row[7];
        		$method_final[$key][] = $row[9];
        	}
        	else
        	{
        		$method_final[$key][] = $row[6];
        		$method_final[$key][] = $row[8];
        	}
        	$method_final[$key][] = $row[10];
        }
        return $method_final;
	}

	private function _doFilter($data, $request)
	{
		$filter_exact = ['route_name', 'branch_number', 'road_class', 'direction', 'pavement_type'];

		foreach ($filter_exact as $key) 
		{
			if (!empty($request->{$key}))
			{
				$data = $data->where($key, $request->{$key});
			}
		}

		$filter_super = ['construction_year', 'km_from', 'm_from', 'km_to', 'm_to'];
		foreach ($filter_super as $key) 
		{
			if (!empty($request->{$key}))
			{
				$parseSuperInput = \Helper::parseSuperInput($request->{$key});
				$data = $data->where($key, $parseSuperInput[0], $parseSuperInput[1]);
			}
		}

		return $data;
	}

	/**
	 * $type:
	 *	0: valid sections, display data step
	 * 	1: invalid sections, display data step
	 *  2: forecasting value, forecasting step
	 *  3: repair work long list
	 */
	public function getAjaxDataTable(Request $request, $session_id, $type = 0, $list = 0)
	{	
		$data = $this->_getCsvData($session_id, $type, $list);
		$method_final = [];
		if ($type == 3 || $type == 11)
		{
			$method_final = $this->_getMethodList($session_id);
		}
	
		$data = $this->_doFilter($data, $request);
        $unit = \App\Models\mstMethodUnit::get()->pluck('code_name', 'id');
		return Datatables::of($data)
			->addColumn('error', function($r) {
				return "<div class='wp-error wp-error-" . @$r['error'] . "'></div>";
			})
		    ->editColumn('direction', function($r) {
		    	switch ($r['direction']) 
                {
                    case 1:
                        return trans('back_end.left_direction');
                    case 2:
                        return trans('back_end.right_direction');
                    case 3:
                        return trans('back_end.single_direction');
                    default:
                        return $r['direction'];
                }
            })
			->addColumn('extra_view', function($i) use($type, $method_final, $unit) {
				return view('front-end.work_planning.extra_view', [
						'data' => $i,
						'type' => $type,
						'method_final' => $method_final,
						'unit' => $unit
					])->render();
	        })
		    ->make(true);
	}

	/**
	 * update repair methods used in the session work planning and create .csv
	 *
	 * @param  varchar  $session_id  uuid of session
	 * @param  Request  $request  request of ajax
	 * @access  public
	 * @return	json  code: 200-success, session_id
	 */
	public function updateWorkPlanningBasePlanningYear($session_id, Request $request)
	{
		\DB::beginTransaction();
		try
		{
			$work_planning = tblWorkPlanning::find($session_id);
			if ($work_planning->status == 0)
			{
				$base_planning_year = $request->base_planning_year;
			
				$work_planning->base_planning_year = $request->base_planning_year;
				$work_planning->updated_by = Auth::user()->id;
				$work_planning->save();

				$this->_importForecastValue($work_planning);
			}
			
			
			\DB::commit();
			return response()->json(array(
				'code' => 200,
				'session_id' => $session_id,
			));
		}
		catch(\exception $e)
		{
			DB::rollBack();
			dd($e);
		}
	}

	private function _getChartDataForPavementType($deterioration_id, $json, $link)
    {
        $file = 'para3.csv';
    
        $parameter = Excel::load('public/application/process/deterioration/'.$deterioration_id.'/'.$link.'/output3/'.$file)->get();

        $header = '';
        foreach ($parameter[0] as $key => $value)
        {
            $header = $key;
        }

        $data[] = $header/pow(10, strlen($header)-1);
        
        for ($i = 0; $i < $parameter->count() - 1; $i++)
        {
            $data[] = (float) $parameter[$i][$header];
        }
        
        $expected_life_length = array();
        for ($i = 0; $i <= count($data); $i++)
        {
            if ($i == 0)
            {
                $expected_life_length[] = 0;
            }
            else
            {
                $expected_life_length[] = 1/($data[$i-1]) + $expected_life_length[$i-1];
            }
        }

        foreach ($expected_life_length as $k => $v) 
        {
            $expected_life_length[$k] = round($v, 2);    
        }
        
        $chart_data = array();
        for ($i = 0; $i < count($expected_life_length); $i++)
        {
            $chart_data[] = [$expected_life_length[$i], $json[$i]['from']];
        }
        return $chart_data;
    }

	private function _importForecastValue($work_planning)
	{
		$condition_rank = [
			'crack' => tblConditionRank::where('target_type', 1)->orderBy('rank')->get(),
			'rut' => tblConditionRank::where('target_type', 2)->orderBy('rank')->get(),
			'iri' => tblConditionRank::where('target_type', 3)->orderBy('rank')->get(),
		];

		$organization = $work_planning->organizations()->get()->pluck('id')->toArray();
        $year = $work_planning->base_planning_year;
        // $base_planning_gap = $year - $work_planning->year;
        
        // get id in chose process
        $ids = tblDeterioration::where('year_of_dataset', $work_planning->year)
                ->whereIn('organization_id', $organization)
                ->where('dataset_flg', 1)
                ->get()
                ->pluck('id')
                ->toArray();  
        
        $crack_rank = count($condition_rank['crack']);
        $rut_rank = count($condition_rank['rut']);
        $iri_rank = count($condition_rank['iri']);
        $data = ['crack' => $crack_rank, 'rut' => $rut_rank, 'IRI' => $iri_rank];
        $eps = ['epsilon31' => 'AC', 'epsilon32' => 'BST'];
        $result = array();
        $c_r_i = [];
        $data_from = \Helper::_getXAxisData($data, $condition_rank);

        foreach ($ids as $id)
        {   
            foreach ($data as $link => $offset)
            {
                // $predict_tmp = [];
                foreach ($eps as $k => $v) 
                {
                    $epsilon = file(public_path("application/process/deterioration/".$id."/".$link."/output4/" .$k. ".csv"));
                    $data_eps = [];
                    foreach ($epsilon as $row)
                    {
                        $tmp = explode(',', $row);
                        array_pop($tmp);
                        $data_eps[] = $tmp;
                    }
                   
                    foreach ($data_eps as $row)
                    {   
                        // get data from deterioration
                        $key = $row[0].$v;

                        $det_data = array_slice($row, 2 + $offset, $offset);
                        $det_data = array_pad($det_data, $offset, '0');
                       
                        array_unshift($det_data, $row[1]);
                        if (!isset($c_r_i[$key]))
                        {
                            $c_r_i[$key] = [
                                'crack' => array_fill(0, $crack_rank + 1, '0'),
                                'rut' => array_fill(0, $rut_rank + 1, '0'),
                                'IRI' => array_fill(0, $iri_rank + 1, '0')
                            ];
                        }
                        $c_r_i[$key][$link] = $det_data;
                    }
                }
            }
        }
        
        $cc_curves = [
        	'crack' => $this->_getChartDataForPavementType($ids[0], $condition_rank['crack'], 'crack'),
        	'rut' => $this->_getChartDataForPavementType($ids[0], $condition_rank['rut'], 'rut'),
        	'IRI' => $this->_getChartDataForPavementType($ids[0], $condition_rank['iri'], 'IRI'),
        ];

        $source = "application/process/work_planning/" . $work_planning->id . "/data/input.csv";
        $input = array();

		Helper::getRowDataChunk($source, 1024, function($chunk, &$handle, $iteration) use(&$input) {
            $array_cake = explode(',', $chunk);
            for ($i = 1; $i <= count($array_cake) - 1; $i++)
            {
            	$tmp = str_replace(array("\n", "\r", "\r\n", "\n\r"), '', $array_cake[$i]);
                $array_cake[$i] = trim($tmp,'"');
            }
            if (count($array_cake) > 5)
            {
            	$input[] = $array_cake;
            }
        }, 99999999);
		$new_data = [];
        foreach ($input as $index => $tmp) 
        {
            $final_forecast = null;
            $error = $tmp[0];
        	if ($error != 0)
        	{
        		$new_data[] = $input[$index];
        		continue;
        	}

            $section_id2 = $tmp[1];
            $road_class = $tmp[2];
            $surface = $tmp[22];
            // forecast
            $crack = isset($tmp[26]) ? $tmp[26] : 0;
            // $rut = isset($tmp[28]) ? $tmp[28] : 0;// average
            // temporarily change average to max
            $rut = isset($tmp[27]) ? $tmp[27] : 0;// max
            $IRI = isset($tmp[29]) ? $tmp[29] : 0;

            $survey_year = substr($tmp[20], 0, 4);
            if (empty($survey_year)) $survey_year = $work_planning->year;
            $base_planning_gap = $year - $survey_year;
          
            $key = $section_id2 . $surface;
            if (isset($c_r_i[$key]) || $surface == 'CC')
            {   
            	if ($surface == 'CC')
            	{
            		$forecast_crack = \Helper::predict($cc_curves['crack'], $crack, $base_planning_gap);
	                $forecast_rut = \Helper::predict($cc_curves['rut'], $rut, $base_planning_gap);
	                $forecast_IRI = \Helper::predict($cc_curves['IRI'], $IRI, $base_planning_gap);
            	}
            	else
            	{
            		foreach ($c_r_i[$key] as $link => $value)
	                {
	                    $x = $c_r_i[$key][$link];
	                    array_shift($x);
	                    $y = $data_from[$link];
	                    $data_tmp = array_combine($y, $x);
	                    $p[$link] = [];
	                    foreach ($data_tmp as $k => $v)
	                    {
	                        $p[$link][] = [$v, $k]; // (x, y)
	                    }
	                }

	                $forecast_crack = \Helper::predict($p['crack'], $crack, $base_planning_gap);
	                $forecast_rut = \Helper::predict($p['rut'], $rut, $base_planning_gap);
	                $forecast_IRI = \Helper::predict($p['IRI'], $IRI, $base_planning_gap);
            	}
                $MCI_year1 = Helper::getMCI($forecast_crack[0], $forecast_rut[0], $forecast_IRI[0], $surface); 
                $MCI_year2 = Helper::getMCI($forecast_crack[1], $forecast_rut[1], $forecast_IRI[1], $surface); 
                $MCI_year3 = Helper::getMCI($forecast_crack[2], $forecast_rut[2], $forecast_IRI[2], $surface); 
                $MCI_year4 = Helper::getMCI($forecast_crack[3], $forecast_rut[3], $forecast_IRI[3], $surface); 
                $MCI_year5 = Helper::getMCI($forecast_crack[4], $forecast_rut[4], $forecast_IRI[4], $surface); 

                $year1 = [$year + 0, $forecast_crack[0], $forecast_rut[0], $forecast_IRI[0], $MCI_year1];
                $year2 = [$year + 1, $forecast_crack[1], $forecast_rut[1], $forecast_IRI[1], $MCI_year2];
                $year3 = [$year + 2, $forecast_crack[2], $forecast_rut[2], $forecast_IRI[2], $MCI_year3];
                $year4 = [$year + 3, $forecast_crack[3], $forecast_rut[3], $forecast_IRI[3], $MCI_year4];
                $year5 = [$year + 4, $forecast_crack[4], $forecast_rut[4], $forecast_IRI[4], $MCI_year5];
                
                $final_forecast = array_merge($year1, $year2, $year3, $year4, $year5);
            }
            else
            {   
                $final_forecast = array_merge(
                    [$year + 0, $tmp[26], $tmp[27], $tmp[29], $tmp[30]],
                    [$year + 1, $tmp[26], $tmp[27], $tmp[29], $tmp[30]],
                    [$year + 2, $tmp[26], $tmp[27], $tmp[29], $tmp[30]],
                    [$year + 3, $tmp[26], $tmp[27], $tmp[29], $tmp[30]],
                    [$year + 4, $tmp[26], $tmp[27], $tmp[29], $tmp[30]]
                );
            }
            
            $new_data[] = array_merge($input[$index], $final_forecast);
        }

        // $file_path = public_path("application/process/work_planning/" . $work_planning->id . "/data/");
        // $file_name = "input.csv";
        $open = fopen(public_path("application/process/work_planning/" . $work_planning->id . "/data/input_forecast.csv"), 'w+');
		foreach ($new_data as $row)
		{
		    fputcsv($open, $row);
		}	
		fclose($open);
		
        // $excelFile = Excel::load($file_path . $file_name,  function ($reader) use ($final_forecast) {
        //     $reader->sheet(0, function($sheet) use ($final_forecast) {
        //         $sheet->fromArray($final_forecast, NULL, 'AF0');
        //     });
        // })->store('csv', $file_path); 
	}

	public function getListRoadCategory(Request $request)
	{
		$result = array();
		$user_id = Auth::user()->id;
		$road_types = mstRoadCategory::allOptionToAjax(FALSE, TRUE);
		@$road_types[0]['selected'] = TRUE;
		$method = $request->repair_method;
		
		$surface_text = '';
		$surface_type = 0;
		$color = '';
		if (trim($request->repair_method) !== '')
		{
			$method = mstRepairMethod::find($request->repair_method);
			$surface_type = $surface = $method->pavement_type;
			$color = $method->code;
			
			if ($surface == 1)
			{
				$surface_text = 'AC';
			}
			else if ($surface == 2)
			{
				$surface_text = 'BST';
			}
			else if ($surface == 3)
			{
				$surface_text = 'CC';
			}
		}
		
		/*$cell = tblRepairMatrixCell::where('repair_matrix_id', $request->repair_matrix)
									->where('user_id', $user_id);
		
		
		if (isset($cell) && trim($request->repair_method) !== '')
		{
			$cell = $cell->with(array(
					'repairMethodCellValue' => function($query) use ($method) {
						$query->where('parameter_id', 0)
							->where('value', $method);
					},
					
					'repairMatrixCellValue' => function($query) {
						$query->with(array(
							'parameter' => function($sql) {
								$sql->where('code', 'road_type');
							}
						));
					})
				)
				->orderBy('created_at', 'desc')
				->get();
			
			foreach ($cell as $r)
			{
				if (count($r->repairMethodCellValue->toArray()) > 0 && count($r->repairMatrixCellValue->toArray()) > 0)
				{
					foreach ($r->repairMatrixCellValue as $p)
					{
						if ($p->parameter)
						{
							$road_types[$p->value]['selected'] = TRUE;
							break;
						}
						else
						{
							continue;
						}
					}
				}
				else
				{
					continue;
				}
			}
		}
		else if (trim($request->repair_method) == '')
		{
			$road_types = array();
		}*/
		
		return response()->json(array(
			'code' => 200,
			'data' => $road_types,
			'surface_text' => $surface_text,
			'surface_type' => $surface_type,
			'color' => $color,
		));
	}

	public function getListRoadClass(Request $request)
	{
		$result = array();
		$user_id = Auth::user()->id;
		$method = $request->repair_method;
		$road_type = $request->road_type;
		
		$road_class = mstRoadClass::allOptionToAjax(FALSE, $road_type);
		@$road_class[0]['selected'] = TRUE;
		/*$cell = tblRepairMatrixCell::where('repair_matrix_id', $request->repair_matrix)
									->where('user_id', $user_id);
		if (isset($cell) && trim($method) !== '' && trim($request->road_type) !== '')
		{
			$cell = $cell->with(array(
					'repairMethodCellValue' => function($query) use ($method) {
						$query->where('parameter_id', 0)
							->where('value', $method);
					},
					
					'roadTypeCellValue' => function($query) use ($road_type) {
						$query->where('parameter_id', 1)
							->where('value', $road_type);
					},
					
					'repairMatrixCellValue' => function($query) {
						$query->with(array(
							'parameter' => function($sql) {
								$sql->where('code', 'road_class');
							}
						));
					})
				)
				->orderBy('created_at', 'desc')
				->get();
			
			foreach ($cell as $r)
			{
				if (count($r->repairMethodCellValue->toArray()) > 0 && count($r->repairMatrixCellValue->toArray()) > 0 && count($r->roadTypeCellValue->toArray()) > 0)
				{
					foreach ($r->roadTypeCellValue as $check)
					{
						if (isset($check->parameter))
						{
							foreach ($r->repairMatrixCellValue as $p)
							{
								if (isset($p->parameter))
								{
									$road_class[$p->value]['selected'] = TRUE;
									break;
								}
								else
								{
									continue;
								}
							}
						}
					}
				}
				else
				{
					continue;
				}
			}
		}
		else if (trim($request->repair_method) == '' || trim($request->road_type) == '')
		{
			$road_class = array();
		}*/
		
		return response()->json(array(
			'code' => 200,
			'data' => $road_class,
		));
	}
	
	private function _createCustomMatrix($matrix, $repair_matrix_id)
	{
		tblRepairMatrixCell::where('repair_matrix_id', $repair_matrix_id)
        	->where('user_id', \Auth::user()->id)->delete();

        foreach ($matrix as $road_type => $road_classes) 
        {
            foreach ($road_classes as $road_class => $pavement_types) 
            {
                foreach ($pavement_types as $pavement_type => $cracks) 
                {
                    foreach ($cracks as $cindex => $ruts) 
                    {
                        foreach ($ruts as $rindex => $repair_method) 
                        {
                            if ($repair_method != 0)
                            {
                                $rec = new tblRepairMatrixCell;
                                $rec->repair_matrix_id = $repair_matrix_id;
                                $rec->user_id = \Auth::user()->id;
                                $rec->target_type = 2;
                                $rec->created_by = \Auth::user()->id;
                                $rec->row = $cindex;
                                $rec->column = $rindex;
                                $rec->save();
    
                                $rec->saveRelation($repair_method, $road_type, $road_class, $pavement_type, $cindex, $rindex);
                            }
                        }
                    }
                }
            }
        }
		// remove old records
		// \DB::table('tblRepair_matrix_cell')
		// 	->where('repair_matrix_id', $repair_matrix_id)
		// 	->where('user_id', Auth::user()->id)
		// 	->where('target_type', 2)
		// 	->delete();

		// $latest_matrix_chk = tblRepairMatrixCell::where('repair_matrix_id', $repair_matrix_id)
		// 		->where('user_id', Auth::user()->id)
		// 		->where('target_type', 2)
		// 		->count();
		// $data;
		// if ($latest_matrix_chk == 0)
		// {
		// 	// clone default matrix
		// 	$data = tblRepairMatrixCell::with([
		// 			'crackValue',
		// 			'rutValue',
		// 			'repairMethodValue',
		// 			'roadTypeValue',
		// 			'roadClassValue',
		// 			'surfaceValue'
		// 		])
		// 		->where('repair_matrix_id', $repair_matrix_id)
		// 		->whereNull('user_id')
		// 		->whereNull('target_type')
		// 		->get();

		// 	foreach ($data as $r) 
		// 	{
		// 		$clone = $r->replicate();
		// 		$clone->user_id = Auth::user()->id;
		// 		$clone->target_type = 2;
		// 		$clone->created_by = Auth::user()->id;
		// 		$clone->push();
				
		// 		$clone->crackValue()->save($r->crackValue->replicate());
		// 		$clone->rutValue()->save($r->rutValue->replicate());
		// 		$clone->repairMethodValue()->save($r->repairMethodValue->replicate());
		// 		$clone->roadTypeValue()->save($r->roadTypeValue->replicate());
		// 		$clone->roadClassValue()->save($r->roadClassValue->replicate());
		// 		$clone->surfaceValue()->save($r->surfaceValue->replicate());
		// 	}
		// }

		// // apply change to new matrix
		// foreach ($cell_changes as $key => $change) 
		// {
		// 	$indexes = explode('-', $key);
		// 	$repair_method = $indexes[0];
		// 	$road_type = $indexes[1];
		// 	$road_class = $indexes[2];
		// 	$surface = $indexes[3];
		// 	$row = $indexes[4];
		// 	$col = $indexes[5];

		// 	if ($change == '')
		// 	{
		// 		tblRepairMatrixCell::whereHas('roadTypeValue', function($query) use ($road_type) {
		// 				$query->where('value', $road_type);
		// 			})
		// 			->whereHas('roadClassValue', function($query) use ($road_class) {
		// 				$query->where('value', $road_class);
		// 			})
		// 			->whereHas('surfaceValue', function($query) use ($surface) {
		// 				$query->where('value', $surface);
		// 			})
		// 			->where('row', $row)
		// 			->where('column', $col)
		// 			->where('user_id', Auth::user()->id)
		// 			->where('target_type', 2)
		// 			->delete();
		// 	}
		// 	else
		// 	{
		// 		$repair_matrix_cell = tblRepairMatrixCell::whereHas('roadTypeValue', function($query) use ($road_type) {
		// 				$query->where('value', $road_type);
		// 			})
		// 			->whereHas('roadClassValue', function($query) use ($road_class) {
		// 				$query->where('value', $road_class);
		// 			})
		// 			->whereHas('surfaceValue', function($query) use ($surface) {
		// 				$query->where('value', $surface);
		// 			})
		// 			->where('row', $row)
		// 			->where('column', $col)
		// 			->where('target_type', 2)
		// 			->first();
				
		// 		if ($repair_matrix_cell)
		// 		{
		// 			\DB::table('tblRepair_matrix_cell_values')
		// 	            ->where('repair_matrix_cell_id', $repair_matrix_cell->id)
		// 	            ->where('parameter_id', 'repair_method')
		// 	            ->update(['value' => $repair_method]);
		// 		}
		// 		else
		// 		{
		// 			$rec = new tblRepairMatrixCell;
		// 			$rec->repair_matrix_id = $repair_matrix_id;
		// 			$rec->user_id = Auth::user()->id;
		// 			$rec->target_type = 2;
		// 			$rec->created_by = Auth::user()->id;
		// 			$rec->row = $row;
		// 			$rec->column = $col;
		// 			$rec->save();

		// 			$rec->saveRelation($indexes[0], $indexes[1], $indexes[2], $indexes[3], $indexes[4], $indexes[5]);
		// 		}
		// 	}
		// }
	}

	public function createRepairMatrixCSV(Request $request)
	{
		\DB::beginTransaction();
		try
		{
			$session_id = $request->session;
			$work_planning = tblWorkPlanning::find($session_id);
			if ($work_planning->status == 0)
			{
				$user_id = Auth::user()->id;
				
				$matrix = $request->matrix;
				$nochange = $request->nochange;
				$price_esca_factor = $request->price_esca_factor;
				if ($nochange == 0)
				{
					// create latest repair matrix for user
					$this->_createCustomMatrix($matrix, $request->repair_matrix_id);
				}

				$data = null;
				$latest_matrix_chk = tblRepairMatrixCell::where('repair_matrix_id', $request->repair_matrix_id)
					->where('user_id', $user_id)
					->where('target_type', 2)
					->count();
				if ($latest_matrix_chk > 0)
				{
					$data = tblRepairMatrixCell::where('repair_matrix_id', $request->repair_matrix_id)
						->where('user_id', $user_id)
						->where('target_type', 2);
				}
				else
				{
					$data = tblRepairMatrixCell::where('repair_matrix_id', $request->repair_matrix_id)
						->whereNull('user_id');
				}
														
				if (isset($data))
				{
					$info_matrix = $data->with([
							'crackValue',
							'rutValue',
							'repairMethodValue',
							'roadTypeValue',
							'roadClassValue',
							'surfaceValue'
						])
						->get();
					// prepare the structure for csv
					$repair_matrix_structure = [];
					foreach ($info_matrix as $i) 
					{
						$key = implode('-', [
							$i->roadTypeValue->value,
							$i->roadClassValue->value,
							$i->surfaceValue->value,
							$i->row, 
							$i->column
						]);
						$repair_matrix_structure[$key] = $i->repairMethodValue->value;
					}
					
					// generate csv
					$repair_matrix = tblRepairMatrix::findOrFail($request->repair_matrix_id);

					$crack_ranks = \App\Models\tblConditionRank::where('target_type', 1)->orderBy('rank')->get();
	            	$rut_ranks = \App\Models\tblConditionRank::where('target_type', 2)->orderBy('rank')->get();
					// $condition_rank = Helper::convertJsonConditionRank($repair_matrix->condition_rank);
					
					$crack_rank = $crack_ranks->count();
					$rut_rank = $rut_ranks->count();

					$structure = [
						0 => [
							1, 2, 3, 4
						],
						1 => [
							1, 2, 3, 4, 5, 6
						]
					];

					// $work_planning = tblWorkPlanning::find($session_id);
					$work_planning->matrix_flg = 0;
					$work_planning->save();

					foreach ($structure as $road_type => $road_classes) 
					{
						foreach ($road_classes as $road_class) 
						{
							$source = "application/process/work_planning/$session_id/input/repair/$road_type/$road_class";

							// create 3 files
							Excel::create('repair_matrix1', function($excel) use ($crack_rank, $rut_rank, $road_type, $road_class, $repair_matrix_structure) {
								$excel->sheet('repair_matrix1', function($sheet) use ($crack_rank, $rut_rank, $road_type, $road_class, $repair_matrix_structure) {
									for ($i = 1; $i <= $crack_rank; $i++)
									{
										$tmp =[];
										for ($j = 0; $j < $rut_rank; $j++)
										{
											$key = implode('-', [$road_type, $road_class, 1, $i - 1, $j]);
											
											if (isset($repair_matrix_structure[$key]))
											{
												$tmp[] = $repair_matrix_structure[$key];
											}
											else
											{
												$tmp[] = 0;	
											}
										}
										$sheet->row($i, $tmp);
									}
							    });
							})->store('csv', $source);
							Excel::create('repair_matrix2', function($excel) use ($crack_rank, $rut_rank, $road_type, $road_class, $repair_matrix_structure) {
								$excel->sheet('repair_matrix2', function($sheet) use ($crack_rank, $rut_rank, $road_type, $road_class, $repair_matrix_structure) {
									for ($i = 1; $i <= $crack_rank; $i++)
									{
										$tmp =[];
										for ($j = 0; $j < $rut_rank; $j++)
										{
											$key = implode('-', [$road_type, $road_class, 2, $i - 1, $j]);
											if (isset($repair_matrix_structure[$key]))
											{
												$tmp[] = $repair_matrix_structure[$key];
											}
											else
											{
												$tmp[] = 0;	
											}
										}
										$sheet->row($i, $tmp);
									}
							    });
							})->store('csv', $source);
							Excel::create('repair_matrix3', function($excel) use ($crack_rank, $rut_rank, $road_type, $road_class, $repair_matrix_structure) {
								$excel->sheet('repair_matrix1', function($sheet) use ($crack_rank, $rut_rank, $road_type, $road_class, $repair_matrix_structure) {
									for ($i = 1; $i <= $crack_rank; $i++)
									{
										$tmp =[];
										
										$key = implode('-', [$road_type, $road_class, 3, $i - 1, 0]);
										if (isset($repair_matrix_structure[$key]))
										{
											$tmp[] = $repair_matrix_structure[$key];
										}
										else
										{
											$tmp[] = 0;	
										}
										
										$sheet->row($i, $tmp);
									}
							    });
							})->store('csv', $source);
						}
					}

					$matrix_output = (new \App\Jobs\work_planning($session_id))->onQueue('work_planning');
	        		dispatch($matrix_output);
					\DB::commit();
					return response()->json(array(
						'code' => 200,
						'description' => 'success',
					));
				}
				else
				{
					return response()->json(array(
						'code' => 901,
						'description' => 'error',
					));
				}
			}
			return response()->json(array(
				'code' => 200,
				'description' => 'success',
			));
			
		}
		catch (\Exception $e)
		{
			\DB::rollBack();
			dd($e);
		}
	}

	public function getConditionRank(Request $request)
	{
		$user_id = Auth::user()->id;
		$repair_method = $request->repair_method;
		$road_type = $request->road_type;
		$road_class = $request->road_class;

		$method = mstRepairMethod::find($repair_method);
		$surface = $method->pavement_type;
		// $condition_rank = tblBudgetSimulation::find($request->session)->repairMatrix->condition_rank;
		
		$repair_matrix;
		$latest_matrix_chk = tblRepairMatrixCell::where('repair_matrix_id', $request->repair_matrix)
			->where('user_id', $user_id)
			->where('target_type', 2)
			->count();
		if ($latest_matrix_chk > 0)
		{
			$repair_matrix = tblRepairMatrixCell::where('repair_matrix_id', $request->repair_matrix)
				->where('user_id', $user_id)
				->where('target_type', 2);
		}
		else
		{
			$repair_matrix = tblRepairMatrixCell::where('repair_matrix_id', $request->repair_matrix)
				->whereNull('user_id')
				->whereNull('target_type');
		}

		$repair_matrix = $repair_matrix
			->whereHas('roadTypeValue', function($query) use ($road_type) {
				$query->where('value', $road_type);
			})
			->whereHas('roadClassValue', function($query) use ($road_class) {
				$query->where('value', $road_class);
			})
			->whereHas('surfaceValue', function($query) use ($surface) {
				$query->where('value', $surface);
			})
			->with('repairMethodValue')
			->get();

		$result = array();
		foreach ($repair_matrix as $r)
		{
			$method = mstRepairMethod::find($r->repairMethodValue->value);
			if ($method)
			{
				$result[] = array(
					'col' => $r->column,
					'row' => $r->row,
					'color' => $method->code,
				);	
			}
		}
		
		return response()->json(array(
			'code' => 200,
			'data' => $result,
		));
		
	}

	/*public function createRepairMatrix(Request $request)
	{

		\DB::beginTransaction();
		try
		{
			$flag = FALSE;
			$user_id = Auth::user()->id;
			$method = $request->repair_method;
			$road_type = $request->road_type;
			$road_class = $request->road_class;
			
			$check_last_repair_matrix = tblRepairMatrixCell::where('repair_matrix_id', $request->repair_matrix)
															->where('user_id', $user_id)
															->with(array(
																'repairMethodCellValue' => function($query) use ($method) {
																	$query->where('parameter_id', 0)
																		->where('value', $method);
																},
																
																'roadTypeCellValue' => function($query) use ($road_type) {
																	$query->where('parameter_id', 1)
																		->where('value', $road_type);
																},
																
																'roadClassCellValue' => function($query) use ($road_class) {
																	$query->where('parameter_id', 2)
																		->where('value', $road_class);
																},										
															))
															->get();
			foreach ($check_last_repair_matrix as $cell)
			{
				if (count($cell->repairMethodCellValue->toArray()) > 0 && count($cell->roadTypeCellValue->toArray()) > 0 && count($cell->roadClassCellValue->toArray()) > 0)
				{
					// foreach ($cell->repairMatrixCellValue as $r)
					// {
						// $old = tblRepairMatrixCellValue::where('repair_matrix_cell_id', $r->repair_matrix_cell_id)->delete();
					// }
					
					$cell->delete();
				}
				else
				{
					$flag = TRUE;
				}
				// $cell->repairMatrixCellValue()->delete();
			}
			
			foreach ($request->data as $method => $road_types)
			{
				if (is_array($road_types))
				{
					$value_method = $method;
					foreach ($road_types as $road_type => $road_class)
					{
						if (is_array($road_class))
						{
							$value_road_type = $road_type;
							foreach ($road_class as $class => $matrices)
							{
								if(is_array($matrices))
								{
									$value_road_class = $class;
									foreach ($matrices as $row => $cell)
									{
										foreach ($cell as $col => $value)
										{
											if (is_array($value))
											{
												if ($flag)
												{
													$delete = tblRepairMatrixCell::where('repair_matrix_id', $request->repair_matrix)
																				->where('user_id', $user_id)
																				->with(array(
																					'roadTypeCellValue' => function($query) use ($value_road_type) {
																						$query->where('parameter_id', 1)
																							->where('value', $value_road_type);
																					},
																					
																					'roadClassCellValue' => function($query) use ($value_road_class) {
																						$query->where('parameter_id', 2)
																							->where('value', $value_road_class);
																					},										
																				))
																				->where('row', $row)
																				->where('column', $col)
																				->where('type', $request->surface_type)
																				->get();
													foreach ($delete as $cell)
													{
														if (count($cell->roadTypeCellValue->toArray()) > 0 && count($cell->roadClassCellValue->toArray()) > 0)
														{
															$cell->delete();
														}
													}
												}
												
												$cell = new tblRepairMatrixCell;
												$cell->repair_matrix_id = $request->repair_matrix;
												$cell->type = $request->surface_type;
												$cell->row = $row;
												$cell->column = $col;
												$cell->user_id = $user_id;
												$cell->created_by = $user_id;
												$cell->target_type = 2;
												$cell->save();
												
												$cell_method = new tblRepairMatrixCellValue;
												$cell_method->repair_matrix_cell_id = $cell->id;
												$cell_method->parameter_id = 0;
												$cell_method->value = $value_method;
												$cell_method->created_by = $user_id;
												$cell_method->save();
												
												$cell_value_crack = new tblRepairMatrixCellValue;
												$cell_value_crack->repair_matrix_cell_id = $cell->id;
												$cell_value_crack->parameter_id = 5;
												$cell_value_crack->value = $value[0].'-'.$value[1];
												$cell_value_crack->created_by = $user_id;
												$cell_value_crack->save();
												
												if (isset($value[2]) && isset($value[3]))
												{
													$cell_value_rut = new tblRepairMatrixCellValue;
													$cell_value_rut->repair_matrix_cell_id = $cell->id;
													$cell_value_rut->parameter_id = 6;
													$cell_value_rut->value = $value[2].'-'.$value[3];
													$cell_value_rut->created_by = $user_id;
													$cell_value_rut->save();
												}
												
												$cell_road_type = new tblRepairMatrixCellValue;
												$cell_road_type->repair_matrix_cell_id = $cell->id;
												$cell_road_type->parameter_id = 1;
												$cell_road_type->value = $value_road_type;
												$cell_road_type->created_by = $user_id;
												$cell_road_type->save();
												
												$cell_road_class = new tblRepairMatrixCellValue;
												$cell_road_class->repair_matrix_cell_id = $cell->id;
												$cell_road_class->parameter_id = 2;
												$cell_road_class->value = $value_road_class;
												$cell_road_class->created_by = $user_id;
												$cell_road_class->save();
											}
											else
											{
												continue;	
											}
										}
									}
								}
								else
								{
									continue;
								}
							}
						}
						else
						{
							continue;
						}
					}
				}
				else
				{
					continue;
				}
			}
			\DB::commit();
			return response()->json(array(
				'code' => 200,
			));

			
		}
		catch(exception $e)
		{	
			\DB::rollBack();
			dd($e->getMessage());
		}
	}*/
	
	function postFormulateAnnualYear(Request $request)
	{
		\DB::beginTransaction();
		try
		{
			$work_planning = tblWorkPlanning::findOrFail($request->session_id);
			if ($work_planning->status == 0)
			{
				$work_planning->total_budget = $request->total_budget;
				$work_planning->year_1 = $request->year_1;
				$work_planning->year_2 = $request->year_2;
				$work_planning->year_3 = $request->year_3;
				$work_planning->year_4 = $request->year_4;
				$work_planning->price_esca_factor = $request->price_esca_factor;
				if (empty($request->third_priority))
				{
					$third_priority = "0";
				}
				else 
				{
					$third_priority = $request->third_priority;
				}
				//$work_planning->criteria = json_encode([$mci_criteria, $road_class_criteria, $tv_criteria]);
				$work_planning->criteria = json_encode([$request->first_priority, $request->second_priority, $third_priority]);
				$work_planning->updated_by = \Auth::user()->id;
				$work_planning->save();

				// budget calculation
				$this->_budgetCalculation($work_planning);
			}
			
			\DB::commit();
			return response()->json(array(
				'code' => 200,
				'description' => 'success',
			));			
		}
		catch (\Exception $e)
		{
			\DB::rollBack();
			dd($e->getMessage());
		}
	}

	private function _budgetCalculation($work_planning)
	{
		$criteria = json_decode($work_planning->criteria);
		$source = public_path("application/process/work_planning/" . $work_planning->id . "/data/input_method.csv");
        $input = array();
		Helper::getRowDataChunk($source, 1024, function($chunk, &$handle, $iteration) use(&$input) {
            $array_cake = explode(',', $chunk);
            for ($i = 1; $i <= count($array_cake) - 1; $i++)
            {
            	$tmp = str_replace(array("\n", "\r", "\r\n", "\n\r"), '', $array_cake[$i]);
                $array_cake[$i] = trim($tmp,'"');
            }
            if (count($array_cake) > 5)
            {
            	$input[] = $array_cake;
            }
        }, 99999999);
		$sort = array();

		foreach ($input as $k => $v) 
		{
			if ($criteria[0] == 1 || $criteria[1] == 1 || $criteria[2] == 1)
	        {
	        	$sort['35'][$k] = @$v['35'];
	        }
	        if ($criteria[0] == 2 || $criteria[1] == 2 || $criteria[2] == 2)
	        {
		    	$sort['2'][$k] = @$v['2'];
		    }
		    if ($criteria[0] == 3 || $criteria[1] == 3 || $criteria[2] == 3)
	        {
	        	$sort['18'][$k] = @$v['18'];
	       	}
	       	
		}
		// criteria value
		// first element => first_priority
		// 1 : MCI; 2 : Road Class; 3: Traffic Volume
		if ($criteria[0] == 1 && $criteria[1] == 0 && $criteria[2] == 0)
		{
			array_multisort($sort['35'], SORT_ASC, $input);	
		}
		else if ($criteria[0] == 1 && $criteria[1] == 2 && $criteria[2] == 0)
		{
			array_multisort($sort['35'], SORT_ASC, $sort['2'], SORT_ASC, $input);	
		}
		else if ($criteria[0] == 1 && $criteria[1] == 3 && $criteria[2] == 0)
		{
			array_multisort($sort['35'], SORT_ASC, $sort['18'], SORT_DESC, $input);	
		}
		else if ($criteria[0] == 1 && $criteria[1] == 2 && $criteria[2] == 3)
		{
			array_multisort($sort['35'], SORT_ASC, $sort['2'], SORT_ASC, $sort['18'], SORT_DESC, $input);
		}
		else if ($criteria[0] == 1 && $criteria[1] == 3 && $criteria[2] == 2)
		{
			array_multisort($sort['35'], SORT_ASC, $sort['18'], SORT_DESC, $sort['2'], SORT_ASC, $input);
		}
		else if ($criteria[0] == 2 && $criteria[1] == 0 && $criteria[2] == 0)
		{
			array_multisort($sort['2'], SORT_ASC, $input);	
		}
		else if ($criteria[0] == 2 && $criteria[1] == 1 && $criteria[2] == 0)
		{
			array_multisort($sort['2'], SORT_ASC, $sort['35'], SORT_ASC, $input);	
		}
		else if ($criteria[0] == 2 && $criteria[1] == 3 && $criteria[2] == 0)
		{
			array_multisort($sort['2'], SORT_ASC, $sort['18'], SORT_DESC, $input);	
		}
		else if ($criteria[0] == 2 && $criteria[1] == 1 && $criteria[2] == 3)
		{
			array_multisort($sort['2'], SORT_ASC, $sort['35'], SORT_ASC, $sort['18'], SORT_DESC, $input);
		}
		else if ($criteria[0] == 2 && $criteria[1] == 3 && $criteria[2] == 1)
		{
			array_multisort($sort['2'], SORT_ASC, $sort['18'], SORT_DESC, $sort['35'], SORT_ASC, $input);
		}
		else if ($criteria[0] == 3 && $criteria[1] == 0 && $criteria[2] == 0)
		{
			array_multisort($sort['18'], SORT_DESC, $input);
		}
		else if ($criteria[0] == 3 && $criteria[1] == 1 && $criteria[2] == 0)
		{
			array_multisort($sort['18'], SORT_DESC, $sort['35'], SORT_ASC, $input);
		}
		else if ($criteria[0] == 3 && $criteria[1] == 2 && $criteria[2] == 0)
		{	
			array_multisort($sort['18'], SORT_DESC, $sort['2'], SORT_ASC, $input);
		}
		else if ($criteria[0] == 3 && $criteria[1] == 1 && $criteria[2] == 2)
		{
			array_multisort($sort['18'], SORT_DESC, $sort['35'], SORT_ASC, $sort['2'], SORT_ASC,$input);
		}
		else if ($criteria[0] == 3 && $criteria[1] == 2 && $criteria[2] == 1)
		{
			array_multisort($sort['18'], SORT_DESC, $sort['2'], SORT_ASC, $sort['35'], SORT_ASC,$input);
		}



		// if ($criteria[0] == 1 && $criteria[1] == 1 && $criteria[2] == 1)
		// {
		// 	array_multisort($sort['35'], SORT_ASC, $sort['2'], SORT_ASC, $sort['18'], SORT_DESC, $input);	
		// }
		// else if ($criteria[0] == 1 && $criteria[1] == 1 && $criteria[2] == 0)
		// {
		// 	array_multisort($sort['35'], SORT_ASC, $sort['2'], SORT_ASC, $input);	
		// }
		// else if ($criteria[0] == 1 && $criteria[1] == 0 && $criteria[2] == 1)
		// {
		// 	array_multisort($sort['35'], SORT_ASC, $sort['18'], SORT_DESC, $input);	
		// }
		// else if ($criteria[0] == 1 && $criteria[1] == 0 && $criteria[2] == 0)
		// {
		// 	array_multisort($sort['35'], SORT_ASC, $input);	
		// }
		// else if ($criteria[0] == 0 && $criteria[1] == 0 && $criteria[2] == 1)
		// {
		// 	array_multisort($sort['18'], SORT_DESC, $input);	
		// }
		// else if ($criteria[0] == 0 && $criteria[1] == 1 && $criteria[2] == 1)
		// {
		// 	array_multisort($sort['2'], SORT_ASC, $sort['18'], SORT_DESC, $input);	
		// }
		// else if ($criteria[0] == 0 && $criteria[1] == 1 && $criteria[2] == 0)
		// {
		// 	array_multisort($sort['2'], SORT_ASC, $input);	
		// }
        // $input = collect($input);


		// recalculate amount for each method base on price esca factor 
		foreach ($input as $index => $v)
		{
			// $input[$index][71] = null;
			if (!isset($v[71]) && $v[0] == 0)
			{
				for ($i = 0; $i <= 4; $i++)
				{
					$uc = $v[66 + $i] * pow((1 + $work_planning->price_esca_factor/100), $i);
					$input[$index][66 + $i] = intval($uc);
				}
			}
			else
			{
				continue;
			}
		}
        // year 1
        $budget = $work_planning->year_1 * 1000000000;
        foreach ($input as $index => $v) 
        {
        	if (!isset($v[71]) && $v[0] == 0 && $v[66] > 0)
        	{
        		if ($budget - $v[66] < 0)
        		{
        			break;
        		}
        		$budget-= $v[66];
        		$input[$index][71] = 1;
        	}
        	else
        	{
        		continue;
        	}
        }
        // year 2
        $budget += $work_planning->year_2 * 1000000000;

        foreach ($input as $index => $v) 
        {
        	if (!isset($v[71]) && $v[0] == 0 && $v[67] > 0)
        	{
        		if ($budget - $v[67] < 0)
        		{
        			break;
        		}
        		$budget-= $v[67];	
        		$input[$index][71] = 2;
        	}
        	else
        	{
        		continue;
        	}
        }
        // year 3
        $budget += $work_planning->year_3 * 1000000000;
        foreach ($input as $index => $v) 
        {
        	if (!isset($v[71]) && $v[0] == 0 && $v[68] > 0)
        	{
        		if ($budget - $v[68] < 0)
        		{
        			break;
        		}
        		$budget-= $v[68];
        		$input[$index][71] = 3;
        	}
        	else
        	{
        		continue;
        	}
        }
        
        // year 4
        $budget += $work_planning->year_4 * 1000000000;
        foreach ($input as $index => $v) 
        {
        	if (!isset($v[71]) && $v[0] == 0 && $v[69] > 0)
        	{
        		if ($budget - $v[69] < 0)
        		{
        			break;
        		}
        		$budget-= $v[69];
        		$input[$index][71] = 4;
        	}
        	else
        	{
        		continue;
        	}
        	
        }

        // year 5
        $budget += ($work_planning->total_budget - $work_planning->year_1 - $work_planning->year_2 - $work_planning->year_3 - $work_planning->year_4) * 1000000000;
        
        foreach ($input as $index => $v) 
        {
        	if (!isset($v[71]) && $v[0] == 0 && $v[70] > 0)
        	{
        		if ($budget - $v[70] < 0)
        		{
        			break;
        		}
        		$budget-= $v[70]; 		
        		$input[$index][71] = 5;
        	}
        	else
        	{
        		continue;
        	}
        }

        $open = fopen(public_path("application/process/work_planning/" . $work_planning->id . "/data/input_final.csv"), 'w+');
        foreach ($input as $row)
        {               
            fputcsv($open, $row);
        }   
        fclose($open);
	}

	function getTotalCost($session_id, $list)
	{
		if ($list == 0)
		{
			$input_file = 'input_final';
		}
		else if ($list == 1)
		{
			$input_file = 'input_proposal';
		}
		else
		{
			$input_file = 'input_planned';
		}
		
		$source = "application/process/work_planning/" . $session_id . "/data/" . $input_file . ".csv";
		$input = array();

		$dataset = [
			1 => 0,
			2 => 0,
			3 => 0,
			4 => 0,
			5 => 0
		];

		Helper::getRowDataChunk($source, 3000, function($chunk, &$handle, $iteration) use(&$dataset) {
            $i = explode(',', $chunk);

            for ($j = 1; $j <= count($i) - 1; $j++)
            {
                $i[$j] = str_replace(array("\n", "\r", "\r\n", "\n\r"), '', $i[$j]);
            }
            if (count($i) < 5)
            {
            	return;
            }
            if ($i[0] != 0 || !isset($i[71]) || !in_array(intval($i[71]), [1, 2, 3, 4, 5])) return;
    		$dataset[intval($i[71])]+= $i[71 - (6 - intval($i[71]))];
        }, 99999999);

        foreach ($dataset as $key => $value) 
        {
        	$dataset[$key] = number_format(0.001 * $value);
        }
        return $dataset;
	}

	function findCriteria(Request $request)
	{
		$data = [
			['name' => '', 'value' => '0'],
			['name' => 'MCI', 'value' => '1'],
			['name' => trans('wp.road_class'), 'value' => '2'],
			['name' => trans('wp.Traffic_Volume'), 'value' => '3'],
		];
		foreach ($data as $k => $v)
		{
			if ($v['value'] == $request->first_priority)
			{
				unset($data[$k]);
			}
			if (isset($request->second_priority) && !empty($request->second_priority))
			{
				if ($v['value'] == $request->second_priority)
				{
					unset($data[$k]);
				}
			}
		}
		$data = array_values($data);
		return response()->json($data);
	}

	function postGenerate(Request $request)
	{
		\DB::beginTransaction();
		try
		{
			$data = tblWorkPlanning::findOrFail($request->session_id);
			$list = $request->list;
			if (isset($data))
			{
				$data->{"excel_flg_{$list}"} = 0;
				$data->save(); 

				$work_planning_excel = (new \App\Jobs\wp_excel_process_info($request->session_id, Auth::user()->id, $list))->onQueue('work_planning_excel');
        		dispatch($work_planning_excel);
				\DB::commit();
				return response()->json(array(
					'code' => 200,
					'description' => 'success',
				));
			}
			else
			{
				return response()->json(array(
					'code' => 901,
					'description' => 'error',
				));
			}
		}
		catch (\Epception $e)
		{
			\DB::rollBack();
			dd($e->getMessage());
		}
	}

	function getHistory()
	{
		$work_planning = tblWorkPlanning::with('organizations')->where('created_by', Auth::user()->id)->get();
		return Datatables::of($work_planning)
			->addColumn('candidate', function ($wp) {
				$path = public_path('application/process/work_planning/'. $wp->id .'/data/input_final.csv');
				if (file_exists($path))
				{
					return '<i class="fa fa-check" aria-hidden="true"></i>';
				}			
			})
			->addColumn('proposal', function ($wp) {
				$path = public_path('application/process/work_planning/'. $wp->id .'/data/input_proposal.csv');
				if (file_exists($path))
				{
					return '<i class="fa fa-check" aria-hidden="true"></i>';
				}
			})
			->addColumn('final', function ($wp) {
				$path = public_path('application/process/work_planning/'. $wp->id .'/data/input_planned.csv');
				if (file_exists($path))
				{
					return '<i class="fa fa-check" aria-hidden="true"></i>';
				}
			})
			->addColumn('action', function ($wp) {
				$actions = [];
				$actions[] = \Form::lbButton(route('user.wp.history.view', [$wp->id]), 'GET', trans('wp.view'), ["class" => "btn btn-xs btn-warning"])->toHtml();
				if (\Helper::getMonthDiff(Carbon::now(), $wp->created_at) >= 1)
				{
					$actions[] = \Form::lbButton(
	                    route('user.wp.delete', [$wp->id]),
	                    'delete',
	                    trans('wp.delete'),
	                    [
	                        "class" => "btn btn-xs btn-danger",
	                        "onclick" => "return confirm('".trans("wp.are_you_sure?")."')"
	                    ]
	                )->toHtml();
				}
				return implode(' ', $actions);
			})
			->make(true);
	}

	function postProposal(Request $request)
	{
		try
		{
			$work_planning = tblWorkPlanning::findOrFail($request->session_id);
			if ($work_planning->status == 0)
			{
				$data = [];
				$file = public_path("application/process/work_planning/".$request->session_id."/data/input_final.csv");
				if (!file_exists($file))
				{
					return response()->json(array(
						'code' => 901,
						'description' => 'error',
					));
				}
				$fp = fopen($file, 'r');
				while ($line = fgetcsv($fp)) 
				{
					$data[] = $line;
				}
				fclose($fp);

				$fp = fopen("../public/application/process/work_planning/" . $request->session_id . "/data/input_proposal.csv", 'w');
				foreach ($data as $row)
				{				
				    fputcsv($fp, $row);
				}	
				fclose($fp);
			}
			
			return response()->json(array(
				'code' => 200,
				'description' => 'success',
			));

		}
		catch (\Exception $e)
		{
			dd($e->getMessage());
		}
	}

	function postPlanned(Request $request)
	{
		try
		{
			$work_planning = tblWorkPlanning::findOrFail($request->session_id);
			if ($work_planning->status == 0)
			{
				$data = [];
				$file = public_path("application/process/work_planning/".$request->session_id."/data/input_proposal.csv");
				if (!file_exists($file))
				{
					return response()->json(array(
						'code' => 901,
						'description' => 'error',
					));
				}
				$fp = fopen($file, 'r');
				while ($line = fgetcsv($fp)) 
				{
					$data[] = $line;
				}
				fclose($fp);

				$fp = fopen("../public/application/process/work_planning/" . $request->session_id . "/data/input_planned.csv", 'w');
				foreach ($data as $row)
				{				
				    fputcsv($fp, $row);
				}	
				fclose($fp);
			}
			
			return response()->json(array(
				'code' => 200,
				'description' => 'success',
			));

		}
		catch (\Exception $e)
		{
			dd($e->getMessage());
		}
	}

	function postSavePlan(Request $request)
	{
		DB::beginTransaction();
		try
		{	
			$work_planning = tblWorkPlanning::findOrFail($request->session_id);
			if ($work_planning->status == 0)
			{
				for ($i = 0; $i <= 4; $i++)
				{
					$data = $this->_getCsvData($request->session_id, 4 + $i, 2);
					foreach ($data as $item)
					{
						$road_category = substr($item['section_id'], 2, 1);
						$road_number = substr($item['section_id'], 3, 3);
			    		$road_number_supplement = substr($item['section_id'], 6, 3);
			    		$branch_number = substr($item['section_id'], 9, 2);
			    		$direction = substr($item['section_id'], 11, 1);
			    		$lane_pos_number = $item['survey_lane'];
			    		//$number_of_lanes = substr($item['section_id'], 11, 1);
			    		$km_from = sprintf("%04d", $item['km_from']); 
			    		$m_from = sprintf("%05d", $item['m_from']);
			    		$section_id = $road_category . $road_number . $road_number_supplement . $branch_number . $direction . $lane_pos_number. '_' .$km_from . $m_from;	    		
			    	 	$branch_id = tblBranch::where('road_number', $road_number)
			    	 		->where('branch_number', $branch_number)
			    	 		->where('road_number_supplement', $road_number_supplement)
			    	 		->where('road_category', $road_category)
			    	 		->first()->id;
					 	$repair_method = mstRepairMethod::findOrFail($item[56 + $i]);
						$repair_classification = tblRClassification::findOrFail($repair_method->classification_id);
						$planned_section = new tblPlannedSection;
						$planned_section->section_id = $section_id;
						$planned_section->branch_id = $branch_id;
						$planned_section->sb_id = $item['sb_id'];
						$planned_section->km_from = $item['km_from'];
						$planned_section->m_from = $item['m_from'];
						$planned_section->km_to = $item['km_to'];
						$planned_section->m_to = $item['m_to'];
						$planned_section->section_length = $item['section_length'];
						$planned_section->direction = $item['direction'];
						$planned_section->lane_pos_no = $item['survey_lane'];
						$planned_section->planned_year = $item[31] + $i;
						$planned_section->repair_quantity = $item['selected_quantity_unit'];
						$amount = intval(preg_replace('/[^\d.]/', '', $item['amount']));
						$unit_cost = 0;
						if ($item['repair_quantity'] > 0 )
						{
							$unit_cost = intval($amount/$item['repair_quantity']);	
						}
						$planned_section->repair_cost = $amount;
						$planned_section->unit_cost = $unit_cost;
						$planned_section->repair_method_en = $repair_method->name_en;
						$planned_section->repair_method_vn = $repair_method->name_vn;
						$planned_section->repair_classification_en = $repair_classification->name_en;
						$planned_section->repair_classification_vn = $repair_classification->name_vn;
						$planned_section->created_by = Auth::user()->id;
						$planned_section->save();
					}
				}
				
				$work_planning->status = 1;
				$work_planning->updated_by = Auth::user()->id;
				$work_planning->save();
			}
			
			DB::commit();
			
			return response()->json(array(
				'code' => 200,
				'description' => 'success',
			));

		}
		catch (\Exception $e)
		{
			DB::rollBack();
			dd($e->getMessage());
		}
	}
}

