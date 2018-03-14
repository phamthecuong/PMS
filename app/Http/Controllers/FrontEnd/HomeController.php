<?php

namespace App\Http\Controllers\FrontEnd;

use Validator, DB, App;
use App\Http\Controllers\Controller;
use App\Models\tblUser;
use App\Models\tblRoad;
use App\Models\tblSectionSurvey;
use App\Models\tblOrganization;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Foundation\Auth\AuthenticatesUsers;
use App\Models\tblDeterioration;
use App\Models\tblBudgetSimulation;
use App\Models\tblNotification;

class HomeController extends Controller
{
	public function home()
	{
		return view('front-end.home');
	}
    
    public function index()
    {
    	return redirect('web_map');
    }

	// public function get_route_list()
	// {

	// 	// $sb_id = $this -> input -> get('sb_id');
	// 	$sb_id = 1;
	// 	if ($sb_id == 0)
	// 	{
	// 		$route_data = tblRoad::get_route_list();
	// 	}
	// 	else 
	// 	{
	// 		$route_data = tblRoad::get_route_by_sb($sb_id);
	// 	}
	// 	echo json_encode($route_data);
	// }

	// public function getSectionRoadData(Request $request)
	// {
	// 	//$user = checkuser();
	// 	$sb_id = $request->input('sb_id');
		
	// 	$road_name = $request->input('road_name');
	// 	dd($road_name);die;
	// 	$zoomLvl = $request->input('zoomLvl');
	// 	$survey_date = $request->input('survey_date');
	// 	$min_lat = $request->input('min_lat');
	// 	$min_lng = $request->input('min_lng');
	// 	$max_lat = $request->input('max_lat');
	// 	$max_lng = $request->input('max_lng');
	// 	// $language = current_language();
	// 	$language = "vi";
	// 	$kilopost_from = $request->input('kilopost_from');
	// 	$kilopost_to = $request->input('kilopost_to');
	// 	//
	// 	$sql = $this -> prepareSectionRoadDataSql($sb_id, $zoomLvl, $road_name, $survey_date, $min_lat, $min_lng, $max_lat, $max_lng, $language, $kilopost_from, $kilopost_to);
	// 	// $result = $this -> db -> query($sql) -> result();
	// 	$results = DB::select( DB::raw("$sql") );
	// 	$response = array();
	// 	foreach ($result as $rec)
	// 	{
	// 		$data = array();
	// 		if (!isset($rec -> section_code))
	// 			continue;
	// 		$data["section_code"] = $rec -> section_code;
	// 		$data["data"] = $this -> convertLatLngFormat($rec -> latitudes, $rec -> longitudes);
	// 		$data["cracking_ratio"] = $this -> convertColor('cracking_ratio', ($rec -> cracking_ratio));
	// 		$data["rutting_depth"] = $this -> convertColor('rutting_depth', ($rec -> rutting_ave));
	// 		$data["mci"] = $this -> convertColor('mci', $rec -> MCI);
	// 		$data["iri"] = $this -> convertColor('iri', $rec -> IRI);
	// 		$response[] = $data;
	// 	}
	// 	echo json_encode($response);
	// }

	// private function prepareSectionRoadDataSql($sb_id, $zoomLvl, $road_name, $survey_date, $min_lat, $min_lng, $max_lat, $max_lng, $language, $kilopost_from, $kilopost_to)
	// {
	// 	// section table
	// 	$section_table = "";
	// 	switch ($zoomLvl)
	// 	{
	// 		case 0 :
	// 		case 1 :
	// 		case 2 :
	// 			$section_table = "(select * from tblSection group by SB_id)";
	// 			break;
	// 		case 3 :
	// 		case 4 :
	// 			$section_table = "(select * from tblSection group by SB_id, route_number)";
	// 			break;
	// 		case 5 :
	// 			$section_table = "(select * from tblSection group by SB_id, route_number, branch_number)";
	// 			break;
	// 		case 6 :
	// 		case 7 :
	// 		case 8 :
	// 		case 9 :
	// 		case 10 :
	// 			$section_table = "(select * from tblSection group by SB_id, route_number, branch_number, km_from)";
	// 			break;
	// 		case 11 :
	// 		case 12 :
	// 		case 13 :
	// 		case 14 :
	// 		case 15 :
	// 		case 16 :
	// 			$section_table = "(select * from tblSection group by SB_id, route_number, branch_number, km_from, m_from)";
	// 			break;
	// 		default :
	// 			$section_table = "tblSection";
	// 			break;
	// 	}

	// 	// survey table
	// 	$modified_survey;
	// 	if ($survey_date == "latest")
	// 	{
	// 		$modified_survey = "(SELECT section_id, section_survey_id, max(survey_year) as survey_year FROM `tblSection_survey` group by section_id)";
	// 	}
	// 	else
	// 	if ($survey_date == "second_latest")
	// 	{
	// 		$modified_survey = "(select * 
	// 			from (
	// 				SELECT a.section_id, a.survey_year, count(*) as row_number, a.section_survey_id 
	// 				FROM tblSection_survey a
	// 				JOIN tblSection_survey b ON a.section_id = b.section_id AND a.survey_year <= b.survey_year
	// 				GROUP BY a.section_id, a.survey_year
	// 			) survey
	// 			WHERE row_number = 2)";
	// 	}
	// 	else
	// 	if (is_numeric($survey_date))
	// 	{
	// 		$modified_survey = "(select * from tblSection_survey where survey_year = '{$survey_date}')";
	// 	}
	// 	else
	// 	{
	// 		echo "Invalid Survey Date: " . $survey_date;
	// 		die ;
	// 	}
		
	// 	// $point_table = "(select * from tblPoint where lat >= '{$min_lat}' and lat <= '{$max_lat}' and lng >= '{$min_lng}' and lng <= '{$max_lng}' and oldFlg = 0)";

	// 	// $sql = "select s.section_code, GROUP_CONCAT(lat) AS latitudes, GROUP_CONCAT(lng) AS longitudes, section_data.*
	// 			// \n from {$point_table} as p
	// 			// \n join {$modified_survey} as survey on p.section_survey_id = survey.section_survey_id
	// 			// \n left join tblSection_data AS section_data ON survey.section_survey_id = section_data.section_survey_id AND section_data.oldFlg = 0 
	// 			// \n join {$section_table} as s on s.section_code = survey.section_id";
	// 	$sql = "select s.section_code, GROUP_CONCAT(p.lat) AS latitudes, GROUP_CONCAT(p.lng) AS longitudes, section_data.*
	// 			\n from tblPoint as p
	// 			\n join {$modified_survey} as survey on p.section_survey_id = survey.section_survey_id
	// 			\n left join tblSection_data AS section_data ON survey.section_survey_id = section_data.section_survey_id AND section_data.oldFlg = 0 
	// 			\n join {$section_table} as s on s.section_code = survey.section_id";
	// 	if (!empty($road_name) && $road_name != "0")
	// 	{
	// 		$sql .= "\n LEFT JOIN tblBranch AS branch ON s.branch_id = branch.id
	//                  \n LEFT JOIN tblRoad as route ON branch.route_id = route.id";
	// 	}
	// 	$sql .= "\n where p.lat >= '{$min_lat}' and p.lat <= '{$max_lat}' and p.lng >= '{$min_lng}' and p.lng <= '{$max_lng}' and p.oldFlg = 0";
	// 	if ($sb_id != 0)
	// 	{
	// 		$sql .= "\n and s.SB_id = {$sb_id}";
	// 	}
	// 	if (!empty($road_name) && $road_name != "0")
	// 	{
	// 		//$sql .= " AND (route.name_vn LIKE '%{$road_name}%' OR route.name_en LIKE '%{$road_name}%')";
	// 		$sql .= " AND (route.id = {$road_name})";
	// 	}
			
	// 	if (!empty($kilopost_from))
	// 	{
 // 			$sql .= " AND (section_data.KP_from >= {$kilopost_from})";
	// 	}
		
	// 	if (!empty($kilopost_to))
	// 	{
 // 			$sql .= " AND (section_data.KP_to <= {$kilopost_to})";
	// 	}
		
	// 	$sql .= "\n group by s.section_code";
	// 	// echo $sql;die;
	// 	return $sql;
	// }

	public function CheckFLG(Request $request)
	{
		if ($request->process == 'deterioration')
		{
			if ($request->name_step == 'pavement_type')
			{
				$deterioration = tblDeterioration::find($request->id);
				$flg = $deterioration->pav_type_flg;
				if ($flg == 0)
				{
					return "0%";
				}
				else if ($flg == 1)
				{
					return "17%";
				}
				else if ($flg == 2)
				{
					return "33%";
				}
				else if ($flg == 3)
				{
					return "50%";
				}
				else if ($flg == 4)
				{
					return "67%";
				}
				else if ($flg == 5)
				{
					return "83%";
				}
				else if ($flg == 6)
				{
					return "100%";
				}
			}
			if ($request->name_step == 'route')
			{
				$deterioration = tblDeterioration::find($request->id);
				$flg = $deterioration->route_flg;
				if ($flg == 0)
				{
					return "0%";
				}
				else if ($flg == 1)
				{
					return "17%";
				}
				else if ($flg == 2)
				{
					return "33%";
				}
				else if ($flg == 3)
				{
					return "50%";
				}
				else if ($flg == 4)
				{
					return "67%";
				}
				else if ($flg == 5)
				{
					return "83%";
				}
				else if ($flg == 6)
				{
					return "100%";
				}
			}
			if ($request->name_step == 'section_flg')
			{
				$deterioration = tblDeterioration::find($request->id);
				$flg = $deterioration->section_flg;
				if ($flg == 0)
				{
					return "0%";
				}
				else if ($flg == 1)
				{
					return "17%";
				}
				else if ($flg == 2)
				{
					return "33%";
				}
				else if ($flg == 3)
				{
					return "50%";
				}
				else if ($flg == 4)
				{
					return "67%";
				}
				else if ($flg == 5)
				{
					return "83%";
				}
				else if ($flg == 6)
				{
					return "100%";
				}
			}
			if ($request->name_step == 'benchmarking')
			{
				$deterioration = tblDeterioration::find($request->id);
				$flg = $deterioration->benchmark_flg;
                // dd($deterioration);
				if ($flg == 0)
				{
					return "0%";
				}
				else if ($flg == 1)
				{
					return "33%";
				}
				else if ($flg == 2)
				{
					return "67%";
				}
				else if ($flg == 3)
				{
					return "100%";
				}
				
			}
		}
	}

 //    function ChangeDataNotification()
 //    {
 //        // ini_set('display_errors', 1);
 //        // ini_set('display_startup_errors', 1);
 //        // error_reporting(E_ALL);
 //        $data = tblNotification::where('status_notification', 0)->get();
 //        foreach ($data as $d)
 //        {
 //            $d->status_notification = 1;
 //            $d->save();
 //        }
 //    }

 //    function LoadAjaxNotification(Request $request)
 //    {
 //        $max = 20;
 //        if ($request->type == 'complete')
 //        {
 //            $deteration = tblDeterioration::where('status', 1)->orderBy('id', 'desc')->get();
 //            $data = [];
            
 //            if (App::getLocale() == 'en')
 //            {
 //                if (count($deteration) < $max)
 //                {
 //                    for ($i=0; $i < count($deteration); $i++) 
 //                    {
 //                        $data[] = [$deteration[$i]->name_en, 
 //                        $deteration[$i]->year_of_dataset.'/'.@tblDeterioration::where('id', $deteration[$i]->id)->first()->year_of_dataset, 
 //                        '/user/deterioration/dataset_import/'.$deteration[$i]->id];
 //                    }
 //                }
 //                else
 //                {
 //                    for ($i=0; $i < $max; $i++) 
 //                    {
 //                        $data[] = [$deteration[$i]->name_en, 
 //                        $deteration[$i]->year_of_dataset.'/'.@tblDeterioration::where('id', $deteration[$i]->id)->first()->year_of_dataset, 
 //                        '/user/deterioration/dataset_import/'.$deteration[$i]->id];
 //                    }
 //                }
 //            }
 //            else
 //            {
 //                if (count($deteration) < $max)
 //                {
 //                    for ($i = 0; $i < count($deteration); $i++) 
 //                    {
 //                        $data[] = [$deteration[$i]->name_vn, 
 //                        // $deteration[$i]->year_of_dataset.'/'.@tblDeterioration::where('deterioration_id', $deteration[$i]->id)->first()->year, 
 //                        // '/user/deterioration/dataset_import/'.$deteration[$i]->id];
	// 					$deteration[$i]->year_of_dataset.'/'.@tblDeterioration::where('id', $deteration[$i]->id)->first()->year_of_dataset, 
 //                        '/user/deterioration/dataset_import/'.$deteration[$i]->id];
 //                    }
 //                }
 //                else
 //                {
 //                    for ($i = 0; $i < $max; $i++) 
 //                    {
 //                        $data[] = [$deteration[$i]->name_vn, 
 //                        // $deteration[$i]->year_of_dataset.'/'.@tblDeterioration::where('deterioration_id', $deteration[$i]->id)->first()->year, 
 //                        // '/user/deterioration/dataset_import/'.$deteration[$i]->id];
	// 					$deteration[$i]->year_of_dataset.'/'.@tblDeterioration::where('id', $deteration[$i]->id)->first()->year_of_dataset, 
 //                        '/user/deterioration/dataset_import/'.$deteration[$i]->id];
 //                    }
 //                }
 //            }
 //            return $data;
 //        }
 //        else if ($request->type == 'running')
 //        {
 //            $notification = tblNotification::where('status_process', 0)->orderBy('id', 'desc')->get();
 //            $data = [];
 //            // if (count($notification) < $max )
 //            // {
 //            $i = 0;
 //            foreach ($notification as $n)
 //            {
 //                if ($n->tblDeterioration != null)
 //                {
 //                    if (App::getLocale() == 'en')
 //                    {
 //                        $data[] = [@$n->tblDeterioration->name_en, '/user/deterioration/dataset_import/'.@$n->tblDeterioration->id, $n->percent];
 //                    }
 //                    else
 //                    {
 //                        $data[] = [@$n->tblDeterioration->name_vn, '/user/deterioration/dataset_import/'.@$n->tblDeterioration->id, $n->percent];
 //                    }
	// 				$i += 1;
 //                }
	// 			if ($i > $max)
	// 			{
	// 				break;
	// 			}
				
 //            }
 //            // }
 //            // else
 //            // {
 //                // for ($i = 0; $i < $max; $i ++)
 //                // {
 //                	// dd($notification);
 //                	// dd(tblNotification::where('reference_id', '169e8f71-2cee-4d8d-9c35-bd594d138f7d')->first()->tblDeterioration);
 //                    // if ($notification[$i]->tblDeterioration != null)
 //                    // {
 //                        // if (App::getLocale() == 'en')
 //                        // {
 //                            // $data[] = [$notification[$i]->tblDeterioration->name_en, '/user/deterioration/dataset_import/'.$notification[$i]->tblDeterioration->id, $notification[$i]->percent];
 //                        // }
 //                        // else
 //                        // {
 //                            // $data[] = [$notification[$i]->tblDeterioration->name_vn, '/user/deterioration/dataset_import/'.$notification[$i]->tblDeterioration->id, $notification[$i]->percent];
 //                        // }
 //                    // }
 //                // }
 //            // }
 //            return $data;
 //        }
 //    }
	
	// public function checkUserRole()
	// {
	// 	if (Auth::user()->hasRole('adminlv1'))
	// 	{
	// 		return 'admin';
	// 	}
	// 	else if (Auth::user()->hasRole('adminlv2'))
	// 	{
	// 		return 'admin';
	// 	}
	// 	else if (Auth::user()->hasRole('adminlv3'))
	// 	{
	// 		return 'admin';
	// 	}
	// 	else if (Auth::user()->hasRole('superadmin'))
	// 	{
	// 		return 'superadmin';
	// 	}
	// }
}
