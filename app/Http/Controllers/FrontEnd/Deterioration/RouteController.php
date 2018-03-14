<?php

namespace App\Http\Controllers\FrontEnd\Deterioration;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\tblDeterioration;
use App, Excel, Hash, Helper;
use App\Models\tblBranch;
use App\Models\tblConditionRank;
use App\Models\tblOrganization;

class RouteController extends DeteriorationController
{
    public function index($session_id)
    {
        $deterioration = tblDeterioration::find($session_id);
        if ($deterioration->route_flg != 6)
        {
            if ($deterioration->benchmark_flg == 3 && $deterioration->pav_type_flg == 6 )
            {
                return view('front-end.layouts.templates.loading')->with(array(
                    'name_step' => 'route',
                    'process' => 'deterioration',
                    'timer' => '5000',
                    'id' => $deterioration->id,
                ));
            }
            else
            {
                return redirect('/user/deterioration/dataset_import/'.$deterioration->id);
            }
        }
        $condition_rank = json_decode($deterioration->condition_rank);
        $link = "";

        $load = Excel::load('public/application/process/deterioration/'.$session_id.'/crack/output2/output0.csv')->get();
    	$muy_excel = '';
        $log_excel = '';
    	for ($i = 0; $i < count($load); $i++)
    	{
    		foreach ($load[$i] as $key => $value) 
    		{
    			if ($value == ' fai')
    			{
    				$muy_excel = $load[$i+1];
    			}
    			if ($value == ' log-likelihood function')
    			{
    				$log_excel = $load[$i+1];
    			}
    		}
    	}
    	$muy = ''; 
        $log = '';
    	foreach ($muy_excel as $key => $value) 
        {
    		$muy = trim($value);
    	}
    	foreach ($log_excel as $key => $value) 
        {
    		$log = trim($value);
    	}
        $as_data = Excel::load('public/application/process/deterioration/'.$session_id.'/crack/output4/epsilon21.csv')->get();
        $bst_data = Excel::load('public/application/process/deterioration/'.$session_id.'/crack/output4/epsilon22.csv')->get();
        $array1 = array();
        $array2 = array();
        foreach ($as_data[0] as $key => $value) 
        {
            $array1[] = $key;
        }
        foreach ($bst_data[0] as $key => $value) 
        {
            $array2[] = $key;
        }
        $as = array();
        $bst = array();

        $epsilon_bm = (float) $array1[2];
        $as[] = [$array1[0], $array1[1], $epsilon_bm/pow(10, strlen($epsilon_bm) - 1)];

        $epsilon_bm = (float) $array2[2];
        $bst[] = [$array2[0], $array2[1], $epsilon_bm/pow(10, strlen($epsilon_bm) - 1)];
        $select_as = array();
        $select_bst = array();
        for ($i=0; $i < count($as_data); $i++) 
        {
            $as[] = [$as_data[$i][$array1[0]], $as_data[$i][$array1[1]], $as_data[$i][$array1[2]]];
            $select_as[] = $as_data[$i][$array1[0]];
        }
        for ($i=0; $i < count($bst_data); $i++) 
        {
            $bst[] = [$bst_data[$i][$array2[0]], $bst_data[$i][$array2[1]], $bst_data[$i][$array2[2]]];
            $select_bst[] = $bst_data[$i][$array2[0]];
        }
        $array_as_bst = array_merge($select_as, $select_bst);
        $select_all = tblBranch::find($array_as_bst);
        $distress_type = (Helper::convertJsonConditionRank($deterioration->condition_rank));
        // dd($as);
    	return view('front-end.deterioration.route')->with(array(
    	    'id' => $session_id, 
            'deterioration' => $deterioration, 
            'muy' => $muy, 
            'log' => $log, 
            'as' => $as,
            'bst' => $bst, 
            'select_all' => $select_all ,
            'distress_type_select' => $distress_type
        ));
    }

    public function getDataChart(Request $request)
    {
        $tblDeterioration = tblDeterioration::find($request->deterioration_id);
        if ($request->select_distress_type == 1)
        {
            $link = 'crack';
            $json_cache = 'crack';
        }
        else if ($request->select_distress_type == 3)
        {
            $link = 'IRI';
            $json_cache = 'iri';
        }
        else if ($request->select_distress_type == 2 )
        {
            $link = 'rut';
            $json_cache = 'rut';
        }
        // dd(Helper::convertJsonConditionRank($tblDeterioration->condition_rank));
        $count = count(Helper::convertJsonConditionRank($tblDeterioration->condition_rank)[$json_cache]);
        //AS ALL
        if ($request->select == 'as_all')
        {
            $load_as = Excel::load('public/application/process/deterioration/'.$tblDeterioration->id.'/'.$link.'/output4/epsilon21.csv')->get();
            $array1 = array();
            foreach($load_as[0] as $key=>$value)
            {
                $array1[] = $key;
            }
            $array1 = array_slice($array1, $count+2, count($array1)-1);
            $array_cake = array();
            foreach ($array1 as $key => $value) 
            {
                $array_cake[] = (int)$value/1000000;
            }
            $array_cake[] = [
                'name' => 'BM_' . (\App::isLocale('en') ? 'AC' : 'BTN')
            ];
            $array_as = array();
            if ($count > 2)
            {
                $array_as[] = $array_cake;
            }
            for ($i = 0; $i < count($load_as); $i++)
            {
                // dd((int)round($load_as[$i]['bm']));
                if ($count > 2)
                {
                    $array_cache = array();
                    for ($j = 0; $j < count($array1); $j++)
                    {
                        $array_cache[] = (float)trim($load_as[$i][$array1[$j]]);
                    }
                    
                    $route_id = (string)(int)round($load_as[$i]['bm']);
                    $array_cache[] = [
                        'name' => tblBranch::findRouteNameByRouteId($route_id)
                    ];
					// dd($array_cache);
                    if (tblBranch::findRouteNameByRouteId($route_id) != '')
					{
						$array_as[] = $array_cache;
					}
                    else
                    {
                        // return $route_id;
                    }
                }
            }
            return $array_as;
        }
        //BST ALL
        else if ($request->select == 'bst_all')
        {
            $load_bst = Excel::load('public/application/process/deterioration/'.$tblDeterioration->id.'/'.$link.'/output4/epsilon22.csv')->get();
            $array2 = array();
            foreach($load_bst[0] as $key=>$value)
            {
                $array2[] = $key;
            }
            $array2 = array_slice($array2, $count+2, count($array2)-1);
            $array_cake = array();
            foreach ($array2 as $key => $value) 
            {
                $array_cake[] = $value/1000000;
            }
            $array_cake[] = [
                'name' => 'BM_' . (\App::isLocale('en') ? 'BST' : 'LN')
            ];
            $array_as = array();
            if ($count > 2)
            {
                $array_as[] = $array_cake;
            }
            for ($i = 0; $i < count($load_bst); $i++)
            {
                if ($count > 2)
                {
                    $array_cache = array();
                    for ($j = 0; $j < count($array2); $j++)
                    {
                        $array_cache[] = (float)trim($load_bst[$i][$array2[$j]]);
                    }
                    
                    $route_id = (string)(int)round($load_bst[$i]['bm']);
                    $array_cache[] = [
                        'name' => tblBranch::findRouteNameByRouteId($route_id)
                    ];
					// if (tblBranch::findRouteNameByRouteId($route_id) == 'National Highway 279')
					// {
						// dd($route_id);
					// }
					if (tblBranch::findRouteNameByRouteId($route_id))
					{
						$array_as[] = $array_cache;
					}
                }
            }
            return $array_as;
        }
        // else
        else 
        {
            $load_bst = Excel::load('public/application/process/deterioration/'.$tblDeterioration->id.'/'.$link.'/output4/epsilon22.csv')->get();
            $array2 = array();
            foreach($load_bst[0] as $key => $value)
            {
                $array2[] = $key;
            }

            $log = $array2[0];
            $array2 = array_slice($array2, $count+2, count($array2)-1);
            $array_cake = array();
            foreach ($array2 as $key => $value) 
            {
                $array_cake[] = $value/1000000;
            }
            $array_cake[] = [
                'name' => 'BM_' . (\App::isLocale('en') ? 'BST' : 'LN')
            ];
            $array_as = array();
            $check = ''; 
            
            $branch = tblBranch::find($request->select);

            for ($i = 0; $i < count($load_bst); $i++)
            {
                if (
                    substr((int)$load_bst[$i][$log], 2, 1) == $branch->road_category &&
                    substr((int)$load_bst[$i][$log], 3, 3) == $branch->road_number &&
                    substr((int)$load_bst[$i][$log], 6, 3) == $branch->road_number_supplement
                )
                {
                    $check = "ok";
                    $array_caches = array();
                    // $array_caches[] = 0;
                    for ($j = 0; $j < count($array2); $j++)
                    {
                        $array_caches[] = (float)trim($load_bst[$i][$array2[$j]]);
                    }
                    $route_id = (string)(int)round($load_bst[$i]['bm']);
                    $array_caches[] = [
                        'name' => tblBranch::findRouteNameByRouteId($route_id) . '_' . (\App::isLocale('en') ? 'BST' : 'LN')
                    ];
					if (tblBranch::findRouteNameByRouteId($route_id))
					{
						$array_as[] = $array_caches;
					}
                    // return $array_as;
                    break;
                }
            }
           
            if ($check == "ok")
            {
                $array_as[] = $array_cake;
            }
            // return $array_as;
            $load_as = Excel::load('public/application/process/deterioration/'.$tblDeterioration->id.'/'.$link.'/output4/epsilon21.csv')->get();
            $array1 = array();
            foreach($load_as[0] as $key=>$value)
            {
                $array1[] = $key;
            }
            $array1 = array_slice($array1, $count+3, count($array1)-1);
            $array_cake = array();
            $array_cake[] = 0;
            foreach ($array1 as $key => $value) 
            {
                $array_cake[] = $value/1000000;
            }
            $array_cake[] = [
                'name' => 'BM_' . (\App::isLocale('en') ? 'AC' : 'BTN')
            ];
            $check = '';
            for ($i = 0; $i < count($load_as); $i++)
            {
                if (substr((int)$load_as[$i][$log], 3, 3) == tblBranch::find($request->select)->road_number)
                {
                    $check = "ok";
                    $array_caches = array();
                    $array_caches[] = 0;
                    for ($j = 0; $j < count($array1); $j++)
                    {
                        $array_caches[] = (float)trim($load_as[$i][$array1[$j]]);
                    }
                    $route_id = (string)(int)round($load_as[$i]['bm']);
                    $array_caches[] = [
                        'name' => tblBranch::findRouteNameByRouteId($route_id) . '_' . (\App::isLocale('en') ? 'AC' : 'BTN')
                    ];
					if (tblBranch::findRouteNameByRouteId($route_id))
					{
						$array_as[] = $array_caches;
					}
                }
            }
            if ($check == "ok")
            {
                $array_as[] = $array_cake;
            }
            return array_merge($array_as);
        }
    }

    public function getDataChartWithDistress(Request $request)
    {
        $lang_cd = (App::getLocale() == 'en') ? 'en' : 'vn';
        $deterioration = tblDeterioration::find($request->deterioration);
        if ($request->select_distress_type == 1)
        {
            $link = 'crack';
        }
        else if ($request->select_distress_type == 2)
        {
            $link = 'rut';
        }
        else if ($request->select_distress_type == 3)
        {
            $link = 'IRI';
        }
        $load = Excel::load('public/application/process/deterioration/'.$deterioration->id.'/'.$link.'/output2/output0.csv')->get();
        $muy_excel = '';
        $log_excel = '';
        for ($i = 0; $i < count($load); $i++)
        {
            foreach ($load[$i] as $key => $value) 
            {
                if ($value == ' fai')
                {
                    $muy_excel = $load[$i+1];
                }
                if ($value == ' log-likelihood function')
                {
                    $log_excel = $load[$i+1];
                }
            }
        }
        $muy = ''; 
        $log = '';
        foreach ($muy_excel as $key => $value) 
        {
            $muy = trim($value);
        }
        foreach ($log_excel as $key => $value) 
        {
            $log = trim($value);
        }

        $as_data = Excel::load('public/application/process/deterioration/'.$deterioration->id.'/'.$link.'/output4/epsilon21.csv')->get();
        $bst_data = Excel::load('public/application/process/deterioration/'.$deterioration->id.'/'.$link.'/output4/epsilon22.csv')->get();
        $array1 = array();
        $array2 = array();

        foreach ($as_data[0] as $key => $value) 
        {
            $array1[] = $key;
        }

        foreach ($bst_data[0] as $key => $value) 
        {
            $array2[] = $key;
        }
        $as = array();
        $bst = array();
        $select_as = array();
        $select_bst = array();

        for ($i = 0; $i < count($as_data); $i++) 
        {   
        	$road_number = substr((int)$as_data[$i][$array1[0]], 3, 3);
            $road_number_supplement = substr((int)$as_data[$i][$array1[0]], 6, 3);
            $road_category = substr((int)$as_data[$i][$array1[0]], 2, 1);

			$branch = tblBranch::where('road_number', $road_number)
                            ->where('road_number_supplement', $road_number_supplement)
                            ->where('road_category', $road_category)
                            ->where('branch_number', '00')
                            ->first();
			if ($branch) 
			{
				$as[] = [$branch->{"name_$lang_cd"}, $as_data[$i][$array1[1]], $as_data[$i][$array1[2]]];
			}
    
            $select_as[] = $as_data[$i][$array1[0]];
        }

        $epsilon_bm = (float) $array1[2];
        $as[] = [strtoupper($array1[0]), $array1[1], $epsilon_bm/pow(10, strlen($epsilon_bm) - 1)];

        for ($i = 0; $i < count($bst_data); $i++) 
        {
            $road_number = substr((int)$bst_data[$i][$array2[0]], 3, 3);
            $road_number_supplement = substr((int)$bst_data[$i][$array2[0]], 6, 3);
            $road_category = substr((int)$bst_data[$i][$array2[0]], 2, 1);
			$branch = tblBranch::where('road_number', $road_number)
	            ->where('road_number_supplement', $road_number_supplement)
	            ->where('road_category', $road_category)
	            ->where('branch_number', '00')
	            ->first();
			if ($branch) 
            {
				$bst[] = [$branch->{"name_$lang_cd"}, $bst_data[$i][$array2[1]], $bst_data[$i][$array2[2]]];
			}

            $select_bst[] = $bst_data[$i][$array2[0]];
        }
        $epsilon_bm = (float) $array2[2];
        $bst[] = [strtoupper($array2[0]), $array2[1], $epsilon_bm/pow(10, strlen($epsilon_bm) - 1)];

        $array_as_bst = array_merge($select_as, $select_bst);
        $select_all = [];
		$array_select = [];

        foreach ($array_as_bst as $aa)
        {
            $road_number = substr((int)$aa, 3, 3);
            $road_number_supplement = substr((int)$aa, 6, 3);
            $road_category = substr((int)$aa, 2, 1);

            $branch = tblBranch::where('road_number', $road_number)
                        ->where('road_number_supplement', $road_number_supplement)
                        ->where('branch_number', '00')
                        ->where('road_category', $road_category)
                        ->first();

            if ($branch)
            {
                $array_select[] = $branch;
            }
        }

		foreach (array_unique($array_select) as $a)
		{
			$select_all[] = ['id' => @$a->id, 'name_en' => @$a->name_en, 'name_vn' => @$a->name_vn];
		}
		
        $data = [$muy, $log, $as, $bst, $select_all];
        return $data;
    }

    function getDataTableSection(Request $request)
    {
        try
        {
            if ($request->distress == 1)
            {
                $link = 'crack';
            }
            else if ($request->distress == 2)
            {
                $link = 'rut';
            }
            else if ($request->distress == 3)
            {
                $link = 'IRI';
            }
            if ($request->route == 0)
            {
                $file = 'epsilon31.csv';
            }
            else
            {
                $file = 'epsilon32.csv';
            }
            $deterioration = tblDeterioration::find($request->deterioration);
            $data_table_load = [];
    
            $source = 'application/process/deterioration/' . $deterioration->id . '/' . $link . '/output4/' . $file;
            Helper::getRowDataChunk($source, 1024, function($chunk, &$handle, $iteration) use (&$data_table_load) {
                $array_cake = explode(',', $chunk);
                if (count($array_cake) < 2)
                {
                    return null;   
                }
                for ($i = 1; $i <= count($array_cake) - 1; $i++)
                {
                    $array_cake[$i] = ($i == 1) ? intval($array_cake[$i]) : Helper::strToFloat($array_cake[$i]);
                }
                array_pop($array_cake);
                $string = $array_cake[0];
                
                if ($string == trim("BM"))
                {
                    $data_table_load[] = [$array_cake[0], ' ', ' ', ' ', $array_cake[1], $array_cake[2]];
                }
                else if (strlen($string) == 17)
                {
                    $load_section_string = Helper::subString($string);
                    $data_table_load[] = [$load_section_string[0], $load_section_string[2], $load_section_string[3], $load_section_string[4], $array_cake[1], $array_cake[2]];
                }
            }, 1000000);
            // dd($data_table_load);
            return $data_table_load;
        }
        catch (\Exception $e)
        {
            dd($e->getMessage());
        }
    }
    
    public function fileExport($id = '')
    { 
        try
        {
            $public_dir = public_path() . '/application/process/deterioration/' . $id;
            $zip_file_name = 'Bench-mark-route.zip';

            $zip = new \ZipArchive;
            if ($zip->open($public_dir . '/' . $zip_file_name, \ZIPARCHIVE::CREATE) === TRUE) 
            {    
                $zip->addFile($public_dir . '/crack/bench-mark.xlsx', 'bench-mark-crack.xlsx');
                $zip->addFile($public_dir . '/rut/bench-mark.xlsx', 'bench-mark-rut.xlsx');
                $zip->addFile($public_dir . '/IRI/bench-mark.xlsx', 'bench-mark-iri.xlsx');

                $zip->addFile($public_dir . '/crack/pavement-type.xlsx', 'pavement-type-crack.xlsx');
                $zip->addFile($public_dir . '/rut/pavement-type.xlsx', 'pavement-type-rut.xlsx');
                $zip->addFile($public_dir . '/IRI/pavement-type.xlsx', 'pavement-type-iri.xlsx');

                $zip->addFile($public_dir . '/crack/route.xlsx', 'route-crack.xlsx');
                $zip->addFile($public_dir . '/rut/route.xlsx', 'route-rut.xlsx');
                $zip->addFile($public_dir . '/IRI/route.xlsx', 'route-iri.xlsx');

                $zip->addFile($public_dir . '/crack/data/input.csv', 'input-crack.csv');
                $zip->addFile($public_dir . '/rut/data/input.csv', 'input-rut.csv');
                $zip->addFile($public_dir . '/IRI/data/input.csv', 'input-iri.csv');
                
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
