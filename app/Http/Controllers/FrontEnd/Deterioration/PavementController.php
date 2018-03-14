<?php

namespace App\Http\Controllers\FrontEnd\Deterioration;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App, Excel, Hash, Helper;
use App\Models\tblRoad;
use App\Models\tblConditionRank;
// use App\Models\tblDeteriorationOrganization;
use App\Models\tblDeterioration;

class PavementController extends DeteriorationController
{
    function index($session_id)
    {
        // $deterioration_organization = tblDeteriorationOrganization::where('deterioration_id', $session_id)->get();
        $deterioration = tblDeterioration::find($session_id);
        if ($deterioration->pav_type_flg != 6)
        {
            if ($deterioration->benchmark_flg == 3)
            {
                return view('front-end.layouts.templates.loading')->with(array(
                    'name_step' => 'pavement_type',
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
        if ($deterioration->distress_type == 1 )
        {
            $link = 'crack';
        }
        else if ($deterioration->distress_type == 2 )
        {
            $link = 'rut';
        }
        else if ($deterioration->distress_type == 3 )
        {
            $link = 'IRI';
        }
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
        $load_as_bst_cc = Excel::load('public/application/process/deterioration/'.$session_id.'/crack/output4/epsilon10.csv')->get();
        $array1 = array();
        foreach ($load_as_bst_cc[0] as $key => $value) 
        {
            $array1[] = $key;
        }
        // $as = array();
        // $bst = array();
        // $select_as = array();
        // $select_bst = array();
        $as = '';
        $bst = '';
        $cc = '';
        for ($i=0; $i < count($load_as_bst_cc); $i++) 
        {
            // $as[] = [$load_as_bst_cc[$i][$array1[0]], $load_as_bst_cc[$i][$array1[1]], $load_as_bst_cc[$i][$array1[2]]];
            // $select_as[] = $load_as_bst_cc[$i][$array1[0]];
            if ($load_as_bst_cc[$i][$array1[0]] == 1)
            {
                $as = $load_as_bst_cc[$i][$array1[2]];
            }
            else if ($load_as_bst_cc[$i][$array1[0]] == 2)
            {
                $bst = $load_as_bst_cc[$i][$array1[2]];
            }
            else if ($load_as_bst_cc[$i][$array1[0]] == 3)
            {
                $cc = $load_as_bst_cc[$i][$array1[2]];
            }
        }
        $data = array();
        $parameter = Excel::load('public/application/process/deterioration/'.$session_id.'/crack/output2/output0.csv')->get();
        for ($i = 0; $i < count($parameter); $i++)
        {
            if (trim($parameter[$i]['hazard_parameters']) == 'fai')
            {
                break;
            }
            else
            {
                $data[] = trim($parameter[$i]['hazard_parameters']);
            }
        }
        $data_chart = array();
        for ($i = 0; $i <= count($data); $i++)
        {
            if ($i == 0)
            {
                $data_chart[] = 0;
            }
            else
            {
                $data_chart[] = 1/($data[$i-1]) + $data_chart[$i-1];
            }
        }
        $json = Helper::convertJsonConditionRank($deterioration->condition_rank)['crack'];
        // $data_chart = array();
        // function recursive($data, $data_chart)
        // {
        //     // $data_chart = array();
        //     $n = count($data);
        //     if ($n == 1)
        //     {
        //         $data_chart[] = 0;
        //         // dd($data_chart);
        //         return $data_chart;
        //     }
        //     else
        //     {
        //         $data_chart[] = 1/array_pop($data) + array_pop($data_chart);
        //         unset($data[$n-1]);
        //         recursive($data, $data_chart);
        //     }
        // }
        // $test = array();
        // $test = recursive($data, $data_chart);
        // dd($test);
        // dd(recursive($data, $data_chart));
        return view('front-end.deterioration.pavement')->with(array(
            // 'deterioration_organization' => $deterioration_organization,
            'deterioration' => $deterioration,
            'muy' => $muy,
            'log' => $log,
            'as' => $as,
            'bst' => $bst,
            'cc' => $cc,
            'data_chart' => $data_chart,
            'json' => $json
        ));
    }

    /**
     * $link: rut|crack|IRI
     */
    private function _getBMData($deterioration, $distress_type)
    {
        if ($distress_type == 1)
        {
            $json = Helper::convertJsonConditionRank($deterioration->condition_rank)['crack'];
            $link = 'crack';
        }
        else if ($distress_type == 3)
        {
            $json = Helper::convertJsonConditionRank($deterioration->condition_rank)['iri'];
            $link = 'IRI';
        }
        else if ($distress_type == 2)
        {
            $json = Helper::convertJsonConditionRank($deterioration->condition_rank)['rut'];
            $link = 'rut';
        }
        $load = Excel::load('public/application/process/deterioration/'.$deterioration->id.'/'.$link.'/output1/output.csv')->get();

        $hazard_parameter = [];
        $t_value = [];
        $cache = '';
        
        for ($i = 0; $i < count($load) ; $i++) 
        { 
        // dd($load[6]['unknown_parameter_beta']);
            if (trim($load[$i]['unknown_parameter_beta']) != 't-value')
            {
                $hazard_parameter[] = $load[$i]['unknown_parameter_beta'];
            }
            else
            {
                $cache = $i;
                break;
            }
        }
        
        for ($i = $cache+1; $i < count($load); $i++) 
        { 
            $t_value[] = $load[$i]['unknown_parameter_beta'];
        }
        
        $data_chart_BM = [];
        for ($i = 0; $i <= count($hazard_parameter); $i++)
        {
            if ($i == 0)
            {
                $data_chart_BM[] = 0;
            }
            else
            {
                $data_chart_BM[] = round(1/($hazard_parameter[$i-1]) + $data_chart_BM[$i-1], 2);
            }
        }
        $chart_data_BM = [];
        
        for ($i = 0; $i <= count($hazard_parameter); $i++)
        {
            $chart_data_BM[] = [$data_chart_BM[$i], $json[$i]['from']];
        }
        return $chart_data_BM;
    }

    /**
     * distress type: 1: crack, 2: rut, 3: iri
     * route: 0: AC, 1: BST, 2: CC
     */
    private function _getChartDataForPavementType($deterioration, $distress_type, $route)
    {
        if ($distress_type == 1)
        {
            $json = Helper::convertJsonConditionRank($deterioration->condition_rank)['crack'];
            $link = 'crack';
        }
        else if ($distress_type == 3)
        {
            $json = Helper::convertJsonConditionRank($deterioration->condition_rank)['iri'];
            $link = 'IRI';
        }
        else if ($distress_type == 2)
        {
            $json = Helper::convertJsonConditionRank($deterioration->condition_rank)['rut'];
            $link = 'rut';
        }
        if ($route == 0)
        {
            $file = 'para1.csv';
        }
        else if ($route == 1)
        {
            $file = 'para2.csv';
        }
        else if ($route == 2)
        {
            $file = 'para3.csv';
        }
        $parameter = Excel::load('public/application/process/deterioration/'.$deterioration->id.'/'.$link.'/output3/'.$file)->get();

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

    function getAllPavementTypeCurve(Request $request)
    {
        try 
        {
            $deterioration = tblDeterioration::find($request->deterioration);
            
            $data_draw_chart = [];
            $data_draw_chart[] = $this->_getBMData($deterioration, $request->distress_type);
            $data_draw_chart[] = $this->_getChartDataForPavementType($deterioration, $request->distress_type, 0);
            $data_draw_chart[] = $this->_getChartDataForPavementType($deterioration, $request->distress_type, 1);
            $data_draw_chart[] = $this->_getChartDataForPavementType($deterioration, $request->distress_type, 2);
            return $data_draw_chart;
        }
        catch (\Exception $e)
        {
            dd($e);
        }
    }
    
	//curve
    function getDataPaymentPerformance(Request $request)
    {
        try 
        {
	    	$deterioration = tblDeterioration::find($request->deterioration);
	    	
            $data_draw_chart = [];
            $data_draw_chart[] = $this->_getBMData($deterioration, $request->distress_type);
            $data_draw_chart[] = $this->_getChartDataForPavementType($deterioration, $request->distress_type, $request->route);
	    	return $data_draw_chart;
        }
        catch (\Exception $e)
        {
            dd($e);
        }
    }

    //disstress
    public function getDataPaymentDisstress(Request $request)
    {
    	// $deterioration_organization = tblDeteriorationOrganization::where('deterioration_id', $request->deterioration)->get();
    	$deterioration = tblDeterioration::find($request->deterioration);
    	if ($request->distress_type == 1)
    	{
    		$link = 'crack';
    	}
    	else if ($request->distress_type == 3)
    	{
    		$link = 'IRI';
    	}
    	else if ($request->distress_type == 2)
    	{
    		$link = 'rut';
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
        $load_as_bst_cc = Excel::load('public/application/process/deterioration/'.$deterioration->id.'/'.$link.'/output4/epsilon10.csv')->get();
        $array1 = array();
        foreach ($load_as_bst_cc[0] as $key => $value) 
        {
            $array1[] = $key;
        }
        // $as = array();
        // $bst = array();
        // $select_as = array();
        // $select_bst = array();
        $as = '';
        $bst = '';
        $cc = '';
        for ($i=0; $i < count($load_as_bst_cc); $i++) 
        {
            // $as[] = [$load_as_bst_cc[$i][$array1[0]], $load_as_bst_cc[$i][$array1[1]], $load_as_bst_cc[$i][$array1[2]]];
            // $select_as[] = $load_as_bst_cc[$i][$array1[0]];
            if ($load_as_bst_cc[$i][$array1[0]] == 1)
            {
                $as = $load_as_bst_cc[$i][$array1[2]];
            }
            else if ($load_as_bst_cc[$i][$array1[0]] == 2)
            {
                $bst = $load_as_bst_cc[$i][$array1[2]];
            }
            else if ($load_as_bst_cc[$i][$array1[0]] == 3)
            {
                $cc = $load_as_bst_cc[$i][$array1[2]];
            }
        }
        $data = array();
        $json = Helper::convertJsonConditionRank($deterioration->condition_rank)['crack'];
        $data = [];
        $data[] = [$muy, $log, trim($as), trim($bst), trim($cc)];
        return $data;
    }

    //probabilities
    function getDataPaymentProbabilities(Request $request)
    {
    	// $deterioration_organization = tblDeteriorationOrganization::where('deterioration_id', $request->deterioration)->get();
    	$deterioration = tblDeterioration::find($request->deterioration);
    	if ($request->distress_type == 1)
    	{
    		$link = 'crack';
    		$data_title = [];
    		$json = Helper::convertJsonConditionRank($deterioration->condition_rank);
    		for ($i=0; $i<count($json['crack']); $i++)
    		{
    			// $data_title[] = $json['crack'][$i]['from']." <C<= ".$json['crack'][$i]['to'];
                $data_title[] = Helper::convertConditionInforToText($json['crack'][$i]['from'], $json['crack'][$i]['to'], 'C');
    		}
    	}
    	else if ($request->distress_type == 3)
    	{
    		$link = 'IRI';
    		$data_title = [];
    		$json = Helper::convertJsonConditionRank($deterioration->condition_rank);
    		for ($i=0; $i<count($json['iri']); $i++)
    		{
    			// $data_title[] = $json['iri'][$i]['from']." <I<= ".$json['crack'][$i]['to'];
                $data_title[] = Helper::convertConditionInforToText($json['iri'][$i]['from'], $json['iri'][$i]['to'], 'IRI');
    		}
    	}
    	else if ($request->distress_type == 2)
    	{
    		$link = 'rut';
    		$data_title = [];
    		$json = Helper::convertJsonConditionRank($deterioration->condition_rank);
    		for ($i=0; $i<count($json['rut']); $i++)
    		{
    			// $data_title[] = $json['rut'][$i]['from']." <R<= ".$json['crack'][$i]['to'];
                $data_title[] = Helper::convertConditionInforToText($json['rut'][$i]['from'], $json['rut'][$i]['to'], 'R');
    		}
    	}
    	if ($request->route == 0)
    	{
    		$file = 'transition_1.csv';
    	}
    	else if ($request->route == 1)
    	{
    		$file = 'transition_2.csv';
    	}
    	else if ($request->route == 2)
    	{
    		$file = 'transition_3.csv';
    	}
    	$data = [];
    	$header = [];
    	$data_cache = [];
    	$inputFileType = 'CSV';
        $inputFileName = '../public/application/process/deterioration/'.$deterioration->id.'/'.$link.'/output3/'.$file;
        $objReader = \PHPExcel_IOFactory::createReader($inputFileType);
        $objPHPExcel = $objReader->load($inputFileName);
        $worksheet = $objPHPExcel->getActiveSheet();
        foreach ($worksheet->getRowIterator() as $row) 
        {
        	$array_cake = [];
            $cellIterator = $row->getCellIterator();
            $cellIterator->setIterateOnlyExistingCells(false); // Loop all cells, even if it is not set
            foreach ($cellIterator as $cell) 
            {
            	$array_cake[] = trim($cell->getValue())*100;
            }
            $header[] = trim($array_cake[0])/100;
            array_shift($array_cake);
            $data_cache[] = array_reverse($array_cake);
        }
        // $data_cache[] = array_reverse($array_cake[0]);
        $data_chart = [];
        for ($i=0; $i < count($data_cache[0]); $i++) 
        { 
        	$cache = [];
        	for ($j=0; $j < count($data_cache); $j++) 
        	{
        		$cache[] = round($data_cache[$j][$i], 5);
        	}
        	$data_chart[] = $cache;
        }
        // dd($data_chart);
        $data[] = $data_chart;
        $data[] = $header;
    	$data[] = array_reverse($data_title);
        if (count($data_title) > 0)
        {
            return $data;
        }
        else 
        {
            return [[], [], []];
        }
    }

    function getDataPavementMatrix(Request $request)
    {
    	// $deterioration_organization = tblDeteriorationOrganization::where('deterioration_id', $request->deterioration)->get();
    	$deterioration = tblDeterioration::find($request->deterioration);
    	if ($request->distress_type == 1)
    	{
    		$link = 'crack';
    	}
    	else if ($request->distress_type == 3)
    	{
    		$link = 'IRI';
    	}
    	else if ($request->distress_type == 2)
    	{
    		$link = 'rut';
    	}
    	if ($request->route == 0)
    	{
    		$file = 'matrix_1.csv';
    	}
    	else if ($request->route == 1)
    	{
    		$file = 'matrix_2.csv';
    	}
    	else if ($request->route == 2)
    	{
    		$file = 'matrix_3.csv';
    	}
    	// $load_matrix = Excel::load('public/application/process/deterioration/binhln/'.$link.'/output3/'.$file)->get();
    	$data = [];
    	$inputFileType = 'CSV';
    	$inputFileName = '../public/application/process/deterioration/'.$deterioration->id.'/'.$link.'/output3/'.$file;
        $objReader = \PHPExcel_IOFactory::createReader($inputFileType);
        $objPHPExcel = $objReader->load($inputFileName);
        $worksheet = $objPHPExcel->getActiveSheet();
        foreach ($worksheet->getRowIterator() as $row)
        {
        	$data_cache = [];
            $cellIterator = $row->getCellIterator();
            $cellIterator->setIterateOnlyExistingCells(false); // Loop all cells, even if it is not set
            foreach ($cellIterator as $cell) 
            {
            	$data_cache[] = (float) $cell->getValue();
            }
            $data[] = $data_cache;
        }
        return $data;
    }
    public function fileExport(Request $request, $id = '')
    {
        try
        {
            $public_dir = public_path() . '/application/process/deterioration/' . $id;
            $zip_file_name = 'Bench-mark-pavement-type.zip';

            $zip = new \ZipArchive;
            if ($zip->open($public_dir . '/' . $zip_file_name, \ZIPARCHIVE::CREATE) === TRUE) 
            {
                $zip->addFile($public_dir . '/crack/bench-mark.xlsx', 'bench-mark-crack.xlsx');
                $zip->addFile($public_dir . '/rut/bench-mark.xlsx', 'bench-mark-rut.xlsx');
                $zip->addFile($public_dir . '/IRI/bench-mark.xlsx', 'bench-mark-iri.xlsx');

                $zip->addFile($public_dir . '/crack/pavement-type.xlsx', 'pavement-type-crack.xlsx');
                $zip->addFile($public_dir . '/rut/pavement-type.xlsx', 'pavement-type-rut.xlsx');
                $zip->addFile($public_dir . '/IRI/pavement-type.xlsx', 'pavement-type-iri.xlsx');

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
