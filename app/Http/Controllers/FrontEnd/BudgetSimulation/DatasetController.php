<?php

namespace App\Http\Controllers\FrontEnd\BudgetSimulation;

use DB, Config, Helper, Auth, Excel, App, Session;
use Illuminate\Http\Request;
use Illuminate\Routing\Redirector;
use App\Http\Requests;
use App\Http\Controllers\Controller;
use App\Models\tblOrganization;
use App\Models\tblRoad;
use App\Models\tblSegment;
use App\Models\tblBudgetSimulation;
use App\Models\tblBudgetSimulationRoad;
use App\Models\tblBudgetSimulationOrganization;
use App\Models\tblDeterioration;
use App\Models\tblRepairMatrix;
use App\Models\mstRepairMethod;
use App\Models\tblRepairMatrixCell;
use App\Http\Requests\BudgetProcessRequest;

class DatasetController extends Controller
{
	/**
	 * check permission user
	 *
	 * use plugin rolePermissionLBR
	 * 
	 * @access	public
	 * @return	redirect url
	 */
	function __construct()
	{
		$this->middleware("dppermission:budget_simulation.budget_simulation");
	}
	
	/**
	 * Create display step init
	 *
	 *
	 * @access	public
	 * @return	view display step init
	 */
    public function index()
	{
		try
		{
			$year = tblDeterioration::allToComplete('year_of_dataset', 'desc', trans('budget.choose_year_of_datatset'));
			return view('front-end.budget_simulation.index')->with(array(
				'year' => $year
			));	
		}
		catch (\Exception $e)
		{
			dd($e->getMessage());
		}
		
	}
	
	/**
	 * Create display step dataset
	 *
	 * @param  varchar  $session_id uuid of session
	 * @access	public
	 * @return	view display step dataset
	 */
	public function getDatasetImport($session_id)
	{
		try
		{
			$budget_simulation = tblBudgetSimulation::find($session_id);

			// if (isset($budget_simulation->simulation_term))
			// {
			// 	\Session::put("history-{$session_id}", 1);
			// }

			$text_region = $budget_simulation->getInfoOrganization();
			$text_road = $budget_simulation->getInfoRoad();
			$text_year = $budget_simulation->year;
			
			$source = "application/process/budget_simulation/$session_id/data/budget_sim.csv";
			$header = Helper::getHeaderJqfrid($source);
			$col_model = Helper::getColModelJqgird($source);
			$csv_data = $this->_getDatasetCsvData($source, $col_model);
			
			return view('front-end.budget_simulation.data')->with(array(
				'text_region' => $text_region,
				'text_road' => $text_road,
				'text_year' => $text_year,
				'col_name' => json_encode($header),
				'json_data' => json_encode($csv_data),
				'col_model' => json_encode($col_model),
				'session' => $session_id,
				'next' => route('user.budget.get.repairmethod', array('session_id' => $session_id)),
				'history_flg' => $this->_checkIsHistory($budget_simulation)
			));	
		}
		catch (\Exception $e)
		{
			dd($e->getMessage());
		}	
	}

	private function _checkIsHistory($budget_simulation)
	{
		// if (isset($budget_simulation->status) || $budget_simulation->output_0_flg != 0 || $budget_simulation->output_1_flg != 0 || $budget_simulation->output_2_flg != 0 || $budget_simulation->output_3_flg != 0 )
		// {
		// 	return true;
		// }
		// return false;
		if (\Session::has("history-" . $budget_simulation->id))
		{
			return true;
		}
		return false;
	}

	function getHistory()
	{
		return view('front-end.budget_simulation.history');
	}

	function getHistoryDetail($session_id)
	{
		\Session::put("history-{$session_id}", 1);
		return redirect()->route('user.budget.get.scenario_tab', [$session_id]);
	}

	/**
	 * Create repair method csv, input/repair/list_1,2,3.csv
	 *
	 * @param  varchar  $session_id uuid of session
	 * @access	public
	 * @return	view display step repair method
	 */
	public function getListRepairMethod($session_id)
	{
		\DB::beginTransaction();
		try
		{
			$budget_simulation = tblBudgetSimulation::find($session_id);
			$history_flg = $this->_checkIsHistory($budget_simulation);

			if (!$history_flg || !isset($budget_simulation->default_repair_matrix_id))
			{
				$repair_matrix = tblRepairMatrix::find(1);
				$budget_simulation->default_repair_matrix_id = $repair_matrix->id;
				$budget_simulation->updated_by = \Auth::user()->id;
				$budget_simulation->save();
				
				$this->_createRepairMethodCsv($budget_simulation, 1);
				$this->_createRepairMethodCsv($budget_simulation, 2);
				$this->_createRepairMethodCsv($budget_simulation, 3);
				\DB::commit();
			}
			
			return redirect('/user/budget_simulation/repair_matrix/' . $session_id);
		}
		catch (\Exception $e)
		{
			dd($e);
		}
	}

	/**
	 * $budget_simulation tblBudgetSimulation object
	 * $pavement_type int
	 */
	private function _createRepairMethodCsv($budget_simulation, $pavement_type)
	{
		$data_repair_method = array();
		$repair_methods = \App\Models\mstRepairMethod::with('costs')
			// ->whereIn('classification_id', [2, 3])
			->whereNull('exclude_flg')
			->where('pavement_type', $pavement_type)
			->orderBy('zone_id')
			->get();
		$name = \App::isLocale('en') ? 'name_en' : 'name_vn';
		
		$index = 1;
		foreach ($repair_methods as $key => $method)
		{
			// if ($method->costs->count() == 0)
			// {
			// 	// dd($method);
			// 	continue;
			// }
			$data_repair_method[] = array(
				$index,
				$method->id,
				$method->classification_id,
				intval(@$method->costs->where('organization_id', 1)->first()->cost),
				intval(@$method->costs->where('organization_id', 2)->first()->cost),
				intval(@$method->costs->where('organization_id', 3)->first()->cost),
				intval(@$method->costs->where('organization_id', 4)->first()->cost),
				0,
				0,
				0,
				0,
				NULL,
				NULL,
				$method->$name,
			);
			$index++;
		}
		
		$count = count($data_repair_method);
		$source = "application/process/budget_simulation/{$budget_simulation->id}/input/repair";
		Excel::create('list_' . $pavement_type, function($excel) use($data_repair_method, $count) {
			$excel->sheet('list', function($sheet) use($data_repair_method, $count) {
				for ($i = 1; $i <= $count ; $i++)
				{ 
					$sheet->row($i, $data_repair_method[$i - 1]);					
				}
		    });
		})->store('csv', $source);
	}

	private function _getAllRepairMethods($history_flg, $budget_simulation)
    {
        $lang = \App::isLocale('en') ? 'en' : 'vn';
        if ($history_flg && isset($budget_simulation->repair_methods_cache))
        {
        	$records = json_decode($budget_simulation->repair_methods_cache);
        }
        else
        {
	        $records = \App\Models\mstRepairMethod::select('id', 'zone_id', 'pavement_type', 'code', 'name_en', 'name_vn')
	        	// ->whereIn('classification_id', [2, 3])
	        	->whereNull('exclude_flg')
	            ->orderBy('zone_id')
	            ->get();
	    }
        $data = [];
        foreach ($records as $index => $r) 
        {
            $data[] = [
                'id' => $r->id,
                'color' => $r->code,
                'name' => $r->{"name_{$lang}"},
                'pavement_type' => $r->pavement_type,
                'zone_id' => $r->zone_id
            ];
        }
        return $data;
    }

    private function _getSavedMatrix($crack_rank, $rut_rank, $repair_matrix)
    {
        $structure = [
            0 => [
                1, 2, 3, 4
            ],
            1 => [
                1, 2, 3, 4, 5, 6
            ]
        ];
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

        $latest_matrix_chk = tblRepairMatrixCell::where('repair_matrix_id', $repair_matrix->id)
			->where('user_id', \Auth::user()->id)
			->where('target_type', 1)
			->count();
		$info_matrix;
		if ($latest_matrix_chk > 0)
		{
			$info_matrix = tblRepairMatrixCell::where('repair_matrix_id', $repair_matrix->id)
				->where('user_id', \Auth::user()->id);
		}
		else
		{
			$info_matrix = tblRepairMatrixCell::where('repair_matrix_id', $repair_matrix->id)
				->whereNull('user_id');
		}

        $info_matrix = $info_matrix->with([
                'crackValue',
                'rutValue',
                'repairMethodValue',
                'roadTypeValue',
                'roadClassValue',
                'surfaceValue'
            ])
            ->get();

        foreach ($info_matrix as $i) 
        {
            $dataset[$i->roadTypeValue->value][$i->roadClassValue->value][$i->surfaceValue->value][$i->row][$i->column] = $i->repairMethodValue->value;
        }
        return $dataset;
    }

    private function _getRestrictZone($repair_matrix)
    {
        $zone_repair_matrix = tblRepairMatrix::where('type', $repair_matrix->type)
            ->where('id', '<>', $repair_matrix->id)
            ->first();

        $dataset = [];
        $info_matrix = tblRepairMatrixCell::where('repair_matrix_id', $zone_repair_matrix->id)
            ->whereNull('user_id')
            ->with([
                'crackValue',
                'rutValue',
                'repairMethodValue',
                'roadTypeValue',
                'roadClassValue',
                'surfaceValue'
            ])
            ->get();

        foreach ($info_matrix as $i) 
        {
            $dataset[$i->roadTypeValue->value][$i->roadClassValue->value][$i->surfaceValue->value][$i->row][$i->column] = $i->repairMethodValue->value;
        }
        return $dataset;

    }
	
	/**
	 * Create display step repair matrix
	 *
	 * @param  varchar  $session_id uuid of session
	 * @access	public
	 * @return	view display step repair matrix
	 */
	public function getRepairMatrix($session_id)
	{
		try
		{
			$budget_simulation = tblBudgetSimulation::find($session_id);

			if (!isset($budget_simulation->default_repair_matrix_id)) 
			{
				return redirect()->route('user.budget.dataset_import', ['session_id' => $session_id]);
			}

			$history_flg = $this->_checkIsHistory($budget_simulation);

			$text_region = $budget_simulation->getInfoOrganization();
			$text_road = $budget_simulation->getInfoRoad();
			$text_year = $budget_simulation->year;

			$repair_matrix = tblRepairMatrix::find($budget_simulation->repairMatrix->id);
            $crack_ranks = \App\Models\tblConditionRank::where('target_type', 1)->orderBy('rank')->get();
            $rut_ranks = \App\Models\tblConditionRank::where('target_type', 2)->orderBy('rank')->get();
            $road_category = \App\Models\mstRoadCategory::whereIn('code_id', [0, 1])->get();
            $road_class = \App\Models\mstRoadClass::whereIn('code_id', [1, 2, 3, 4, 5, 6])->get();
            $pavement_type = \App\Models\mstSurface::whereIn('code_id', [1, 2, 3])->get();

            $repair_methods = $this->_getAllRepairMethods($history_flg, $budget_simulation);
            
            $matrix = $history_flg ? json_decode($budget_simulation->matrix_cache, true) : $this->_getSavedMatrix($crack_ranks->count(), $rut_ranks->count(), $repair_matrix);
            $saved_zone = $this->_getRestrictZone($repair_matrix);

            return view('front-end.budget_simulation.repair_matrix_setup', [
                'repair_matrix_id' => $repair_matrix->id,
                'repair_matrix_name' => trans('back_end.matrix_' . $repair_matrix->name),
                'crack_ranks' => $crack_ranks,
                'rut_ranks' => $rut_ranks,
                'road_category' => $road_category,
                'road_class' => $road_class,
                'pavement_type' => $pavement_type,
                'zones' => $repair_methods,
                'matrix' => $matrix,
                'saved_zone' => $saved_zone,
                'text_region' => $text_region,
				'text_road' => $text_road,
				'text_year' => $text_year,
				'session' => $session_id,
				'back' => route('user.budget.dataset_import', array('session_id' => $session_id)),
				'session' => $session_id,
				'history_flg' => $history_flg,
				'file_created' => file_exists(public_path("application/process/budget_simulation/$session_id/input/repair/0/1"))
            ]);
		}
		catch (\Exception $e)
		{
			dd($e);
		}
	}
	
	/**
	 * Create display step repair condition
	 *
	 * @param  varchar  $session_id uuid of session
	 * @access	public
	 * @return	view display step repair condition
	 */
	public function getRepairCondition($session_id)
	{
		$budget_simulation = tblBudgetSimulation::find($session_id);

		if (!file_exists(public_path("application/process/budget_simulation/$session_id/input/repair/0/1"))) 
		{
			return redirect()->route('user.budget.get.repairmatrix', ['session_id' => $session_id]);
		}

		$text_region = $budget_simulation->getInfoOrganization();
		$text_road = $budget_simulation->getInfoRoad();
		$text_year = $budget_simulation->year;
		
		$simulation_term = array(
			10 => 10,
			20 => 20,
			30 => 30,
			40 => 40,
			50 => 50,
		);
		
		$simulation_time = array(
			10 => 10,
			100 => 100,
			1000 => 1000,
			10000 => 10000,
		);
		
		// $current_risk = $budget_simulation->getCurrentRisk();
		
		return view('front-end.budget_simulation.repair_condition')->with(array(
			'text_region' => $text_region,
			'text_road' => $text_road,
			'text_year' => $text_year,
			'session' => $session_id,
			'simulation_term' => $simulation_term,
			'simulation_time' => $simulation_time,
			'back' => route('user.budget.get.repairmatrix', array('session_id' => $session_id)),
			'history_flg' => $this->_checkIsHistory($budget_simulation),
			'budget' => $budget_simulation
		));
	}

	private function _listScenario()
	{
		return [
			[
				'name' => trans('budget.scenario0'),
				'value' => 0
			],
			[
				'name' => trans('budget.scenario1'),
				'value' => 1
			],
			[
				'name' => trans('budget.scenario2'),
				'value' => 2
			],
			[
				'name' => trans('budget.scenario3'),
				'value' => 3
			]
		];
	}

	function getScenarioTab($session_id, Request $request)
	{
		try
		{
			$budget_simulation = tblBudgetSimulation::findOrFail($session_id);
			if (!isset($budget_simulation->simulation_term))
			{
				return redirect()->route('user.budget.get.repaircondition', ['session_id' => $session_id]);
			}

			if ($request->scenario)
			{
				\Session::flash('scenario', $request->scenario);
			}

			if (isset($budget_simulation->status))
			{
				return view('front-end.budget_simulation.loading')->with(array(
	                'timer' => '5000',
	                'id' => $session_id,
	                'in_process' => $budget_simulation->status
	            ));
			}

			$text_region = $budget_simulation->getInfoOrganization();
			$text_road = $budget_simulation->getInfoRoad();
			$text_year = $budget_simulation->year;

			$scenario = $this->_listScenario();
			return view('front-end.budget_simulation.scenario_tab', [
				'text_region' => $text_region,
				'text_road' => $text_road,
				'text_year' => $text_year,
				'scenario' => $scenario,
				'budget_simulation' => $budget_simulation,
				'history_flg' => $this->_checkIsHistory($budget_simulation),
				'lang' => App::isLocale('en') ? 'en' : 'vn'
			]);
		}
		catch (\Exception $e)
		{
			dd($e->getMessage());
		}
	}
	
	private function _getDatasetCsvData($source, $col_model)
	{
		$indexes = [];
		foreach ($col_model as $c) 
		{
			$indexes[] = $c['name'];
		}

		$data_table_load = [];

		Helper::getRowDataChunk($source, 1024, function($chunk, &$handle, $iteration) use (&$data_table_load, &$indexes) {
            $array_cake = explode(',', $chunk);
            for ($i = 1; $i <= count($array_cake) - 1; $i++)
            {
                $array_cake[$i] = $array_cake[$i];
            }
           	if (count($array_cake) < 5) return;
            $data_table_load[] = array_combine($indexes, $array_cake);
        }, 101);
        array_shift($data_table_load);
        return $data_table_load;
	}

	function checkFlg(Request $request)
	{
		try
		{
			$rec = tblBudgetSimulation::findOrFail($request->id);
			$in_process = $request->in_process;
			$total = 2 + 10;

			$passed = $rec->{'output_' . $in_process . '_flg'};

			$log_file = public_path("application/process/budget_simulation/" . $request->id . "/result.log");
			$row_count = count(file($log_file)) - 1;
			if ($row_count > 10) $row_count = 10;

			$passed+= $row_count;

			$percentage = round(100*$passed/$total);
			// if ($percentage == 0)
			// {
			// 	$percentage = 25;
			// }
			return $percentage . '%';
		}
		catch (\Exception $e)
		{
			dd($e->getMessage());
		}
	}

	function postScenarioProcess($session_id, BudgetProcessRequest $request)
	{
		\DB::beginTransaction();
		try
		{
			$rec = tblBudgetSimulation::findOrFail($session_id);

			$source = public_path("application/process/budget_simulation/$session_id/input/conditions");
			$scenario = $request->scenario;
			$budget_constraint = $request->budget_constraint;
			$target_risk_level = $request->target_risk_level;

			switch ($scenario) 
			{
				case '0':
					if ($rec->output_0_flg == 0)
					{
						$budget_simulation_output = (new \App\Jobs\cri_flg0($session_id))->onQueue('budget_simulation_output');
						dispatch($budget_simulation_output);

						$rec->status = 0;
						$rec->save();
					}
					break;
				case '1':
					// if ($rec->output_1_flg == 0)
					// {
						// save budget.csv
						
						Excel::create('budget', function($excel) use ($budget_constraint) {
							$excel->sheet('budget', function($sheet) use ($budget_constraint) {
								$sheet->row(1, array($budget_constraint));	
						    });
						})->store('csv', $source);
						dd($rec->toArray());
						$rec->budget_constraint = $budget_constraint;
						$rec->status = 1;
						$rec->output_1_flg = 0;
						$rec->save();

						$budget_simulation_output = (new \App\Jobs\cri_flg1($session_id))->onQueue('budget_simulation_output');
						dispatch($budget_simulation_output);
					// }
					break;
				case '2':
					if ($rec->output_2_flg == 0)
					{
						$rec->status = 2;
						$rec->save();

						$budget_simulation_output = (new \App\Jobs\cri_flg2($session_id))->onQueue('budget_simulation_output');
						dispatch($budget_simulation_output);
					}
					break;
				case '3':
					// if ($rec->output_3_flg == 0)
					// {
						// save rick.csv
						Excel::create('risk', function($excel) use ($target_risk_level) {
							$excel->sheet('risk', function($sheet) use ($target_risk_level) {
								$sheet->row(1, array($target_risk_level));	
						    });
						})->store('csv', $source);

						$rec->target_risk = $target_risk_level;
						$rec->status = 3;
						$rec->output_3_flg = 0;
						$rec->save();	

						$budget_simulation_output = (new \App\Jobs\cri_flg3($session_id))->onQueue('budget_simulation_output');
						dispatch($budget_simulation_output);
					// }
					break;
				default:
					# code...
					break;
			}


			chdir(public_path('application/process/budget_simulation/' . $session_id . '/'));
            $re = shell_exec('> result.log');

			\DB::commit();
			return back()->with('scenario', $scenario);
		}
		catch (\Exception $e)
		{
			\DB::rollBack();
			dd($e->getMessage());
		}
	}

	function getChartData(Request $request)
	{
		$id = $request->id;
		$scenario = $request->scenario;
		$type = $request->type;
		$labels = [];
		$bar_chart_data = [];

		switch ($type) {
			case 'RCNR':
				$file = public_path("application/process/budget_simulation/{$id}/output{$scenario}/cost.csv");
				$data = Helper::readCsvToArray($file);
				
				for ($i = 1; $i < count($data); $i++) 
				{ 
					$labels[] = $data[$i][0];
				}

				for ($j = 2; $j < count($data[0]); $j++) 
				{
					if (!isset($bar_chart_data[$data[0][$j]]))
					{
						$bar_chart_data[$data[0][$j]] = [];
					}
					for ($i = 1; $i < count($data); $i++) 
					{
						$bar_chart_data[$data[0][$j]][] = $data[$i][$j]/1000000000;
					}
				}

				$file = public_path("application/process/budget_simulation/{$id}/output{$scenario}/risk.csv");
				$data = Helper::readCsvToArray($file);
				$risk = [];

				for ($i = 1; $i < count($data); $i++) 
				{
					$risk[] = 100 * $data[$i][1];
				}
				return [
					'labels' => $labels,
					'bar_chart_data' => $bar_chart_data,
					'risk' => $risk
				];
				break;
			case 'RL':
				$file = public_path("application/process/budget_simulation/{$id}/output{$scenario}/length.csv");
				$data = Helper::readCsvToArray($file);
				
				for ($i = 1; $i < count($data); $i++) 
				{ 
					$labels[] = $data[$i][0];
				}

				for ($j = 2; $j < count($data[0]); $j++) 
				{
					if (!isset($bar_chart_data[$data[0][$j]]))
					{
						$bar_chart_data[$data[0][$j]] = [];
					}
					for ($i = 1; $i < count($data); $i++) 
					{
						$bar_chart_data[$data[0][$j]][] = $data[$i][$j]/1000;
					}
				}

				return [
					'labels' => $labels,
					'bar_chart_data' => $bar_chart_data
				];
				break;
			case 'CCT':
				$file = public_path("application/process/budget_simulation/{$id}/output{$scenario}/con_crack.csv");
				$data = Helper::readCsvToArray($file);
				$source_csv = public_path("application/process/budget_simulation/{$id}/rank/rank_c.csv");
				$rank_csv = Helper::readCsvToArray($source_csv);

				$crack_rank = [];
				for ($i = 1; $i < count($data[0]); $i++)
				{
					$crack_rank[$data[0][$i]] = \Helper::convertConditionInforToText(floatval(@$rank_csv[$i-2][0]), @$rank_csv[$i-1][0], 'C');
				}
				
				for ($i = 1; $i < count($data); $i++) 
				{ 
					$labels[] = $data[$i][0];
				}

				for ($j = count($data[0]) - 1; $j >= 1; $j--) 
				{
					if (!isset($bar_chart_data[$data[0][$j]]))
					{
						$bar_chart_data[$crack_rank[$data[0][$j]]] = [];
					}
					for ($i = 1; $i < count($data); $i++) 
					{
						$bar_chart_data[$crack_rank[$data[0][$j]]][] = $data[$i][$j];
					}
				}

				$percentage = [];
				$totals = [];

				for ($i = 0; $i < count($labels); $i++) 
				{
					$totals[$i] = 0;
					foreach ($bar_chart_data as $b) 
					{	
						$totals[$i]+= $b[$i];
					}	
				}

				foreach ($bar_chart_data as $key => $b) 
				{
					foreach($b as $i => $hits) 
					{
					   	$percentage[$key][] = 100 * $hits / $totals[$i];
					}
				}

				return [
					'labels' => $labels,
					'area_chart_data' => $percentage
				];
				break;
			case 'RCT':
				$file = public_path("application/process/budget_simulation/{$id}/output{$scenario}/con_rut.csv");
				$data = Helper::readCsvToArray($file);
				$source_csv = public_path("application/process/budget_simulation/{$id}/rank/rank_r.csv");
				$rank_csv = Helper::readCsvToArray($source_csv);

				$crack_rank = [];
				for ($i = 1; $i < count($data[0]); $i++)
				{
					$crack_rank[$data[0][$i]] = \Helper::convertConditionInforToText(floatval(@$rank_csv[$i-2][0]), @$rank_csv[$i-1][0], 'R');
				}
				
				for ($i = 1; $i < count($data); $i++) 
				{ 
					$labels[] = $data[$i][0];
				}

				for ($j = count($data[0]) - 1; $j >= 1; $j--) 
				{
					if (!isset($bar_chart_data[$data[0][$j]]))
					{
						$bar_chart_data[$crack_rank[$data[0][$j]]] = [];
					}
					for ($i = 1; $i < count($data); $i++) 
					{
						$bar_chart_data[$crack_rank[$data[0][$j]]][] = $data[$i][$j];
					}
				}

				$percentage = [];
				$totals = [];

				for ($i = 0; $i < count($labels); $i++) 
				{
					$totals[$i] = 0;
					foreach ($bar_chart_data as $b) 
					{	
						$totals[$i]+= $b[$i];
					}	
				}

				foreach ($bar_chart_data as $key => $b) 
				{
					foreach($b as $i => $hits) 
					{
					   	$percentage[$key][] = 100 * $hits / $totals[$i];
					}
				}
				
				return [
					'labels' => $labels,
					'area_chart_data' => $percentage
				];
				break;
			default:
				# code...
				break;
		}
	}

	function postRemoveCustomization($session_id)
	{
		\DB::beginTransaction();
		try
		{
			$rec = tblBudgetSimulation::findOrFail($session_id);
			tblRepairMatrixCell::where('repair_matrix_id', $rec->default_repair_matrix_id)
        		->where('user_id', \Auth::user()->id)
        		->delete();
			\DB::commit();
			return back();
		}
		catch (\Exception $e)
		{
			\DB::rollBack();
			dd($e->getMessage());
		}
	}

	function deleteHistory($session_id)
	{
		\DB::beginTransaction();
		try
		{
			tblBudgetSimulation::destroy($session_id);
			\DB::commit();
			return back();
		}
		catch (\Exception $e)
		{
			\DB::rollBack();
			dd($e->getMessage());
		}	
	}

	function getExport($session_id, $case)
	{
		if (!in_array($case, [0, 1, 2, 3])) $case = 0;
		try
        {
            $public_dir = public_path('/application/process/budget_simulation/' . $session_id);
            $zip_file_name = 'Output_02_budget_cri_flg=' . $case . '.zip';
            $zip = new \ZipArchive;
            if ($zip->open($public_dir . '/' . $zip_file_name, \ZIPARCHIVE::CREATE) === TRUE) 
            {    
                $zip->addFile($public_dir . '/Output_02_budget_cri_flg=' . $case . '.xlsx', 'Output_02_budget_cri_flg=' . $case . '.xlsx');
                $zip->addFile($public_dir . '/data/budget_sim.csv', 'budget_sim.csv');
                $zip->close();
            }
            else
            {
                dd($zip->open($public_dir . '/' . $zip_file_name, \ZIPARCHIVE::CREATE));
            }
            $headers = array(
                'Content-Type' => 'application/octet-stream',
            );
            $filetopath = $public_dir . '/' . $zip_file_name;
            return response()->download($filetopath, $zip_file_name, $headers);
        }
        catch (\Exception $e)
        {
            dd($e->getMessage());
        }
	}
}
