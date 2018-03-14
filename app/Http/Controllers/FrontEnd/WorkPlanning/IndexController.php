<?php  
namespace App\Http\Controllers\FrontEnd\WorkPlanning;

use Illuminate\Http\Request;
use DB, Config, Helper, Auth, Excel, App;
use App\Http\Requests;
use App\Http\Controllers\Controller;
use App\Models\tblOrganization;
use App\Models\tblSectiondataMH;
use App\Models\tblSectiondataRMD;
use App\Models\tblSectiondataTV;
use App\Models\tblRoad;
use App\Models\tblSegment; 
use App\Models\tblBudgetSimulation;
use App\Models\tblBudgetSimulationRoad;
use App\Models\tblBudgetSimulationOrganization;
use App\Models\tblDeterioration;
use App\Models\tblRepairMatrix;
use App\Models\mstRepairMethod;
use App\Models\tblConditionRank;
use App\Models\tblNotification;
use App\Models\tblWorkPlanning;
use App\Models\tblWorkPlanningOrganization;
use App\Models\tblRepairMatrixCell;
use App\Models\tblBranch;
use App\Models\mstRoadClass;
use Box\Spout\Common\Type;
use Box\Spout\Writer\WriterFactory;
use Box\Spout\Writer\Style\StyleBuilder;
use Box\Spout\Writer\Style\Color;

class IndexController extends Controller
{
	function __construct()
	{
		$this->middleware("dppermission:work_planning.work_planning");
	}
	/**
	  * display view select process
	  */ 
	public function index()
	{
		try
		{
			$year = tblDeterioration::allToComplete('year_of_dataset', 'desc', trans('wp.choose_year_of_datatset'));
			return view('front-end.work_planning.select_process', ['year'=> $year]);
		}
		catch (\Exception $e)
		{
			dd($e->getMessage());
		}
	}
	
	public function getDisplayData($session_id)
	{
		$work_planning = tblWorkPlanning::find($session_id); 
		if (Auth::user()->id != $work_planning->created_by)
		{
			return redirect()->back();
		}
		$text_region = $work_planning->getInfoOrganization($session_id);
		$text_year = $work_planning->year;
		return view('front-end.work_planning.display_data', [
			'session_id' => $session_id,
			'year'=> $text_year, 
			'region'=> $text_region 
		]);
	}

	public function getForecastIndex($session_id)
	{
		$work_planning = tblWorkPlanning::find($session_id);
		if (Auth::user()->id != $work_planning->created_by)
		{
			return redirect()->back();
		} 
		$text_region = $work_planning->getInfoOrganization($session_id);
		$text_year = $work_planning->year;
		$base_planning_year = $work_planning->base_planning_year;

		return view('front-end.work_planning.forecast_index', [
			'session_id' => $session_id,
			'year' => $text_year, 
			'region'=> $text_region,
			'base_planning_year' => $base_planning_year
		]);
	}

	public function getBasePlanning($session_id)
	{
		try
		{
			$work_planning = tblWorkPlanning::find($session_id);
			if (Auth::user()->id != $work_planning->created_by)
			{
				return redirect()->back();
			}
			if ($work_planning->default_repair_matrix_id == 0)
			{
				return redirect()->route('get.display.data', ['session_id' => $session_id]);
			}

			$text_region = $work_planning->getInfoOrganization($session_id);
			$text_year = $work_planning->year;
			$status = $work_planning->status;
			$base_planning_year = @$work_planning->base_planning_year;

			return view('front-end.work_planning.base_planning',[
			    'text_year' => $text_year, 
			    'text_region' => $text_region,
			    'session' => $session_id,
			    'status' => $status,
			    'base_planning_year' => $base_planning_year,
				'back' => route('get.display.data', array('session_id' => $session_id))
			]);
		}
		catch (\Exception $e)
		{
			dd($e->getMessage());
		}
	}
	
	// Repair Matrix Selection
	public function getListRepairMethod($session_id)
	{
		\DB::beginTransaction();
		try
		{
			$repair_matrix = tblRepairMatrix::find(2);
			$rec = tblWorkPlanning::find($session_id);
			if (Auth::user()->id != $rec->created_by)
			{
				return redirect()->back();
			}
			$rec->default_repair_matrix_id = $repair_matrix->id;
			$rec->updated_by = \Auth::user()->id;
			$rec->save();
			
			$data_repair_method = array();
			// $repair_methods = \App\Models\mstRepairMethod::with([
			// 		'costs' => function($q) use($rec){
			// 			$q->whereIn('organization_id', $rec->organizations()->get()->pluck('id')->toArray());
			// 		},
			// 		'classification',
			// 	])
			// 	->get();
			$repair_methods = \App\Models\mstRepairMethod::with([
				'costs' => function($q){
					$q->orderBy('organization_id', 'ASC');
				},
				'classification',
			])
			->get();
			foreach ($repair_methods as $key => $method)
			{
				if ($method->costs->count() == 0)
				{
					continue;
				}
				$data_repair_method[] = array(
					$method->id,
					$method->costs[0]->cost * 1000,
					$method->costs[1]->cost * 1000,
					$method->costs[2]->cost * 1000,
					$method->costs[3]->cost * 1000,
					$method->code,
					$method->name_vn,
					$method->name_en,
					$method->classification->name_vn,
					$method->classification->name_en,
					$method->unit_id
				);
				// $data_repair_method[] = array(
				// 	$method->id,
				// 	1000 * round(array_sum($method->costs->pluck('cost')->toArray())/$method->costs->count()),
				// 	$method->code,
				// 	$method->name_vn,
				// 	$method->name_en,
				// 	$method->classification->name_vn,
				// 	$method->classification->name_en,
				// 	$method->unit_id
				// );
			}
			
			// $count = count($data_repair_method);
			$source = public_path("application/process/work_planning/$session_id/input/repair/list.csv");
			\Helper::writeDataToCSV($source, $data_repair_method);

			// Excel::create('list', function($excel) use ($data_repair_method, $count) {
			// 	$excel->sheet('list', function($sheet) use ($data_repair_method, $count) {
			// 		for ($i = 1; $i <= $count ; $i++)
			// 		{ 
			// 			$sheet->row($i, $data_repair_method[$i - 1]);					
			// 		}
			//     });
			// })->store('csv', $source);
			\DB::commit();
			return redirect('/user/work_planning/base_planning/' . $session_id);
		}
		catch (\Exception $e)
		{
			dd($e);
		}
	}

	private function _getAllRepairMethods()
    {
        $lang = \App::isLocale('en') ? 'en' : 'vn';
        $records = \App\Models\mstRepairMethod::select('id', 'zone_id', 'pavement_type', 'code', \DB::raw("name_{$lang} as namerm"))
            ->orderBy('zone_id')
            ->get();
        $data = [];
        foreach ($records as $index => $r) 
        {
            $data[] = [
                'id' => $r->id,
                'color' => $r->code,
                'name' => $r->namerm,
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
			->where('target_type', 2)
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
            ->chunk(100, function($records) use(&$dataset) {
            	foreach ($records as $i) 
		        {
		            $dataset[$i->roadTypeValue->value][$i->roadClassValue->value][$i->surfaceValue->value][$i->row][$i->column] = $i->repairMethodValue->value;
		        }
            });
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
            ->chunk(100, function($records) use(&$dataset) {
            	foreach ($records as $i) 
		        {
		            $dataset[$i->roadTypeValue->value][$i->roadClassValue->value][$i->surfaceValue->value][$i->row][$i->column] = $i->repairMethodValue->value;
		        }
            });
        return $dataset;
    }

	public function getRepairMatrix($session_id)
	{	
		try
		{
			$work_planning = tblWorkPlanning::find($session_id);
			if (Auth::user()->id != $work_planning->created_by)
			{
				return redirect()->back();
			}
			if (!isset($work_planning->base_planning_year))
			{
				return redirect()->route('user.work.base.planning', ['session_id' => $session_id]);
			}

			$text_region = $work_planning->getInfoOrganization($session_id);
			$text_year = $work_planning->year;
			$base_planning_year = $work_planning->base_planning_year;
			$status = $work_planning->status;

			$repair_matrix = tblRepairMatrix::find($work_planning->repairMatrix->id);
            $crack_ranks = \App\Models\tblConditionRank::where('target_type', 1)->orderBy('rank')->get();
            $rut_ranks = \App\Models\tblConditionRank::where('target_type', 2)->orderBy('rank')->get();
            $road_category = \App\Models\mstRoadCategory::whereIn('code_id', [0, 1])->get();
            $road_class = \App\Models\mstRoadClass::whereIn('code_id', [1, 2, 3, 4, 5, 6])->get();
            $pavement_type = \App\Models\mstSurface::whereIn('code_id', [1, 2, 3])->get();

            $repair_methods = $this->_getAllRepairMethods();
            $matrix = $this->_getSavedMatrix($crack_ranks->count(), $rut_ranks->count(), $repair_matrix);
            $saved_zone = $this->_getRestrictZone($repair_matrix);

            return view('front-end.work_planning.repair_matrix_cell', [
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
				'text_year' => $text_year,
				'session' => $session_id,
				'back' => route('user.work.forecast.index', array('session_id' => $session_id)),
				'session' => $session_id,
				'base_planning_year' => $base_planning_year,
				'status' => $status
            ]);
		}
		catch (\Exception $e)
		{
			dd($e->getMessage());
		}
	}

	// private function _getRepairMethods($rec)
 //    {
 //        // $repair_methods = \App\Models\mstRepairMethod::with([
 //        //         'costs' => function($q) use($rec){
 //        //             $q->whereIn('organization_id', $rec->organizations()->get()->pluck('id')->toArray());
 //        //         },
 //        //         'unit'
 //        //     ])
 //        //     ->get();
 //        $repair_methods = \App\Models\mstRepairMethod::with([
 //                'costs' => function($q){
 //                    $q->orderBy('organization_id', 'ASC');
 //                },
 //                'unit'
 //            ])
 //            ->get();
 //        $data_repair_method = [];
 //        foreach ($repair_methods as $key => $method)
 //        {
 //            if ($method->costs->count() == 0)
 //            {
 //                continue;
 //            }
 //            // $data_repair_method[$method->id] = array(
 //            //     'cost' => 1000 * round(array_sum($method->costs->pluck('cost')->toArray())/$method->costs->count()),
 //            //     'unit' => $method->unit->code_id,
 //            // );
 //            $data_repair_method[$method->id] = array(
 //                'cost_1' => @$method->costs[0]->cost * 1000,
 //                'cost_2' => @$method->costs[1]->cost * 1000,
 //                'cost_3' => @$method->costs[2]->cost * 1000,
 //                'cost_4' => @$method->costs[3]->cost * 1000,
 //                'unit' => $method->unit->code_id,
 //            );
 //        }
 //        return $data_repair_method;
 //    }

 //    private function _getMatrixMethod($work_planning)
 //    {
 //        $repair_methods = $this->_getRepairMethods($work_planning);

 //        $data;
 //        $latest_matrix_chk = tblRepairMatrixCell::where('repair_matrix_id', $work_planning->default_repair_matrix_id)
 //            ->where('user_id', $work_planning->created_by)
 //            ->where('target_type', 2)
 //            ->count();
 //        if ($latest_matrix_chk > 0)
 //        {
 //            $data = tblRepairMatrixCell::where('repair_matrix_id', $work_planning->default_repair_matrix_id)
 //                ->where('user_id', $work_planning->created_by)
 //                ->where('target_type', 2);
 //        }
 //        else
 //        {
 //            $data = tblRepairMatrixCell::where('repair_matrix_id', $work_planning->default_repair_matrix_id)
 //                ->whereNull('user_id');
 //        }
                                                
 //        $info_matrix = $data->with([
 //                'crackValue',
 //                'rutValue',
 //                'repairMethodValue',
 //                'roadTypeValue',
 //                'roadClassValue',
 //                'surfaceValue'
 //            ])
 //            ->get();

 //        $repair_matrix_structure = [];
 //        foreach ($info_matrix as $i) 
 //        {
 //            $key = implode('-', [
 //                $i->roadTypeValue->value,
 //                $i->roadClassValue->value,
 //                $i->surfaceValue->value,
 //                $i->row, 
 //                $i->column
 //            ]);
 //            // $repair_matrix_structure[$key] = [
 //            //     'id' => $i->repairMethodValue->value,
 //            //     'cost' => @$repair_methods[$i->repairMethodValue->value]['cost'],
 //            //     'unit' => @$repair_methods[$i->repairMethodValue->value]['unit']
 //            // ];
 //            $repair_matrix_structure[$key] = [
 //                'id' => $i->repairMethodValue->value,
 //                'cost_1' => @$repair_methods[$i->repairMethodValue->value]['cost_1'],
 //                'cost_2' => @$repair_methods[$i->repairMethodValue->value]['cost_2'],
 //                'cost_3' => @$repair_methods[$i->repairMethodValue->value]['cost_3'],
 //                'cost_4' => @$repair_methods[$i->repairMethodValue->value]['cost_4'],
 //                'unit' => @$repair_methods[$i->repairMethodValue->value]['unit']
 //            ];
 //        }
 //        return $repair_matrix_structure;
 //    }

 //    private function _readInputSource($work_planning)
 //    {
 //        $source = public_path("application/process/work_planning/" . $work_planning->id . "/data/input_forecast.csv");
 //        $input = array();

 //        Helper::getRowDataChunk($source, 1024, function($chunk, &$handle, $iteration) use(&$input) {
 //            $array_cake = explode(',', $chunk);
 //            for ($i = 1; $i <= count($array_cake) - 1; $i++)
 //            {
 //                $tmp = str_replace(array("\n", "\r", "\r\n", "\n\r"), '', $array_cake[$i]);
 //                $array_cake[$i] = trim($tmp, '"');
 //            }
 //            if (count($array_cake) > 5)
 //            {
 //                $input[] = $array_cake;
 //            }
 //        }, 99999999);
 //        return $input;
 //    }

 //    private function _convertSurface($pt)
 //    {
 //        $convert_surface = ['AC' => 1, 'BST' => 2, 'CC' => 3];
 //        return isset($convert_surface[$pt]) ? $convert_surface[$pt] : 0;
 //    }

	public function getRepairCondition($session_id)
	{
		try
		{
			// $work_planning = tblWorkPlanning::find($session_id);
	  //       $planned_section_id = [];
	  //       // convert matrix method into array
	  //       $matrix_method = $this->_getMatrixMethod($work_planning);

	  //       $input = $this->_readInputSource($work_planning);
	  //       $crack_rank = \App\Models\tblConditionRank::where('target_type', 1)->orderBy('rank')->get()->pluck('rank', 'from')->toArray();
	  //       $rut_rank = \App\Models\tblConditionRank::where('target_type', 2)->orderBy('rank')->get()->pluck('rank', 'from')->toArray();
	  //       $organizations = \App\Models\tblOrganization::where('level', 3)->get();
	  //       $sb_parent = [];
	  //       foreach ($organizations as $item)
	  //       {
	  //           $sb_parent[$item->id] = @$item->rmb()->first()->id;
	  //       }
	        
	  //       foreach ($input as $index => $tmp) 
	  //       {
	  //           $method = [];
	  //           $weight = [];
	  //           $price = [];
	  //           $error = $tmp[0];
	  //           if ($error != 0)
	  //           {
	  //               continue;
	  //           }

	  //           $section_id2 = $tmp[1];
	  //           $road_class = $tmp[2];
	  //           $surface = $tmp[22];
	            
	  //           $surface_key = $this->_convertSurface($surface);
	  //           $road_category = substr($section_id2, 2, 1);
	  //           // echo '---\\n';
	  //           // echo 'section id: ', $section_id2, '\\n';

	  //           for ($i = 32; $i <= 52; $i+= 5)
	  //           {
	  //               $row = \Helper::vlookup($tmp[$i], $crack_rank) - 1;
	  //               if ($surface_key == 3)
	  //               {
	  //                   $col = 0;
	  //               }
	  //               else
	  //               {
	  //                   $col = \Helper::vlookup($tmp[$i + 1], $rut_rank) - 1;
	  //               }
	                
	  //               // echo $i, '\\n';
	  //               // echo 'rut: ', $tmp[$i + 1], ', crack: ', $tmp[$i], '\\n';
	  //               // echo 'method-rut: ', $col, ', method-crack: ', $row, '\\n';
	  //               $key = implode('-', [
	  //                   $road_category,
	  //                   $road_class,
	  //                   $surface_key,
	  //                   $row, 
	  //                   $col
	  //               ]);
	  //               if ($section_id2 == '201503000003_0209' && $tmp[5] == 400)
		 //            {
		 //            	print '<pre>';
		 //            	print_r($tmp);
		 //            	echo $key, '<br/>';
		 //            }
	  //               // echo 'key: ' , $key, '\\n';
	  //               if (isset($matrix_method[$key]))
	  //               {
	  //                   $method[] = $matrix_method[$key]['id'];
	  //                   if ($matrix_method[$key]['unit'] == 0)
	  //                   {
	  //                       // m
	  //                       $weight[] = $tmp[8];
	  //                       $price[] = $tmp[8] * $matrix_method[$key]['cost_'. $sb_parent[$tmp[11]]];
	  //                   }
	  //                   else if ($matrix_method[$key]['unit'] == 1)
	  //                   {
	  //                       // m2
	  //                       if ($matrix_method[$key]['id'] == 1001 || $matrix_method[$key]['id'] == 1002 || $matrix_method[$key]['id'] == 1003)
	  //                       {
	  //                           $weight[] = $tmp[8] * 1.6;
	  //                           $price[] = $tmp[8] * 1.6 * $matrix_method[$key]['cost_'. $sb_parent[$tmp[11]]];
	  //                       }
	  //                       else
	  //                       {
	  //                           $weight[] = $tmp[8] * $tmp[12];
	  //                           $price[] = $tmp[8] * $tmp[12] * $matrix_method[$key]['cost_'. $sb_parent[$tmp[11]]];
	  //                       }
	  //                   }
	  //                   else
	  //                   {
	  //                       $weight[] = $tmp[8] * $tmp[12] * $tmp[13];
	  //                       $price[] = $tmp[8] * $tmp[12] * $tmp[13] * $matrix_method[$key]['cost_'. $sb_parent[$tmp[11]]];
	  //                   }
	  //               }
	  //               else
	  //               {
	  //                   $method[] = '0';
	  //                   $weight[] = '0';
	  //                   $price[] = '0';
	  //               }
	  //           }
	  //           $road_category = substr($tmp[1], 2, 1);
	  //           $road_number = substr($tmp[1], 3, 3);
	  //           $road_number_supplement = substr($tmp[1], 6, 3);
	  //           $branch_number = substr($tmp[1], 9, 2);
	  //           $direction = substr($tmp[1], 11, 1);
	  //           $lane_pos_number = $tmp[21];
	  //           //$number_of_lanes = substr($item['section_id'], 11, 1);
	  //           $km_from = sprintf("%04d", $tmp[4]); 
	  //           $m_from = sprintf("%05d", $tmp[5]);
	  //           $section_id = $road_category . $road_number . $road_number_supplement . $branch_number . $direction . $lane_pos_number. '_' .$km_from . $m_from;            

	  //           $planned = [];
	  //       }
	  //       dd(1);



			// if ($record_error == Null)
			// {
			$rec = tblWorkPlanning::find($session_id);
			if (Auth::user()->id != $rec->created_by)
			{
				return redirect()->back();
			}
			$path = public_path("application/process/work_planning/". $session_id. "/input/repair/0/1");
	        if (!file_exists($path)) 
			{
				return redirect()->route('user.work.get.repairmatrix', ['session_id' => $session_id]);
			}
			if ($rec->matrix_flg < 4)
	        {
	        	// show loading screen
	            return view('front-end.work_planning.loading')->with(array(
	                'timer' => '5000',
	                'id' => $session_id,
	            ));
	        }
	        $method = [];
			$file = public_path("application/process/work_planning/".$session_id."/input/repair/list.csv");

			$fp = fopen($file, 'r');
			// fseek($fp, 3);
			while ($line = fgetcsv($fp)) 
			{
				$method_tmp = $line;

				if (App::isLocale('en'))
				{
					$method[] = [
						'price' => $method_tmp[1], 
						'method_name' => $method_tmp[4]
					];
				}
				else
				{
					$method[] = [
						'price' => $method_tmp[1], 
						'method_name' => $method_tmp[3]
					];
				}
		   //      foreach ($method as $row)
		   //      {
		   //      	$tmp = explode(',', $row); 
		   //      	for ($i = 1; $i <= count($tmp) - 1; $i++)
		   //          {
		   //          	// $cell_value = preg_replace(sprintf('/^%s/', pack('H*','EFBBBF')), $tmp[$i]);
		   //              $tmp[$i] = trim(str_replace(array("\n", "\r", "\r\n", "\n\r"), '', $tmp[$i]), '"');
		   //          }
					// $method_tmp[] = $tmp;
		   //      }
			}
			fclose($fp);
	       
			// $method_file = file(public_path("application/process/work_planning/".$session_id."/input/repair/list.csv"));
			// $method = [];
			// foreach ($method_file as $row)
			// {
			// 	$method_tmp = explode(',', $row);
				
				
			// }
			// }
	        $work_planning = tblWorkPlanning::find($session_id); 
			$text_region = $work_planning->getInfoOrganization($session_id);
			$text_year = $work_planning->year;
			$base_planning_year = $work_planning->base_planning_year;

			// if ($record_error == Null)
			// {
			return view('front-end.work_planning.repair_condition')->with(array(
				'text_region' => $text_region,
				'text_year'  => $text_year,
				'session_id' => $session_id,
				'base_planning_year' => $base_planning_year,
				'methods' => $method,
				'back' => route('user.work.get.repairmatrix', array('session_id' => $session_id))
			));
			// }
			// else
			// {
			// 	return view('front-end.work_planning.list_records_error')->with(array(
			// 		'text_region' => $text_region,
			// 		'text_year'  => $text_year,
			// 		'session_id' => $session_id,
			// 		'error' => $record_error,
			// 		'back' => route('user.work.get.repairmatrix', array('session_id' => $session_id))
			// 	));
			// }
			
			
        }
		catch (\Exception $e)
		{
			dd($e->getMessage());
		}
	}
	
	/*public function list_error_matrix()
	{
		return view('front-end.work_planning.list_error')->with([
				'session_id' => $session_id
		]);
	}*/
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
           
            $data_table_load[] = array_combine($indexes, $array_cake);
        }, 100);
        array_shift($data_table_load);
        return $data_table_load;
	}

	function checkFlg(Request $request)
	{
		try
		{
			$rec = tblWorkPlanning::findOrFail($request->id);
			
			if (isset($request->type) && !empty($request->type))
			{
				$list = $request->list;
				if ($list == 0)
				{
					$passed = $rec->excel_flg_0;
				}
				else if ($list == 1)
				{
					$passed = $rec->excel_flg_1;
				}
				else 
				{
					$passed = $rec->excel_flg_2;
				}
				$total = 16;
			}
			else
			{
				$total = 4;
				$passed = $rec->matrix_flg;
			}
			return round(100*$passed/$total) . '%';
		}
		catch (\Exception $e)
		{
			dd($e->getMessage());
		}
	}

	// public function creatInputFile($session_id)
	// {
 //        $work_planning = tblWorkPlanning::find($session_id);
 //        $organization = tblWorkPlanningOrganization::where('work_planning_id', $session_id)->get();
 //        $year = $work_planning->year;
 //        //get id in chose process
 //        foreach($organization as $row) 
 //        {
 //            $organization_id[] = $row->organization_id;
 //        }  
 //        //find id in tbldeteriction
 //        foreach($organization_id as $row)
 //        {
 //            $ids[] = $deterioration = tblDeterioration::where('year_of_dataset', $year)
 //                    ->where('organization_id', $row)
 //                    ->where('dataset_flg', 1)->first()
 //                    ->id;
 //        }
        
 //        $data = Helper::epsilonInput($session_id, $ids);
 //        $final_forecast = $data[0];
 //        $final_data = $data[1];
 //        $method_name = $data[2];
 //        //dd($method_name);
 //        $forecast = [];
 //        foreach ($final_forecast as $row)
 //        {   
 //            $tmp = [];
 //            for ($i =0; $i < 5; $i++)
 //            {
 //                $tmp = array_merge($tmp, $row[$i]);
 //            }
 //            $forecast[] = $tmp;
 //        }
        
 //        $data = [];
 //        foreach($final_data as $row)
 //        {
 //            $tmp = array_merge($row['crack'], $row['rut']);
 //            $tmp = array_merge($tmp, $row['IRI']);
 //            $data[] = $tmp; 
 //        }
       
 //        $file_path = public_path("application/process/work_planning/".$session_id."/data/");
 //        $file_name = "input.csv";
       
 //        $excelFile = Excel::load($file_path . $file_name,  function ($reader) use ($forecast) {
 //            $reader->sheet(0, function($sheet) use ($forecast) {
 //                $sheet->fromArray($forecast, NULL, 'AF0');
 //            });
 //        })->store('csv', $file_path); 

 //        $excelFile = Excel::load($file_path . $file_name,  function ($reader) use ($data) {
 //            $reader->sheet(0, function($sheet) use ($data) {
 //                $sheet->fromArray($data , NULL, 'BE0');
 //            });
 //        })->store('csv', $file_path);

 //        $excelFile = Excel::load($file_path . $file_name,  function ($reader) use ($method_name) {
 //            $reader->sheet(0, function($sheet) use ($method_name) {
 //                $sheet->fromArray($method_name, NULL, 'CG0');
 //            });
 //        })->store('csv', $file_path);
	// } 

	function postRemoveCustomization($session_id)
	{
		\DB::beginTransaction();
		try
		{
			$rec = tblWorkPlanning::findOrFail($session_id);
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

	function getFormulateAnnualYear($session_id)
	{
		try
		{
			$work_planning = tblWorkPlanning::find($session_id); 
			if (Auth::user()->id != $work_planning->created_by)
			{
				return redirect()->back();
			}
			$path = public_path('application/process/work_planning/'. $session_id .'/input/repair/0/1');
			if (!file_exists($path)) 
			{
				return redirect()->route('user.work.get.repairmatrix', ['session_id' => $session_id]);
			}

			$text_region = $work_planning->getInfoOrganization($session_id);
			$text_year = $work_planning->year;
			$base_planning_year = $work_planning->base_planning_year;
			$total_budget = $work_planning->total_budget;
			$year_1 = $work_planning->year_1;
			$year_2 = $work_planning->year_2;
			$year_3 = $work_planning->year_3;
			$year_4 = $work_planning->year_4;
			$price_esca_factor = $work_planning->price_esca_factor;
			$criteria = json_decode($work_planning->criteria);
			$status = $work_planning->status;
			return view('front-end.work_planning.formulate_annual')->with(array(
				'text_region' => $text_region,
				'text_year'  => $text_year,
				'session_id' => $session_id,
				'base_planning_year' => $base_planning_year,
				'total_budget' => $total_budget,
				'year_1' => $year_1,
				'year_2' => $year_2,
				'year_3' => $year_3,
				'year_4' => $year_4,
				'price_esca_factor' => $price_esca_factor,
				'criteria' => $criteria,
				'status' => $status,
				'back' => route('user.work.get.repaircondition', array('session_id' => $session_id))
			));
		}
		catch (\Exception $e)
		{
			dd($e->getMessage());
		}
	}

	function getResult($session_id)
	{
		try
		{
			$work_planning = tblWorkPlanning::find($session_id); 
			if (Auth::user()->id != $work_planning->created_by)
			{
				return redirect()->back();
			}
			if (!isset($work_planning->total_budget))
			{
				return redirect()->route('user.wp.formulate.annual.year', ['session_id' => $session_id]);
			}

			$text_region = $work_planning->getInfoOrganization($session_id);
			$text_year = $work_planning->year;
			$base_planning_year = $work_planning->base_planning_year;
			$status = $work_planning->status;
			return view('front-end.work_planning.result')->with(array(
				'text_region' => $text_region,
				'text_year'  => $text_year,
				'session_id' => $session_id,
				'base_planning_year' => $base_planning_year,
				'back' => route('user.wp.formulate.annual.year', array('session_id' => $session_id)),
				'excel_flg' => @$work_planning->excel_flg_0,
				'list' => 0,
				'status' => $status
			));
		}
		catch (\Exception $e)
		{
			dd($e->getMessage());
		}
	}

	private function _createHeader(&$writer)
	{
		$writer->writeSheetRow('Sheet1', array(
			trans('wp.road_inventory'), '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '',
			trans('wp.Latest_Repair'), '', '', '',
			trans('wp.Traffic_Volume'), '', '',
			trans('wp.pavement_condition_survey'), '', '', '', '', '', '', '', '', '',
			trans('wp.MCI'),
			trans('wp.first_year_prediction'), '', '', '', '', '', '', '', '', '', '',
			trans('wp.second_year_prediction'), '', '', '', '', '', '', '', '', '', '',
			trans('wp.third_year_prediction'), '', '', '', '', '', '', '', '', '', '',
			trans('wp.fourth_year_prediction'), '', '', '', '', '', '', '', '', '', '',
			trans('wp.fifth_year_prediction'), '', '', '', '', '', '', '', '', '', ''
		));
		$writer->writeSheetRow('Sheet1', array(
			trans('wp.section_id'), 
			trans('wp.route_no'), 
			trans('wp.route_name'), 
			trans('wp.branch_no'), 
			trans('wp.jurisdiction'), 
			trans('wp.management_agency'), 
			trans('wp.road_class'), 
			trans('wp.construction_year'), 
			trans('wp.kilopost'), '', '', '', 
			trans('wp.length'), 
			trans('wp.number_of_lane'), 
			trans('wp.direction'), 
			trans('wp.pavement_type'), 
			trans('wp.width'), 
			trans('wp.pavement_thickness'),
			trans('wp.year_of_latest_repair'), 
			trans('wp.repair_lane'), 
			trans('wp.repair_category'), 
			trans('wp.repair_classification'),
			trans('wp.traffic_survey_year'), 
			trans('wp.total_traffic_volume'), 
			trans('wp.heavy_traffic_volume'),
			trans('wp.surveyed_year_month'), 
			trans('wp.surveyed_lane'), 
			trans('wp.pavement_type'), 
			trans('wp.cracking_ratio_%'), '', '', '', 
			trans('wp.rutting_depth'), '', 
			trans('wp.iri_mm_m'),
			'',
			trans('wp.concerning_year'), 
			trans('wp.estimated_pavement_indices'), '', '', '', 
			trans('wp.estimated_repair_information'), '', '', '', '', '',
			trans('wp.estimated_pavement_indices'), '', '', '', 
			trans('wp.estimated_repair_information'), '', '', '', '', '',
			trans('wp.estimated_pavement_indices'), '', '', '', 
			trans('wp.estimated_repair_information'), '', '', '', '', '',
			trans('wp.estimated_pavement_indices'), '', '', '', 
			trans('wp.estimated_repair_information'), '', '', '', '', '',
			trans('wp.estimated_pavement_indices'), '', '', '', 
			trans('wp.estimated_repair_information'), '', '', '', '', ''
		));
		$writer->writeSheetRow('Sheet1', array(
			'', '', '', '', '', '', '', '', 
			trans('wp.from'), '', 
			trans('wp.to'), '', '', '', '', '', '', '',
			'', '', '', '',
			'', '', '',
			'', '', '', 
			trans('wp.crack_%'), trans('wp.patching_%'), trans('wp.pothole_%'), trans('wp.total_%'), 
			trans('wp.max_mm'), trans('wp.average_%'), '',
			'',
			'', trans('wp.independent_indices'), '', '', 
			trans('wp.MCI'), 
			trans('wp.repair_method'), 
			trans('wp.repair_classification'), 
			trans('wp.unit_cost_1000'), 
			trans('wp.quantity'), 
			trans('wp.unit_of_quantity'), 
			trans('wp.repair_cost'),
			'', trans('wp.independent_indices'), '', '', 
			trans('wp.MCI'), 
			trans('wp.repair_method'), 
			trans('wp.repair_classification'), 
			trans('wp.unit_cost_1000'), 
			trans('wp.quantity'), 
			trans('wp.unit_of_quantity'), 
			trans('wp.repair_cost'),
			'', trans('wp.independent_indices'), '', '', 
			trans('wp.MCI'), 
			trans('wp.repair_method'), 
			trans('wp.repair_classification'), 
			trans('wp.unit_cost_1000'), 
			trans('wp.quantity'), 
			trans('wp.unit_of_quantity'), 
			trans('wp.repair_cost'),
			'', trans('wp.independent_indices'), '', '', 
			trans('wp.MCI'), 
			trans('wp.repair_method'), 
			trans('wp.repair_classification'), 
			trans('wp.unit_cost_1000'), 
			trans('wp.quantity'), 
			trans('wp.unit_of_quantity'), 
			trans('wp.repair_cost'),
			'', trans('wp.independent_indices'), '', '', 
			trans('wp.MCI'), 
			trans('wp.repair_method'), 
			trans('wp.repair_classification'), 
			trans('wp.unit_cost_1000'), 
			trans('wp.quantity'), 
			trans('wp.unit_of_quantity'), 
			trans('wp.repair_cost')
		));
		$writer->writeSheetRow('Sheet1', array(
			'', '', '', '', '', '', '', '', 
			trans('wp.km'), trans('wp.m'), trans('wp.km'), trans('wp.m'), '', '', '', '', '', '',
			'', '', '', '',
			'', '', '',
			'', '', '', 
			'', '', '', '', 
			'', '', '',
			'',
			'', trans('wp.cracking_ratio_total_%'), trans('wp.rutting_depth_mm'), trans('wp.iri_mm_m'), 
			'', '', '', '', '', '', '',
			'', trans('wp.cracking_ratio_total_%'), trans('wp.rutting_depth_mm'), trans('wp.iri_mm_m'), 
			'', '', '', '', '', '', '',
			'', trans('wp.cracking_ratio_total_%'), trans('wp.rutting_depth_mm'), trans('wp.iri_mm_m'), 
			'', '', '', '', '', '', '',
			'', trans('wp.cracking_ratio_total_%'), trans('wp.rutting_depth_mm'), trans('wp.iri_mm_m'), 
			'', '', '', '', '', '', '',
			'', trans('wp.cracking_ratio_total_%'), trans('wp.rutting_depth_mm'), trans('wp.iri_mm_m'), 
			'', '', '', '', '', '', ''
		));

		$writer->markMergedCell('Sheet1', $start_row = 0, $start_col = 0, $end_row = 0, $end_col = 17);
		$writer->markMergedCell('Sheet1', $start_row = 0, $start_col = 18, $end_row = 0, $end_col = 21);
		$writer->markMergedCell('Sheet1', $start_row = 0, $start_col = 22, $end_row = 0, $end_col = 24);
		$writer->markMergedCell('Sheet1', $start_row = 0, $start_col = 25, $end_row = 0, $end_col = 34);
		$writer->markMergedCell('Sheet1', $start_row = 0, $start_col = 36, $end_row = 0, $end_col = 46);
		$writer->markMergedCell('Sheet1', $start_row = 0, $start_col = 47, $end_row = 0, $end_col = 57);
		$writer->markMergedCell('Sheet1', $start_row = 0, $start_col = 58, $end_row = 0, $end_col = 68);
		$writer->markMergedCell('Sheet1', $start_row = 0, $start_col = 69, $end_row = 0, $end_col = 79);
		$writer->markMergedCell('Sheet1', $start_row = 0, $start_col = 80, $end_row = 0, $end_col = 90);
		$writer->markMergedCell('Sheet1', $start_row = 1, $start_col = 0, $end_row = 3, $end_col = 0);
		$writer->markMergedCell('Sheet1', $start_row = 1, $start_col = 1, $end_row = 3, $end_col = 1);
		$writer->markMergedCell('Sheet1', $start_row = 1, $start_col = 2, $end_row = 3, $end_col = 2);
		$writer->markMergedCell('Sheet1', $start_row = 1, $start_col = 3, $end_row = 3, $end_col = 3);
		$writer->markMergedCell('Sheet1', $start_row = 1, $start_col = 4, $end_row = 3, $end_col = 4);
		$writer->markMergedCell('Sheet1', $start_row = 1, $start_col = 5, $end_row = 3, $end_col = 5);
		$writer->markMergedCell('Sheet1', $start_row = 1, $start_col = 6, $end_row = 3, $end_col = 6);
		$writer->markMergedCell('Sheet1', $start_row = 1, $start_col = 7, $end_row = 3, $end_col = 7);
		$writer->markMergedCell('Sheet1', $start_row = 1, $start_col = 8, $end_row = 1, $end_col = 11);
		$writer->markMergedCell('Sheet1', $start_row = 2, $start_col = 8, $end_row = 2, $end_col = 9);
		$writer->markMergedCell('Sheet1', $start_row = 2, $start_col = 10, $end_row = 2, $end_col = 11);
		for ($i = 12; $i <= 27; $i++)
		{
			$writer->markMergedCell('Sheet1', $start_row = 1, $start_col = $i, $end_row = 3, $end_col = $i);
		}
		$writer->markMergedCell('Sheet1', $start_row = 1, $start_col = 28, $end_row = 1, $end_col = 31);
		$writer->markMergedCell('Sheet1', $start_row = 1, $start_col = 32, $end_row = 1, $end_col = 33);
		for ($i = 28; $i <= 33; $i++)
		{
			$writer->markMergedCell('Sheet1', $start_row = 2, $start_col = $i, $end_row = 3, $end_col = $i);
		}
		$writer->markMergedCell('Sheet1', $start_row = 1, $start_col = 34, $end_row = 3, $end_col = 34);
		$writer->markMergedCell('Sheet1', $start_row = 0, $start_col = 35, $end_row = 3, $end_col = 35);

		for ($i = 0; $i < 5; $i++)
		{
			$writer->markMergedCell('Sheet1', $start_row = 1, $start_col = (36 + 11*$i), $end_row = 3, $end_col = (36 + 11*$i));
			$writer->markMergedCell('Sheet1', $start_row = 1, $start_col = (37 + 11*$i), $end_row = 1, $end_col = (40 + 11*$i));
			$writer->markMergedCell('Sheet1', $start_row = 2, $start_col = (37 + 11*$i), $end_row = 2, $end_col = (39 + 11*$i));
			$writer->markMergedCell('Sheet1', $start_row = 2, $start_col = (40 + 11*$i), $end_row = 3, $end_col = (40 + 11*$i));
			$writer->markMergedCell('Sheet1', $start_row = 1, $start_col = (41 + 11*$i), $end_row = 1, $end_col = (46 + 11*$i));
			$writer->markMergedCell('Sheet1', $start_row = 2, $start_col = (41 + 11*$i), $end_row = 3, $end_col = (41 + 11*$i));
			$writer->markMergedCell('Sheet1', $start_row = 2, $start_col = (42 + 11*$i), $end_row = 3, $end_col = (42 + 11*$i));
			$writer->markMergedCell('Sheet1', $start_row = 2, $start_col = (43 + 11*$i), $end_row = 3, $end_col = (43 + 11*$i));
			$writer->markMergedCell('Sheet1', $start_row = 2, $start_col = (44 + 11*$i), $end_row = 3, $end_col = (44 + 11*$i));
			$writer->markMergedCell('Sheet1', $start_row = 2, $start_col = (45 + 11*$i), $end_row = 3, $end_col = (45 + 11*$i));
			$writer->markMergedCell('Sheet1', $start_row = 2, $start_col = (46 + 11*$i), $end_row = 3, $end_col = (46 + 11*$i));
		}
	}

	function postExport($session_id, $year, $list, Request $request)
	{
		// $input = array();

		// Helper::getRowDataChunk($source, 10000, function($chunk, &$handle, $iteration) use(&$input) {
  //           $array_cake = explode(',', $chunk);

  //           for ($i = 1; $i <= count($array_cake) - 1; $i++)
  //           {
  //               $array_cake[$i] = $array_cake[$i];
  //           }
  //           if (count($array_cake) > 5)
  //           {
  //           	$input[] = $array_cake;
  //           }
  //       }, 99999999);
		set_time_limit(60);
		include_once(public_path("../lib/XLSXWriter/xlsxwriter.class.php"));
		$filter_type = 1;// get selected
		if (!isset($request->submit_type))
		{
			$filter_type = 2;// get by year
		}
		if ($request->submit_type == 2)
		{
			$filter_type = 3;// get by filter 	
		}

		$dataset = $this->_generateOutput($session_id, $filter_type, $request, $year, $list);

		$writer = new \XLSXWriter();
		$this->_createHeader($writer);
		foreach ($dataset as $index => $row)
		{
			$writer->writeSheetRow('Sheet1', $row);
		}
	
		// $writer->writeSheet($dataset);
		$filetopath = public_path('tmp/' . str_random(40) . '/output.xlsx');
		$dirname = dirname($filetopath);
        if (!is_dir($dirname))
        {
            mkdir($dirname, 0755, true);
        }
		$writer->writeToFile($filetopath);
		return response()->download($filetopath);

		// $src = public_path('excel_templates/b.xlsx');
		// $writer = WriterFactory::create(Type::XLSX);
		// $writer->openToFile($src);
		// foreach ($dataset as $row) 
		// {
		// 	$writer->addRow($row); // no style will be applied
		// }

		// $writer->close();


		// $writer = WriterFactory::create(Type::XLSX);
		// dd(1);
		// $writer->openToFile($src); // write data to a file or to a PHP stream
		// $writer->openToBrowser($src); // stream data directly to the browser

		// $writer->addRow([
			// trans('wp.road_inventory'), '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '',

		// ]);





		// $writer->addRows($dataset); // add multiple rows at a time

		// $writer->close();
		// $src = public_path('excel_templates/work_planning.xlsx');
  //   	$excelFile = Excel::load($src,  function ($reader) use ($dataset) {
  //           $reader->sheet(0, function($sheet) use ($dataset) {
  //               $sheet->fromArray($dataset, NULL, 'A5');
  //           });
  //       })->download('xlsx');
	}

	private function _getMethodList($session_id)
	{
		$method_final = [];
		$file = public_path("application/process/work_planning/".$session_id."/input/repair/list.csv");

		$fp = fopen($file, 'r');
		// fseek($fp, 3);
		$method_tmp = [];
		while ($line = fgetcsv($fp)) 
		{
			$method_tmp[] = $line;
	   //      foreach ($method as $row)
	   //      {
	   //      	$tmp = explode(',', $row); 
	   //      	for ($i = 1; $i <= count($tmp) - 1; $i++)
	   //          {
	   //          	// $cell_value = preg_replace(sprintf('/^%s/', pack('H*','EFBBBF')), $tmp[$i]);
	   //              $tmp[$i] = trim(str_replace(array("\n", "\r", "\r\n", "\n\r"), '', $tmp[$i]), '"');
	   //          }
				// $method_tmp[] = $tmp;
	   //      }
		}
		fclose($fp);
		// dd($method_tmp);
		// foreach ($method_tmp as $row)
  //       {
  //       	$key = $row[0];
  //       	$method_final["m".(string)$key] = [$row[1], $row[2]];

  //       	if (\App::isLocale('en'))
  //       	{
  //       		$method_final["m".(string)$key][] = $row[4];
  //       		$method_final["m".(string)$key][] = $row[6];
  //       	}
  //       	else
  //       	{
  //       		$method_final["m".(string)$key][] = $row[3];
  //       		$method_final["m".(string)$key][] = $row[5];
  //       	}
  //       	$method_final["m".(string)$key][] = $row[7];
  //       }

        foreach ($method_tmp as $row)
        {
        	$key = $row[0];
        	$method_final["m".(string)$key] = [$row[1], $row[2], $row[3], $row[4], $row[5]];

        	if (\App::isLocale('en'))
        	{
        		$method_final["m".(string)$key][] = $row[7];
        		$method_final["m".(string)$key][] = $row[9];
        	}
        	else
        	{
        		$method_final["m".(string)$key][] = $row[6];
        		$method_final["m".(string)$key][] = $row[8];
        	}
        	$method_final["m".(string)$key][] = $row[10];
        }
        return $method_final;
	}

	private function _generateOutput($session_id, $filter_type, $request, $year, $list)
	{
		$branches = [];
		$sbs = [];
		$road_classes = [];
		$repair_categories = [];
		$repair_classifications = [];
		$sbs = [];
		$rmbs = [];
		$direction = [
			1 => trans('back_end.left_direction'),
            2 => trans('back_end.right_direction'),
            3 => trans('back_end.single_direction')
		];
		$unit = [];

		$lang = \App::isLocale('en') ? 'en' : 'vn';
		$records = tblBranch::all();
		foreach ($records as $r)
		{
			$branches[intval($r->road_number)][intval($r->branch_number)][intval($r->road_number_supplement)][intval($r->road_category)] = $r->{"name_{$lang}"};
		}
		$records = mstRoadClass::all();
		foreach ($records as $r)
		{
			$road_classes[$r->code_id] = $r->{"name_{$lang}"};
		}
		$records = \App\Models\tblRCategory::get();
        foreach ($records as $r) 
        {
            $repair_categories[$r->id] = $r->code;
        }
        $records = \App\Models\tblRClassification::get();
        foreach ($records as $r) 
        {
            $repair_classifications[$r->id] = $r->code;
        }
        $records = \App\Models\tblOrganization::where('level', 3)->get();
        foreach ($records as $r) 
        {
            $sbs[$r->id] = $r->{"name_{$lang}"};
        }
        $records = \App\Models\tblOrganization::where('level', 2)->get();
        foreach ($records as $r) 
        {
        	$rmbs[$r->code_id][] = $r->id;
            $rmbs[$r->code_id][] = $r->{"name_{$lang}"};
        }
        $records = \App\Models\mstMethodUnit::get();
        foreach ($records as $r) 
        {
            $unit[$r->id] = $r->code_name;
        }

        $method_final = $this->_getMethodList($session_id);

        $input = [];
        $index = 0;
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
        $source = "application/process/work_planning/" . $session_id . "/data/". $input_file. ".csv";
        Helper::getRowDataChunk($source, 10000, function($chunk, &$handle, $iteration) use(&$index, &$input, $branches, $road_classes, $repair_categories, $repair_classifications, $sbs, $rmbs, $direction, $unit, $method_final, $filter_type, $request, $year) {
            $i = explode(',', $chunk);

            for ($j = 1; $j <= count($i) - 1; $j++)
            {
                $i[$j] = str_replace(array("\n", "\r", "\r\n", "\n\r"), '', $i[$j]);
            }
            // $flag = false;
            // if (in_array($year, [1, 2, 3, 4, 5]))
            // {
            // 	if (count($i) > 5 && $i[0] == 0 && isset($i[71]) && $i[71] == $year)
            // 	{
            // 		$flag = true;
            // 	}
            // }
            // else if ($year == 6)
            // {
            // 	if (isset($i[71]))
            // 	{
            // 		$flag = false;
            // 	} 
            // 	else
            // 	{
            // 		$flag = true;
            // 	}
            // }
            if (count($i) > 5 && $i[0] == 0 && isset($i[71]) && $i[71] == $year)
            {
            	$jurisdiction = substr($i[1], 0, 2); 
            	$road_category = substr($i[1], 2, 1);
	    		$road_number = substr($i[1], 3, 3);
	    		$road_number_supplement = substr($i[1], 6, 3);
	    		$branch_number = substr($i[1], 9, 2);
	    		$route_name = $branches[intval($road_number)][intval($branch_number)][intval($road_number_supplement)][intval($road_category)];

	    		$failed = false;
            	switch ($filter_type) 
            	{
            		case 1:
            			if (!in_array($index, $request->id))
            			{
            				$failed = true;
            				break;
            			}
            			break;
            		case 2:
            			break;
            		case 3:
            			if (!empty($request->route_name) && $request->route_name != $route_name)
            			{
            				$failed = true;
            				break;
            			}
            			if (!empty($request->branch_number) && $request->branch_number != $branch_number)
            			{
            				$failed = true;
            				break;
            			}
            			if (!empty($request->road_class) && $request->road_class != $road_classes[$i[2]])
            			{
            				$failed = true;
            				break;
            			}
            			if (!empty($request->direction) && $request->direction != $i[10])
            			{
            				$failed = true;
            				break;
            			}
            			if (!empty($request->pavement_type) && $request->pavement_type != $i[22])
            			{
            				$failed = true;
            				break;
            			}
            			if (!empty($request->construction_year))
            			{
            				$parse = \Helper::parseSuperInput($request->construction_year);
            				if (!version_compare($i[3], $parse[1], $parse[0]))
            				{
            					$failed = true;
            					break;
            				}
            			}
            			if (!empty($request->km_from))
            			{
            				$parse = \Helper::parseSuperInput($request->km_from);
            				if (!version_compare($i[4], $parse[1], $parse[0]))
            				{
            					$failed = true;
            					break;
            				}
            			}
            			if (!empty($request->m_from))
            			{
            				$parse = \Helper::parseSuperInput($request->m_from);
            				if (!version_compare($i[5], $parse[1], $parse[0]))
            				{
            					$failed = true;
            					break;
            				}
            			}
            			if (!empty($request->km_to))
            			{
            				$parse = \Helper::parseSuperInput($request->km_to);
            				if (!version_compare($i[6], $parse[1], $parse[0]))
            				{
            					$failed = true;
            					break;
            				}
            			}
            			if (!empty($request->m_to))
            			{
            				$parse = \Helper::parseSuperInput($request->m_to);
            				if (!version_compare($i[7], $parse[1], $parse[0]))
            				{
            					$failed = true;
            					break;
            				}
            			}
            			break;
            		default:
    					$failed = true;
    					break;
            	}
            	// dd($sbs);
            	if (!$failed)
            	{
		    		$tmp = [
		    			$i[1],
		    			intval($road_number),
		    			$route_name,
		    			intval($branch_number),
		    			$rmbs[$jurisdiction][1],
		    			@$sbs[$i[11]],
		    			$road_classes[$i[2]],
		    			$i[3],
		    			$i[4],
		    			$i[5],
		    			$i[6],
		    			$i[7],
		    			$i[8],
		    			$i[9],
		    			$direction[$i[10]],
		    			$i[22],
		    			$i[12],
		    			$i[13],
		    			$i[14],
		    			$i[21],
		    			@$repair_categories[$i[15]],
		    			@$repair_classifications[$i[16]],
		    			$i[17],
		    			$i[18],
		    			$i[19],
		    			$i[20],
		    			$i[21],
		    			$i[22],
		    			$i[23],
		    			$i[24],
		    			$i[25],
		    			$i[26],
		    			$i[27],
		    			$i[28],
		    			$i[29],
		    			$i[30],
		    			// year 1
		    			$i[31],
		    			$i[32],
		    			$i[33],
		    			$i[34],
		    			max(floatval($i[35]), 0),
		    			@$method_final["m".(string)$i[56]][5],
		    			@$method_final["m".(string)$i[56]][6],
		    			@$method_final["m".(string)$i[56]][$rmbs[$jurisdiction][0] - 1]*0.001,
		    			$i[61],
		    			@$unit[$method_final["m".(string)$i[56]][7]],
		    			$i[66]*0.001,
		    			// year 2
		    			$i[36],
		    			$i[37],
		    			$i[38],
		    			$i[39],
		    			max(floatval($i[40]), 0),
		    			@$method_final["m".(string)$i[57]][5],
		    			@$method_final["m".(string)$i[57]][6],
		    			@$method_final["m".(string)$i[57]][$rmbs[$jurisdiction][0] - 1]*0.001,
		    			$i[62],
		    			@$unit[$method_final["m".(string)$i[57]][7]],
		    			$i[67]*0.001,
		    			// year 3
		    			$i[41],
		    			$i[42],
		    			$i[43],
		    			$i[44],
		    			max(floatval($i[45]), 0),
		    			@$method_final["m".(string)$i[58]][5],
		    			@$method_final["m".(string)$i[58]][6],
		    			@$method_final["m".(string)$i[58]][$rmbs[$jurisdiction][0] - 1]*0.001,
		    			$i[63],
		    			@$unit[$method_final["m".(string)$i[58]][7]],
		    			$i[68]*0.001,
		    			// year 4
		    			$i[46],
		    			$i[47],
		    			$i[48],
		    			$i[49],
		    			max(floatval($i[50]), 0),
		    			@$method_final["m".(string)$i[59]][5],
		    			@$method_final["m".(string)$i[59]][6],
		    			@$method_final["m".(string)$i[59]][$rmbs[$jurisdiction][0] - 1]*0.001,
		    			$i[64],
		    			@$unit[$method_final["m".(string)$i[59]][7]],
		    			$i[69]*0.001,
		    			// year 5
		    			$i[51],
		    			$i[52],
		    			$i[53],
		    			$i[54],
		    			max(floatval($i[55]), 0),
		    			@$method_final["m".(string)$i[60]][5],
		    			@$method_final["m".(string)$i[60]][6],
		    			@$method_final["m".(string)$i[60]][$rmbs[$jurisdiction][0] - 1]*0.001,
		    			$i[65],
		    			@$unit[$method_final["m".(string)$i[60]][7]],
		    			$i[70]*0.001
		    		];
	    		
    				$input[] = $tmp;
    			}
    		}
    		$index++;
        }, 99999999);

    	return $input;
	}

	function postMoveSection($session_id, $year, $list, $type, Request $request)
	{		
		// $start = microtime(true);
		$year_to = $request->year_to;
		$input = [];
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
		$source = "application/process/work_planning/" . $session_id . "/data/". $input_file .".csv";
		$index = 0;

		if ($type == 0)
		{
			$selected = $request->id;	
			Helper::getRowDataChunk($source, 10000, function($chunk, &$handle, $iteration) use(&$input, &$index, $year_to, $selected) {
	            $i = explode(',', $chunk);

	            for ($j = 1; $j <= count($i) - 1; $j++)
	            {
	                $i[$j] = str_replace(array("\n", "\r", "\r\n", "\n\r"), '', $i[$j]);
	            }
	            if (count($i) > 5 && $i[0] == 0 && isset($i[71]) && in_array($index, $selected))
	            {
	            	$i[71] = $year_to;
	            	if (empty($year_to))
	            	{
	            		unset($i[71]);
	            	}
	    		}
	    		else if (count($i) > 5 && $i[0] == 0 && !isset($i[71]) && in_array($index, $selected))
	    		{
	    			$i[71] = $year_to;
	    		}
	    		$input[] = $i;

	    		$index++;
        	}, 99999999);
		}
		else
		{
			$input = $this->_moveFilterSection($request, $year_to, $year, $source);
		}

        $open = fopen(public_path("application/process/work_planning/" . $session_id . "/data/". $input_file.".csv"), 'w+');
		foreach ($input as $row)
		{
		    fputcsv($open, $row);
		}	
		fclose($open);
		
		return redirect()->back();
	}

	private function _moveFilterSection($request, $year_to, $year, $source)
	{
		$start = microtime(true);
		$input = [];
		$branches = [];
		$road_classes = [];
		$direction = [
			1 => trans('back_end.left_direction'),
            2 => trans('back_end.right_direction'),
            3 => trans('back_end.single_direction')
		];
		$lang = \App::isLocale('en') ? 'en' : 'vn';
		$records = tblBranch::all();
		foreach ($records as $r)
		{
			$branches[intval($r->road_number)][intval($r->branch_number)][intval($r->road_number_supplement)][intval($r->road_category)] = $r->{"name_{$lang}"};
		}
		$records = mstRoadClass::all();
		foreach ($records as $r)
		{
			$road_classes[$r->code_id] = $r->{"name_{$lang}"};
		}
		$r_name = $request->route_name;
		$r_branch_number = $request->branch_number;
		$r_road_class = $request->road_class;
		$r_direction = $request->direction;
		$r_pavement_type = $request->pavement_type;
		$r_construction_year = $request->construction_year;
		$r_km_from = $request->km_from;
		$r_m_from = $request->m_from;
		$r_km_to = $request->km_to;
		$r_m_to = $request->m_to;
	
        Helper::getRowDataChunk($source, 10000, function($chunk, &$handle, $iteration) use(&$input, $branches, $road_classes, $direction, $year_to, $year, $r_name, $r_branch_number, $r_road_class, $r_direction, $r_pavement_type, $r_construction_year, $r_km_from, $r_m_from, $r_km_to, $r_m_to) {

            $i = explode(',', $chunk);

            for ($j = 1; $j <= count($i) - 1; $j++)
            {
                $i[$j] = str_replace(array("\n", "\r", "\r\n", "\n\r"), '', $i[$j]);
            }

        	if (count($i) > 5 && $i[0] == 0)
            {
            	$jurisdiction = substr($i[1], 0, 2); 
            	$road_category = substr($i[1], 2, 1); 
	    		$road_number = substr($i[1], 3, 3);
	    		$road_number_supplement = substr($i[1], 6, 3);
	    		$branch_number = substr($i[1], 9, 2);
	    		$route_name = $branches[intval($road_number)][intval($branch_number)][intval($road_number_supplement)][intval($road_category)];

	    		


	    		$failed = false;

    			//if ($r_name != '' && $r_name != $route_name)
    			if (!empty($r_name) && $r_name != $route_name)
    			{
    				$failed = true;
    			}
    			if (!empty($r_branch_number) && $r_branch_number != $branch_number)
    			{
    				$failed = true;
    			}
    			if (!empty($r_road_class) && $r_road_class != $road_classes[$i[2]])
    			{
    				$failed = true;
    			}
    			if (!empty($r_direction) && $rr_direction != $i[10])
    			{
    				$failed = true;
    			}
    			if (!empty($r_pavement_type) && $r_pavement_type != $i[22])
    			{
    				$failed = true;
    			}
    			if (!empty($r_construction_year))
    			{
    				$parse = \Helper::parseSuperInput($r_construction_year);
    				if (!version_compare($i[3], $parse[1], $parse[0]))
    				{
    					$failed = true;
    				}
    			}
    			if (!empty($r_km_from))
    			{
    				$parse = \Helper::parseSuperInput($r_km_from);
    				if (!version_compare($i[4], $parse[1], $parse[0]))
    				{
    					$failed = true;
    				}
    			}
    			if (!empty($r_m_from))
    			{
    				$parse = \Helper::parseSupr_m_fromrInput($r_m_from);
    				if (!version_compare($i[5], $parse[1], $parse[0]))
    				{
    					$failed = true;
    				}
    			}
    			if (!empty($r_km_to))
    			{
    				$parse = \Helper::parseSuperInput($r_km_to);
    				if (!version_compare($i[6], $parse[1], $parse[0]))
    				{
    					$failed = true;
    				}
    			}
    			if (!empty($r_m_to))
    			{
    				$parse = \Helper::parseSuperInput($r_m_to);
    				if (!version_compare($i[7], $parse[1], $parse[0]))
    				{
    					$failed = true;
    				}
    			}









    			// if (!empty($request->road_class) && $request->road_class != $road_classes[$i[2]])
    			// {
    			// 	$failed = true;
    			// }
    			// if (!empty($request->direction) && $request->direction != $i[10])
    			// {
    			// 	$failed = true;
    			// }
    			// if (!empty($request->pavement_type) && $request->pavement_type != $i[22])
    			// {
    			// 	$failed = true;
    			// }
    			// if (!empty($request->construction_year))
    			// {
    			// 	$parse = \Helper::parseSuperInput($request->construction_year);
    			// 	if (!version_compare($i[3], $parse[1], $parse[0]))
    			// 	{
    			// 		$failed = true;
    			// 	}
    			// }
    			// if (!empty($request->km_from))
    			// {
    			// 	$parse = \Helper::parseSuperInput($request->km_from);
    			// 	if (!version_compare($i[4], $parse[1], $parse[0]))
    			// 	{
    			// 		$failed = true;
    			// 	}
    			// }
    			// if (!empty($request->m_from))
    			// {
    			// 	$parse = \Helper::parseSuperInput($request->m_from);
    			// 	if (!version_compare($i[5], $parse[1], $parse[0]))
    			// 	{
    			// 		$failed = true;
    			// 	}
    			// }
    			// if (!empty($request->km_to))
    			// {
    			// 	$parse = \Helper::parseSuperInput($request->km_to);
    			// 	if (!version_compare($i[6], $parse[1], $parse[0]))
    			// 	{
    			// 		$failed = true;
    			// 	}
    			// }
    			// if (!empty($request->m_to))
    			// {
    			// 	$parse = \Helper::parseSuperInput($request->m_to);
    			// 	if (!version_compare($i[7], $parse[1], $parse[0]))
    			// 	{
    			// 		$failed = true;
    			// 	}
    			// }

            	if (!$failed)
            	{
            		// echo "<pre>";
            		// print_r($i);
            		// echo "</pre>";
            		if (in_array($year, [1, 2, 3, 4, 5]))
            		{
            			if(isset($i[71]) && $i[71] == $year)
	            		{
	            			$i[71] = $year_to;
		            		if (empty($year_to))
		            		{
		            			unset($i[71]);
		            		}
	            		}
            		}
            		else
            		{
            			if (!isset($i[71])) $i[71] = $year_to;
            		}	
    			}
    		}
    		$input[] = $i;
        }, 99999999);
        // dd('total: ', microtime(true) - $start);
       	
        return $input;
	}

	function getResultExport(Request $request, $session_id, $list)
	{
		try
        {
        	$work_planning = tblWorkPlanning::findOrFail($session_id);
        	$lang = (\App::isLocale('en')) ? 'en' : 'vn';
        	if (Auth::user()->id != $work_planning->created_by)
			{
				return redirect()->back();
			}
        	if ($list == 0)
        	{
        		$name = "Output_WP_Candidate_". $lang .".xlsx";
        	}
        	else if ($list == 1)
        	{
        		$name = "Output_WP_Proposal_". $lang .".xlsx";
        	}
        	else
        	{
        		$name = "Output_WP_Final_". $lang .".xlsx";
        	}
            $public_dir = public_path('/application/process/work_planning/' . $session_id);
            $file_name = 'Output_WP_'. $list. '_'. $lang .'.xlsx';
            $headers = array(
                'Content-Type' => 'application/octet-stream',
            );
            $file_to_path = $public_dir . '/' . $file_name;
            if (file_exists($file_to_path))
            {
            	return response()->download($file_to_path, $name, $headers);	
            }
            
        }
        catch (\Exception $e)
        {
            dd($e->getMessage());
        }
	}

	function getGenerateResult($session_id, $list)
	{
		try
		{
			$work_planning = tblWorkPlanning::find($session_id);
			if (Auth::user()->id != $work_planning->created_by)
			{
				return redirect()->back();
			} 
			if ($list == 0)
			{
				$route = 'user.wp.result';
			}
			else if ($list == 1)
			{
				$route = 'user.wp.proposal';
			}
			else 
			{
				$route = 'user.wp.planned';
			}
			if ($work_planning->{"excel_flg_{$list}"} < 16 && $work_planning->{"excel_flg_{$list}"} != NULL)
			{
				return view('front-end.work_planning.loading')->with(array(
	                'timer' => '5000',
	                'id' => $session_id,
	                'type' => '1',
	                'list' => $list
	            ));
			}
			
			return redirect()->route($route, [$session_id]);
		}
		catch (\Exception $e)
		{
			dd($e->getMessage());
		}
	}

	function getHistory()
	{
		return view('front-end.work_planning.history');
	}

	function getViewHistory($session_id)
	{
		return redirect()->route('user.wp.planned', [$session_id]);
	}

	function deleteHistory($session_id)
	{
		DB::beginTransaction();
		try
		{
			$work_planning = tblWorkPlanning::findOrFail($session_id);
			if ($work_planning->status == 1)
			{
				return redirect(url('user/work_planning/history'))->with([
	                'flash_level' => 'danger',
	                'flash_message' => trans('validation.wp_has_been_planned_delete_fail')
	            ]);
			}
			$work_planning->delete();
			DB::commit();
			return redirect(url('user/work_planning/history'))->with([
                'flash_level' => 'success',
                'flash_message' => trans('validation.delete_wp_success')
            ]); 
		}
		catch(\Exception $e)
		{
			DB::rollBack();
			dd($e->getMessage);
		}
	}

	function getProposal($session_id)
	{
		try
		{
			$work_planning = tblWorkPlanning::find($session_id); 
			if (Auth::user()->id != $work_planning->created_by)
			{
				return redirect()->back();
			}
			$path = public_path('application/process/work_planning/'. $session_id .'/data/input_proposal.csv');
			if (!file_exists($path)) 
			{
				return redirect()->route('user.wp.result', ['session_id' => $session_id]);
			}

			$text_region = $work_planning->getInfoOrganization($session_id);
			$text_year = $work_planning->year;
			$base_planning_year = $work_planning->base_planning_year;
			$status = $work_planning->status;
			return view('front-end.work_planning.result')->with(array(
				'text_region' => $text_region,
				'text_year'  => $text_year,
				'session_id' => $session_id,
				'base_planning_year' => $base_planning_year,
				'back' => route('user.wp.result', array('session_id' => $session_id)),
				'excel_flg' => @$work_planning->excel_flg_1,
				'list' => 1,
				'status' => $status
			));
		}
		catch (\Exception $e)
		{
			dd($e->getMessage());
		}
	}

	function getPlanned($session_id)
	{
		try
		{
			$work_planning = tblWorkPlanning::find($session_id);
			if (Auth::user()->id != $work_planning->created_by)
			{
				return redirect()->back();
			} 
			$path = public_path('application/process/work_planning/'. $session_id .'/data/input_planned.csv');
			if (!file_exists($path)) 
			{
				return redirect()->route('user.wp.proposal', ['session_id' => $session_id]);
			}

			$text_region = $work_planning->getInfoOrganization($session_id);
			$text_year = $work_planning->year;
			$base_planning_year = $work_planning->base_planning_year;
			$status = $work_planning->status;
			return view('front-end.work_planning.result')->with(array(
				'text_region' => $text_region,
				'text_year'  => $text_year,
				'session_id' => $session_id,
				'base_planning_year' => $base_planning_year,
				'back' => route('user.wp.proposal', array('session_id' => $session_id)),
				'excel_flg' => @$work_planning->excel_flg_2,
				'list' => 2,
				'status' => $status
			));
		}
		catch (\Exception $e)
		{
			dd($e->getMessage());
		}
	}
}
