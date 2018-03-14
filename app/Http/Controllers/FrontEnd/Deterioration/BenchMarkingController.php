<?php

namespace App\Http\Controllers\FrontEnd\Deterioration;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App, Excel, Hash, Helper;
use App\Models\tblRoad;
use App\Models\tblConditionRank;
use Box\Spout\Reader\ReaderFactory;
// use App\Models\tblDeteriorationOrganization;
use App\Models\tblDeterioration;

class BenchMarkingController extends DeteriorationController
{
    function index($session_id)
    {
      
        // $deterioration_organization = tblDeteriorationOrganization::where('deterioration_id', $session_id)->get();
        $deterioration = tblDeterioration::find($session_id);

        if ($deterioration->benchmark_flg != 3)
        {
            return view('front-end.layouts.templates.loading')->with(array(
                'name_step' => 'benchmarking',
                'process' => 'deterioration',
                'timer' => '5000',
                'id' => $deterioration->id,
            ));
        }
        
        return view('front-end.deterioration.benchmarking')->with(array(
            'deterioration' => $deterioration
            // 'muy' => $muy,
            // 'log' => $log
        ));
    }
    
    //curve
    function getDataBenchmarkingHazard(Request $request)
    {
        try
        {
        	$deterioration = tblDeterioration::find($request->deterioration);
            $link;
            $condition_rank;
        	switch ($request->distress_type)
        	{
                case 1:
            		$link = 'crack';
            		$json = json_decode($deterioration->condition_rank);
                    
            		$condition_rank = [];
            		for ($i=0; $i < count($json); $i++) 
            		{
            		    if ($json[$i]->target_type == 1)
                        {
        			         $condition_rank[] = $json[$i]->from;
                        }
            		}
                    break;
                case 2:
                    $link = 'rut';
                    $json = json_decode($deterioration->condition_rank);
                    $condition_rank = [];
                    for ($i=0; $i < count($json); $i++) 
                    {
                        if ($json[$i]->target_type == 2)
                        {
                             $condition_rank[] = $json[$i]->from;
                        }
                    }
                    break;
                case 3:
                    $link = 'IRI';
                    $json = json_decode($deterioration->condition_rank);
                    $condition_rank = [];
                    for ($i=0; $i < count($json); $i++) 
                    {
                        if ($json[$i]->target_type == 3)
                        {
                            $condition_rank[] = $json[$i]->from;
                        }
                    }
                    break;
                default:
                    throw new Exception("Invalid Distress type", 1);
                    break;
        	}
             
            //bm
        	$load = Excel::load('public/application/process/deterioration/'.$deterioration->id.'/'.$link.'/output1/output.csv')->get();
        	$hazard_parameter = [];
        	$t_value = [];
        	$cache = '';

        	for ($i = 0; $i < count($load); $i++) 
        	{ 
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
        	$data_chart = [];

            for ($i = 0; $i <= count($hazard_parameter); $i++)
            {
                if ($i == 0)
                {
                    $data_chart[] = 0;
                }
                else
                {
                    $data_chart[] = 1/($hazard_parameter[$i-1]) + $data_chart[$i-1];
                }
            }
            
            foreach ($data_chart as $k => $v) 
            {
                $data_chart[$k] = round($v, 2);    
            }
        	
            $data = [];
            $data[] = $condition_rank;
            $data[] = $hazard_parameter;
            $data[] = $t_value;
            $data[] = $data_chart;
            return $data;
        }
        catch (\Exception $e)
        {
            dd($e);
        }
    }

    function getDataBenchmarkingProbabilities(Request $request)
    {
    	// $deterioration_organization = tblDeteriorationOrganization::where('deterioration_id', $request->deterioration)->get();
    	$deterioration = tblDeterioration::find($request->deterioration);
        $link;
        $data_title = [];
    	if ($request->distress_type == 1)
    	{
    		$link = 'crack';
    		$json = Helper::convertJsonConditionRank($deterioration->condition_rank);
    		for ($i = 0; $i < count($json['crack']); $i++)
    		{
    			$data_title[] = Helper::convertConditionInforToText($json['crack'][$i]['from'], $json['crack'][$i]['to'], 'C');
            }

    	}
    	else if ($request->distress_type == 3)
    	{
    		$link = 'IRI';
    		// $data_title = [];
    		$json = Helper::convertJsonConditionRank($deterioration->condition_rank);
    		for ($i = 0; $i < count($json['iri']); $i++)
    		{
                $data_title[] = Helper::convertConditionInforToText($json['iri'][$i]['from'], $json['iri'][$i]['to'], 'IRI');
    		}
    	}
    	else if ($request->distress_type == 2)
    	{
    		$link = 'rut';
    		// $data_title = [];
    		$json = Helper::convertJsonConditionRank($deterioration->condition_rank);
    		for ($i=0; $i<count($json['rut']); $i++)
    		{
                $data_title[] = Helper::convertConditionInforToText($json['rut'][$i]['from'], $json['rut'][$i]['to'], 'R');
    		}
    	}
    	$data = [];
    	$header = [];
    	$data_cache = [];
    	$inputFileType = 'CSV';
        $inputFileName = '../public/application/process/deterioration/'.$deterioration->id.'/'.$link.'/output1/transition.csv';
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
            	$array_cake[] = round((float) $cell->getValue()*100, 4);
            }
            $header[] = trim($array_cake[0])/100;
            array_shift($array_cake);
            $data_cache[] = array_reverse($array_cake);
        }
        
        $data_chart = [];
        for ($i=0; $i < count($data_cache[0]); $i++) 
        { 
        	$cache = [];
        	for ($j=0; $j < count($data_cache); $j++) 
        	{
        		$cache[] = $data_cache[$j][$i];
        	}
        	$data_chart[] = $cache;
        }
        $data[] = $data_chart;
        $data[] = $header;
    	$data[] = array_reverse($data_title);
        return $data;
    }

    function getDataBenchmarkingMatrix(Request $request)
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
    	// $load_matrix = Excel::load('public/application/process/deterioration/binhln/'.$link.'/output3/'.$file)->get();
    	$data = [];
    	$inputFileType = 'CSV';
    	$inputFileName = '../public/application/process/deterioration/'.$deterioration->id.'/'.$link.'/output1/matrix.csv';
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

    // public function fileExport(Request $request, $id='', $option='')
    public function fileExport(Request $request, $id = '')
    {
        try
        {
            $public_dir = public_path() . '/application/process/deterioration/' . $id;
            $zip_file_name = 'Bench-mark.zip';
            // file_put_contents($public_dir . '/' . $zip_file_name, '');
            $zip = new \ZipArchive;
            if ($zip->open($public_dir . '/' . $zip_file_name, \ZIPARCHIVE::CREATE) === TRUE) 
            {    
                $zip->addFile($public_dir . '/crack/bench-mark.xlsx', 'bench-mark-crack.xlsx');
                $zip->addFile($public_dir . '/rut/bench-mark.xlsx', 'bench-mark-rut.xlsx');
                $zip->addFile($public_dir . '/IRI/bench-mark.xlsx', 'bench-mark-iri.xlsx');
                
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
