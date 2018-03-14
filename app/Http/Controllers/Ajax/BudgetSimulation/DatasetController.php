<?php

namespace App\Http\Controllers\Ajax\BudgetSimulation;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\tblDeterioration;
use App\Models\tblBudgetSimulation;
use App\Models\tblBudgetSimulationRoad;
use App\Models\tblBudgetSimulationOrganization;
use App\Models\tblRoad;
use App\Models\tblRepairMatrix;
use App\Models\tblRepairMatrixCell;
use App\Models\tblRepairMatrixCellValue;
use App\Models\mstRoadCategory;
use App\Models\mstRoadClass;
use App\Models\mstRepairMethod;
use App\Models\tblOrganization;
use Auth, Helper, Session, Config, Excel, DB;
use Carbon\Carbon;
use Yajra\Datatables\Facades\Datatables;

class DatasetController extends Controller
{
	protected $surfaces = [];
	function __construct()
	{
		$records = \App\Models\mstSurface::all();
		foreach ($records as $r)
		{
			$this->surfaces[$r->code_name] = $r->code_id;
		}
	}
	/**
	 * get list RMB
	 *
	 * @param  Request  $request request of ajax
	 * @access  public
	 * @return	json  list RMB
	 */
    public function getListRegion(Request $request)
	{
		$list_road = tblDeterioration::getRegionByYearOfDataset($request->year);
		return response()->json($list_road);
	}
	
	/**
	 * get list road
	 *
	 * @param  Request  $request request of ajax
	 * @access  public
	 * @return	json  list road
	 */
	public function getListRoad(Request $request)
	{
		$list_road = \App\Models\tblBranch::getRouteByRmb($request->list_region);
		return response()->json($list_road);
	}
	
	/**
	 * get create session budget simulation
	 *
	 * @param  Request  $request request of ajax
	 * @access  public
	 * @return	json  code: 200-success, session_id
	 */
	public function createInit(Request $request)
	{
		\DB::beginTransaction();
		try
		{
			$user_id = Auth::user()->id;
		
			$budget_simulation = new tblBudgetSimulation;
			$budget_simulation->year = $request->year;
			$budget_simulation->created_by = $user_id;
			$budget_simulation->save();
			
			foreach ($request->list_road as $key => $value)
			{
				$bm_road = new tblBudgetSimulationRoad;
				$bm_road->budget_simulation_id = $budget_simulation->id;
				$bm_road->road_id = $value;
				$bm_road->created_by = $user_id;
				$bm_road->save();
			}
			
			foreach ($request->list_region as $key => $value)
			{
				$bm_road = new tblBudgetSimulationOrganization;
				$bm_road->budget_simulation_id = $budget_simulation->id;
				$bm_road->organization_id = $value;
				$bm_road->created_by = $user_id;
				$bm_road->save();
			}
			
			// copy forder of session

			$source = 'application/core/budget_simulation';
			$dest = 'application/process/budget_simulation/' . $budget_simulation->id;
			\Helper::recurseCopy($source, $dest);
			\Helper::chmodr('application/process/budget_simulation/' . $budget_simulation->id . '/', 0755, 0755);
			
			// PMS Dataset Module (Developing)
			$this->_importModuleDataset($budget_simulation, $request->list_region, $request->list_road);
			\DB::commit();
			return response()->json(array(
				'code' => 200,
				'session_id' => $budget_simulation->id,
			));	
		}
		catch (\Exception $e)
		{
			\DB::rollBack();
			dd($e);
		}
	}

	private function _getDetNoData($budget, $list_rmb)
	{
		$dataset = [];
		foreach ($list_rmb as $rmb_id) 
		{
			$rmb = tblOrganization::find($rmb_id);
			$dataset[$rmb->code_id] = [
				'crack' => [
					'det_no_1' => $this->_convertCsvToLookupArray($budget, $rmb->id, 'crack', 'det_no_1', $rmb->code_id),
					'det_no_2' => $this->_convertCsvToLookupArray($budget, $rmb->id, 'crack', 'det_no_2', $rmb->code_id)
				],
				'rut' => [
					'det_no_1' => $this->_convertCsvToLookupArray($budget, $rmb->id, 'rut', 'det_no_1', $rmb->code_id),
					'det_no_2' => $this->_convertCsvToLookupArray($budget, $rmb->id, 'rut', 'det_no_2', $rmb->code_id)
				]
			];
		}
		return $dataset;
	}

	private function _convertCsvToLookupArray($budget, $rmb_id, $type, $det_name, $jurisdiction_code)
	{
		$dataset = [];
		$det = tblDeterioration::where('year_of_dataset', $budget->year)
			->where('dataset_flg', 1)
			->where('organization_id', $rmb_id)
			->first();
		if ($det)
		{
			$src = 'application/process/deterioration/' . $det->id . '/' . $type . '/output5/' . $det_name . '.csv';
			$csv_data = \Helper::readCsvToArray($src);
			foreach ($csv_data as $c) 
			{
				$dataset+= [
					$c[0] => $c[1]
				];
			}
			// copy matrix file from deterioration
			$array_to_copy = ['matrix_10.csv', 'matrix_20.csv', 'matrix_30.csv'];
			foreach ($array_to_copy as $file) 
			{
				$source = 'application/process/deterioration/' . $det->id . '/' . $type . '/output6/' . $file;
				$dest = 'application/process/budget_simulation/' . $budget->id . '/det/' . $jurisdiction_code . '/' . $type . '/' . $file;
				\Helper::straightCopy($source, $dest);
			}

			return $dataset;
		}
		else
		{
			return [];
		}
	}

	private function _getListSegmentByRegionRoadAndYear($pms_year, $list_rmb, $list_road)
	{
		// \DB::enableQueryLog();
		return \App\Models\tblSegmentHistory::whereRaw("(updated_at is null or YEAR(updated_at) <= $pms_year)")
					->whereHas('latestSB', function($q) use($pms_year, $list_rmb) {
		                    $q->whereRaw("(updated_at is null or YEAR(updated_at) <= $pms_year)")
		                    	->whereIn('parent_id', $list_rmb);
		                })
					->whereHas('branch', function($q) use($list_road) {
		                    $q->whereIn('branch_id', $list_road);
		                })
		            ->groupBy('segment_id')
		            ->select('segment_id')
		            ->pluck('segment_id')
		            ->toArray();
        // dd(\DB::getQueryLog());
	}

	private function _importModuleDataset($budget_simulation, $list_rmb, $list_road)
	{
		$det_no_data = $this->_getDetNoData($budget_simulation, $list_rmb);

		$surfaces = $this->surfaces;

		$dataset = ['Latest_Cracking_ratio', 'Latest_Rutting_max', 'Latest_Condition_Year', 'Section_Length', 'Pavement_Width', 'Latest_Pavement_type', 'Road_Category_ID', 'Road_Class_ID', 'TV_Total', 'TV_Heavy', 'Region', 'Route_ID', 'det_no_c', 'det_no_r'];
		$segment_by_year = $this->_getListSegmentByRegionRoadAndYear($budget_simulation->year, $list_rmb, $list_road);

		$source = "application/process/budget_simulation/{$budget_simulation->id}/data/budget_sim.csv";
		if (!file_exists("application/process/budget_simulation/" . $budget_simulation->id . "/data")) 
		{
		    mkdir("application/process/budget_simulation/" . $budget_simulation->id . "/data", 0755, true);
		}
		$open = fopen($source, 'w');
		fputcsv($open, $dataset);

		\App\Models\tblPMSDatasetInfo::whereIn('case', [0, 1, 2, 3, 4, 5, 6])
			->has('sectioning')
			->with('sectioning')
			->where('year_of_dataset', $budget_simulation->year)
			->whereNotNull('latest_condition_year')
			->whereNotNull('latest_condition_month')
			->whereNotNull('latest_pavement_type')
			->whereNotNull('latest_cracking_ratio')
			->whereNotNull('latest_rutting_max')
			->whereNotNull('latest_IRI')
			->whereBetween('latest_cracking_ratio', [0, 100])
			->whereBetween('latest2_cracking_ratio', [0, 100])
			// ->whereIn('latest_pavement_type', ['AC', 'BST', 'CC'])
			->whereIn('segment_id', $segment_by_year)
			->chunk(2000, function($records) use($surfaces, $det_no_data, &$open) {
				foreach ($records as $rec) 
				{
					$road_category = substr($rec->section_id2, 2, 1);
		    		$jurisdiction_code = substr($rec->section_id2, 0, 2);
		    		if (!isset($det_no_data[$jurisdiction_code]))
		    		{
		    			continue;
		    		}
		    		if (!isset($rec->road_class_id) || $rec->road_class_id == 0)
		    		{
		    			continue;
		    		}
		    		$det_no_c = null;
		    		$det_no_r = null;

		    		if ($rec->latest_pavement_type == 'AC')
		    		{
		    			$det_no_c = \Helper::vlookup($rec->route_id, $det_no_data[$jurisdiction_code]['crack']['det_no_1']);
		    			$det_no_r = \Helper::vlookup($rec->route_id, $det_no_data[$jurisdiction_code]['rut']['det_no_1']);
		    		}
		    		else if ($rec->latest_pavement_type == 'BST')
		    		{
		    			$det_no_c = \Helper::vlookup($rec->route_id, $det_no_data[$jurisdiction_code]['crack']['det_no_2']);
		    			$det_no_r = \Helper::vlookup($rec->route_id, $det_no_data[$jurisdiction_code]['rut']['det_no_2']);
		    		}
		    		else
		    		{
		    			$det_no_c = 1;
		    			$det_no_r = 1;
		    		}
		    		if ($rec->case == 5)
		    		{
		    			$rec->latest_cracking_ratio = 0;
						$rec->latest_rutting_max = 0;
		    		}
					$dataset = [
						$rec->latest_cracking_ratio,
						$rec->latest_rutting_max,
						$rec->latest_condition_year,
						$rec->section_length,
						$rec->pavement_width,
						isset($surfaces[$rec->latest_pavement_type]) ? $surfaces[$rec->latest_pavement_type] : -1,
						$road_category,
						(!isset($rec->road_class_id) || $rec->road_class_id == 0) ? 99 : $rec->road_class_id,
						$rec->total_traffic_volume,
						$rec->heavy_traffic,
						0.1*$jurisdiction_code,
						$rec->route_id,
						isset($det_no_c) ? $det_no_c : 1,
						isset($det_no_r) ? $det_no_r : 1
					];
					fputcsv($open, $dataset);
				}
			});
		fclose($open);
	}
	
	/**
	 * get list road category
	 *
	 * @param  Request  $request request of ajax
	 * @access  public
	 * @return	json  list road category
	 */
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
		
		return response()->json(array(
			'code' => 200,
			'data' => $road_types,
			'surface_text' => $surface_text,
			'surface_type' => $surface_type,
			'color' => $color,
		));
	}
	
	/**
	 * get list road class
	 *
	 * @param  Request  $request request of ajax
	 * @access  public
	 * @return	json  list road class
	 */
	public function getListRoadClass(Request $request)
	{
		$result = array();
		$user_id = Auth::user()->id;
		$method = $request->repair_method;
		$road_type = $request->road_type;
		
		$road_class = mstRoadClass::allOptionToAjax(FALSE, $road_type);
		@$road_class[0]['selected'] = TRUE;
		
		return response()->json(array(
			'code' => 200,
			'data' => $road_class,
		));
	}
	
	/**
	 * get matrix default with value of cell
	 *
	 * @param  Request  $request request of ajax
	 * @access  public
	 * @return	json  matrix default with value of cell
	 */
	public function getConditionRank(Request $request)
	{
		$user_id = Auth::user()->id;
		$repair_method = $request->repair_method;
		$road_type = $request->road_type;
		$road_class = $request->road_class;

		$method = mstRepairMethod::find($repair_method);
		$surface = $method->pavement_type;
		// $condition_rank = tblBudgetSimulation::find($request->session)->repairMatrix->condition_rank;
		
		$repair_matrix = null;
		$latest_matrix_chk = tblRepairMatrixCell::where('repair_matrix_id', $request->repair_matrix)
			->where('user_id', $user_id)
			->where('target_type', 1)
			->count();
		if ($latest_matrix_chk > 0)
		{
			$repair_matrix = tblRepairMatrixCell::where('repair_matrix_id', $request->repair_matrix)
				->where('user_id', $user_id)
				->where('target_type', 1);
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
	
	/**
	 * create the corresponding matrix and value cells
	 *
	 * @param  Request  $request request of ajax
	 * @access  public
	 * @return	json  code 200 - success
	 */
	public function createRepairMatrix(Request $request)
	{
		// \DB::beginTransaction();
		// try
		// {
		// 	$flag = FALSE;
		// 	$user_id = Auth::user()->id;
		// 	$method = $request->repair_method;
		// 	$road_type = $request->road_type;
		// 	$road_class = $request->road_class;
			
		// 	$check_last_repair_matrix = tblRepairMatrixCell::where('repair_matrix_id', $request->repair_matrix)
		// 		->where('user_id', $user_id)
		// 		->with(array(
		// 			'repairMethodCellValue' => function($query) use ($method) {
		// 				$query->where('parameter_id', 0)
		// 					->where('value', $method);
		// 			},
					
		// 			'roadTypeCellValue' => function($query) use ($road_type) {
		// 				$query->where('parameter_id', 1)
		// 					->where('value', $road_type);
		// 			},
					
		// 			'roadClassCellValue' => function($query) use ($road_class) {
		// 				$query->where('parameter_id', 2)
		// 					->where('value', $road_class);
		// 			},										
		// 		))
		// 		->get();
		// 	foreach ($check_last_repair_matrix as $cell)
		// 	{
		// 		if (count($cell->repairMethodCellValue->toArray()) > 0 && count($cell->roadTypeCellValue->toArray()) > 0 && count($cell->roadClassCellValue->toArray()) > 0)
		// 		{
		// 			// foreach ($cell->repairMatrixCellValue as $r)
		// 			// {
		// 				// $old = tblRepairMatrixCellValue::where('repair_matrix_cell_id', $r->repair_matrix_cell_id)->delete();
		// 			// }
					
		// 			$cell->delete();
		// 		}
		// 		else
		// 		{
		// 			$flag = TRUE;
		// 		}
		// 		// $cell->repairMatrixCellValue()->delete();
		// 	}
			
		// 	foreach ($request->data as $method => $road_types)
		// 	{
		// 		if (is_array($road_types))
		// 		{
		// 			$value_method = $method;
		// 			foreach ($road_types as $road_type => $road_class)
		// 			{
		// 				if (is_array($road_class))
		// 				{
		// 					$value_road_type = $road_type;
		// 					foreach ($road_class as $class => $matrices)
		// 					{
		// 						if(is_array($matrices))
		// 						{
		// 							$value_road_class = $class;
		// 							foreach ($matrices as $row => $cell)
		// 							{
		// 								foreach ($cell as $col => $value)
		// 								{
		// 									if (is_array($value))
		// 									{
		// 										if ($flag)
		// 										{
		// 											$delete = tblRepairMatrixCell::where('repair_matrix_id', $request->repair_matrix)
		// 																		->where('user_id', $user_id)
		// 																		->with(array(
		// 																			'roadTypeCellValue' => function($query) use ($value_road_type) {
		// 																				$query->where('parameter_id', 1)
		// 																					->where('value', $value_road_type);
		// 																			},
																					
		// 																			'roadClassCellValue' => function($query) use ($value_road_class) {
		// 																				$query->where('parameter_id', 2)
		// 																					->where('value', $value_road_class);
		// 																			},										
		// 																		))
		// 																		->where('row', $row)
		// 																		->where('column', $col)
		// 																		->where('type', $request->surface_type)
		// 																		->get();
		// 											foreach ($delete as $cell)
		// 											{
		// 												if (count($cell->roadTypeCellValue->toArray()) > 0 && count($cell->roadClassCellValue->toArray()) > 0)
		// 												{
		// 													$cell->delete();
		// 												}
		// 											}
		// 										}
												
		// 										$cell = new tblRepairMatrixCell;
		// 										$cell->repair_matrix_id = $request->repair_matrix;
		// 										$cell->type = $request->surface_type;
		// 										$cell->row = $row;
		// 										$cell->column = $col;
		// 										$cell->user_id = $user_id;
		// 										$cell->created_by = $user_id;
		// 										$cell->target_type = 1;
		// 										$cell->save();
												
		// 										$cell_method = new tblRepairMatrixCellValue;
		// 										$cell_method->repair_matrix_cell_id = $cell->id;
		// 										$cell_method->parameter_id = 0;
		// 										$cell_method->value = $value_method;
		// 										$cell_method->created_by = $user_id;
		// 										$cell_method->save();
												
		// 										$cell_value_crack = new tblRepairMatrixCellValue;
		// 										$cell_value_crack->repair_matrix_cell_id = $cell->id;
		// 										$cell_value_crack->parameter_id = 5;
		// 										$cell_value_crack->value = $value[0].'-'.$value[1];
		// 										$cell_value_crack->created_by = $user_id;
		// 										$cell_value_crack->save();
												
		// 										if (isset($value[2]) && isset($value[3]))
		// 										{
		// 											$cell_value_rut = new tblRepairMatrixCellValue;
		// 											$cell_value_rut->repair_matrix_cell_id = $cell->id;
		// 											$cell_value_rut->parameter_id = 6;
		// 											$cell_value_rut->value = $value[2].'-'.$value[3];
		// 											$cell_value_rut->created_by = $user_id;
		// 											$cell_value_rut->save();
		// 										}
												
		// 										$cell_road_type = new tblRepairMatrixCellValue;
		// 										$cell_road_type->repair_matrix_cell_id = $cell->id;
		// 										$cell_road_type->parameter_id = 1;
		// 										$cell_road_type->value = $value_road_type;
		// 										$cell_road_type->created_by = $user_id;
		// 										$cell_road_type->save();
												
		// 										$cell_road_class = new tblRepairMatrixCellValue;
		// 										$cell_road_class->repair_matrix_cell_id = $cell->id;
		// 										$cell_road_class->parameter_id = 2;
		// 										$cell_road_class->value = $value_road_class;
		// 										$cell_road_class->created_by = $user_id;
		// 										$cell_road_class->save();
		// 									}
		// 									else
		// 									{
		// 										continue;	
		// 									}
		// 								}
		// 							}
		// 						}
		// 						else
		// 						{
		// 							continue;
		// 						}
		// 					}
		// 				}
		// 				else
		// 				{
		// 					continue;
		// 				}
		// 			}
		// 		}
		// 		else
		// 		{
		// 			continue;
		// 		}
		// 	}	
		// 	\DB::commit();
		// 	return response()->json(array(
		// 		'code' => 200,
		// 	));
		// }
		// catch (\Exception $e)
		// {
		// 	\DB::rollBack();
		// 	dd($e->getMessage());
		// }
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
                                $rec->target_type = 1;
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
		// 	->where('target_type', 1)
		// 	->delete();
		// $latest_matrix_chk = tblRepairMatrixCell::where('repair_matrix_id', $repair_matrix_id)
		// 		->where('user_id', Auth::user()->id)
		// 		->where('target_type', 1)
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
		// 		$clone->target_type = 1;
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
		// 			->where('user_id', Auth::user()->id)
		// 			->where('target_type', 1)
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
		// 			$rec->target_type = 1;
		// 			$rec->created_by = Auth::user()->id;
		// 			$rec->row = $row;
		// 			$rec->column = $col;
		// 			$rec->save();

		// 			$rec->saveRelation($indexes[0], $indexes[1], $indexes[2], $indexes[3], $indexes[4], $indexes[5]);
		// 		}
		// 	}
		// }
	}

	private function _getCacheMatrix($info_matrix, $structure, $crack_rank, $rut_rank, $methods)
	{
        $dataset = [];
        foreach ($structure as $road_type => $road_classes) 
        {
            foreach ($road_classes as $road_class) 
            {
                $dataset[$road_type][$road_class][1] = array_fill(0, $crack_rank, array_fill(0, $rut_rank, 0));
                $dataset[$road_type][$road_class][2] = array_fill(0, $crack_rank, array_fill(0, $rut_rank, 0));
                $dataset[$road_type][$road_class][3] = array_fill(0, $crack_rank, array_fill(0, 1, 0));
            }
        }

        foreach ($info_matrix as $i) 
        {
            $dataset[$i->roadTypeValue->value][$i->roadClassValue->value][$i->surfaceValue->value][$i->row][$i->column] = (int)@$methods[$i->repairMethodValue->value];
        }
        return $dataset;
	}

	/**
	 * create input/repair file .csv
	 *
	 * @param  Request  $request  request of ajax
	 * @access  public
	 * @return	json  code: 200-success or 901-eror
	 */
	public function createRepairMatrixCSV(Request $request)
	{
		\DB::beginTransaction();
		try
		{
			$user_id = Auth::user()->id;
			$session_id = $request->session;
			$matrix = $request->matrix;
			$nochange = $request->nochange;

			if ($nochange == 0)
			{
				// create latest repair matrix for user
				$this->_createCustomMatrix($matrix, $request->repair_matrix_id);
			}

			$data = null;
			$latest_matrix_chk = tblRepairMatrixCell::where('repair_matrix_id', $request->repair_matrix_id)
				->where('user_id', $user_id)
				->where('target_type', 1)
				->count();
			if ($latest_matrix_chk > 0)
			{
				$data = tblRepairMatrixCell::where('repair_matrix_id', $request->repair_matrix_id)
					->where('user_id', $user_id)
					->where('target_type', 1);
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
				// convert repair method id into order
				$budget_simulation = tblBudgetSimulation::find($session_id);
				$repair_methods_cache = [];
				$repair_methods = \App\Models\mstRepairMethod::whereNull('exclude_flg')
				// whereIn('classification_id', [2, 3])
					->orderBy('zone_id')
					->get();
				$methods = [];

				$method_key = [
					1 => 0,
					2 => 0,
					3 => 0
				];

				foreach ($repair_methods as $k => $r) 
				{
					$method_key[$r->pavement_type] = $method_key[$r->pavement_type] + 1;
					$methods[$r->id] = $method_key[$r->pavement_type];
					$repair_methods_cache[] = [
		                'id' => $r->id,
		                'code' => $r->code,
		                'name_en' => $r->name_en,
		                'name_vn' => $r->name_vn,
		                'pavement_type' => $r->pavement_type,
		                'zone_id' => $r->zone_id
		            ];
				}


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
					$repair_matrix_structure[$key] = (int)@$methods[$i->repairMethodValue->value];
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
				$budget_simulation->repair_methods_cache = json_encode($repair_methods_cache);
				$budget_simulation->matrix_cache = json_encode($this->_getCacheMatrix($info_matrix, $structure, $crack_rank, $rut_rank, $methods));
				$budget_simulation->save();

				foreach ($structure as $road_type => $road_classes) 
				{
					foreach ($road_classes as $road_class) 
					{
						$source = "application/process/budget_simulation/$session_id/input/repair/$road_type/$road_class";
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
		catch (\Exception $e)
		{
			\DB::rollBack();
			dd($e);
		}
	}
	
	/**
	 * create input/conditions file .csv
	 *
	 * @param  Request  $request  request of ajax
	 * @access  public
	 * @return	json code: 200-success or 901-eror
	 */
	public function createRepairCondition(Request $request)
	{
		\DB::beginTransaction();
		try
		{
			// $budget_constraint = $request->budget_constraint;
			// $target_risk_level = round($request->target_risk_level, 2);
			$simulation_term = $request->simulation_term;
			$simulation_time = $request->simulation_time;
			$session_id = $request->session_id;
			$source = "application/process/budget_simulation/$session_id/input/conditions";
			// save budget.csv
			// Excel::create('budget', function($excel) use ($budget_constraint) {
			// 	$excel->sheet('budget', function($sheet) use ($budget_constraint) {
			// 		$sheet->row(1, array($budget_constraint));	
			//     });
			// })->store('csv', $source);
			
			// save rick.csv
			// Excel::create('risk', function($excel) use ($target_risk_level) {
			// 	$excel->sheet('risk', function($sheet) use ($target_risk_level) {
			// 		$sheet->row(1, array($target_risk_level));	
			//     });
			// })->store('csv', $source);
			
			// save para1.csv
			$array = array(1, 2);
			Excel::create('para1', function($excel) use ($simulation_term, $simulation_time, $array) {
				$excel->sheet('para1', function($sheet) use ($simulation_term, $simulation_time, $array) {
					foreach ($array as $key => $value) {
						if ($value == 1) {
							$sheet->row($value, array($simulation_term));
						} else {
							$sheet->row($value, array($simulation_time));												
						}
					}
			    });
			})->store('csv', $source);

			$budget_simulation = tblBudgetSimulation::find($session_id);
			// $budget_simulation->budget_constraint = $budget_constraint;
			// $budget_simulation->target_risk = $target_risk_level;
			$budget_simulation->simulation_term = $simulation_term;
			$budget_simulation->simulation_time = $simulation_time;
			$budget_simulation->updated_by = Auth::user()->id;
			$budget_simulation->save();

			// create rank_c, rank_r
			$repair_matrix = tblRepairMatrix::findOrFail($budget_simulation->default_repair_matrix_id);
			// $condition_rank = Helper::convertJsonConditionRank($repair_matrix->condition_rank);
			$crack_ranks = \App\Models\tblConditionRank::where('target_type', 1)->orderBy('rank')->get();
        	$rut_ranks = \App\Models\tblConditionRank::where('target_type', 2)->orderBy('rank')->get();

			Excel::load("application/process/budget_simulation/$session_id/rank/rank_c.csv", function($reader) use ($crack_ranks) {
                $reader->sheet(0, function($sheet) use ($crack_ranks) {
                	$crack_rank = $crack_ranks->count();
                    for ($i = 1; $i < $crack_rank; $i++)
					{
						$sheet->row($i, [$crack_ranks->get($i-1)->to]);
					}
                });
            })->store('csv', "application/process/budget_simulation/$session_id/rank/");

            Excel::load("application/process/budget_simulation/$session_id/rank/rank_r.csv", function($reader) use ($rut_ranks) {
                $reader->sheet(0, function($sheet) use ($rut_ranks) {
                	$rut_rank = $rut_ranks->count();
                    for ($i = 1; $i < $rut_rank; $i++)
					{
						$sheet->row($i, [$rut_ranks->get($i-1)->to]);
					}
                });
            })->store('csv', "application/process/budget_simulation/$session_id/rank/");
				
			// $budget_simulation_output = (new \App\Jobs\cri_flg0($session_id))->onQueue('budget_simulation_output');
			// dispatch($budget_simulation_output);
			
			\DB::commit();	
			return response()->json(array(
				'code' => 200,
				'session_id' => $session_id
			));
		}
		catch (\Exception $e)
		{
			\DB::rollBack();
			dd($e);
		}
	}

	function getHistory()
	{
		$rec = tblBudgetSimulation::with('organizations', 'roads')
			->where('created_by', \Auth::user()->id)
        	->get();
      			           	
        return Datatables::of($rec)
        	// ->addColumn('organization_name', function($d) {
        	// 	$lang = \App::isLocale('en') ? 'en' : 'vn';
        	// 	return implode(', ', $d->organizations->pluck("name_{$lang}")->toArray());
        	// })
        	// ->addColumn('route_name', function($d) {
        	// 	$lang = \App::isLocale('en') ? 'en' : 'vn';
        	// 	return implode(', ', $d->roads->pluck("name_{$lang}")->toArray());
        	// })
        	->addColumn('progress0', function($d) {
        		$total = 2;
				$passed = $d->output_0_flg;
				$percent = round(100 * $passed/$total);
                return '<div class="progress progress-xs" data-progressbar-value="' . $percent . '"><div class="progress-bar"></div></div>';
            })
            ->addColumn('progress1', function($d) {
        		$total = 2;
				$passed = $d->output_1_flg;
				$percent = round(100 * $passed/$total);
                return '<div class="progress progress-xs" data-progressbar-value="' . $percent . '"><div class="progress-bar"></div></div>';
            })
            ->addColumn('progress2', function($d) {
        		$total = 2;
				$passed = $d->output_2_flg;
				$percent = round(100 * $passed/$total);
                return '<div class="progress progress-xs" data-progressbar-value="' . $percent . '"><div class="progress-bar"></div></div>';
            })
            ->addColumn('progress3', function($d) {
        		$total = 2;
				$passed = $d->output_3_flg;
				$percent = round(100 * $passed/$total);
                return '<div class="progress progress-xs" data-progressbar-value="' . $percent . '"><div class="progress-bar"></div></div>';
            })
			->addColumn('action', function ($rec) {
				$actions = [];
				$actions[] = \Form::lbButton(route('user.budget.history.detail', [$rec->id]), 'GET', trans('budget.view'), ["class" => "btn btn-xs btn-warning"])->toHtml();

				if (\Helper::getMonthDiff(Carbon::now(), $rec->created_at) >= 1)
				{
					$actions[] = view('custom.del_btn')->with([
							'route' => ['user.budget.history.delete', $rec->id], 
							'title' => trans('budget.delete'), 
							'confirm' => trans('budget.are_you_sure')
						])->render();
				}
				return implode(' ', $actions);
            })
		    ->make(true);
	}
}
