<?php

namespace App\Http\Controllers\FrontEnd\Deterioration;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App, Excel, Hash, Helper;
use App\Models\tblConditionRank;
use App\Models\tblDeterioration;
use Box\Spout\Reader\ReaderFactory;
use Box\Spout\Writer\WriterFactory;
use Box\Spout\Common\Type;
use Box\Spout\Writer\Style\Border;
use Box\Spout\Writer\Style\BorderBuilder;
use Box\Spout\Writer\Style\Color;
use Box\Spout\Writer\Style\StyleBuilder;
use App\Models\tblRoad;
use App\Models\tblOrganization;
use App\Models\tblBranch;
use App\Models\mstRoadCategory;

class SectionController extends DeteriorationController
{
    function index($session_id)
    {
    	try
        {
            $deterioration = tblDeterioration::find($session_id);
            if ($deterioration->section_flg != 6)
            {
                if ($deterioration->benchmark_flg == 3 && $deterioration->pav_type_flg == 6 && $deterioration->route_flg == 6)
                {
                    return view('front-end.layouts.templates.loading')->with(array(
                        'name_step' => 'section_flg',
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

            $load = Excel::load('application/process/deterioration/'.$session_id.'/crack/output2/output0.csv')->get();
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
            $excel = [];

            return view('front-end.deterioration.section')->with(array(
                'muy' => $muy,
                'log' => $log,
                'deterioration' => $deterioration
            ));
        } 
        catch (\Exception $e)
        {
            dd($e);
        }
    }

    public function fileExport($id='', $option='')
    {
        try
        {
            $public_dir = public_path() . '/application/process/deterioration/' . $id;
            $zip_file_name = 'Bench-mark-section.zip';

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

                $zip->addFile($public_dir . '/crack/section.xlsx', 'section-crack.xlsx');
                $zip->addFile($public_dir . '/rut/section.xlsx', 'section-rut.xlsx');
                $zip->addFile($public_dir . '/IRI/section.xlsx', 'section-iri.xlsx');

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

    public function ExportAdminDB(Request $request)
    {
        \DB::beginTransaction();
        try
        {
            $deterioration = tblDeterioration::find($request->id);
            tblDeterioration::withTrashed()
                ->where('year_of_dataset', $deterioration->year_of_dataset)
                ->where('organization_id', $deterioration->organization_id)
                ->where('dataset_flg', 1)
                ->update(['dataset_flg' => 0]);
            
            $deterioration = tblDeterioration::find($request->id);
            $deterioration->dataset_flg = 1;
            $deterioration->save();
            \DB::commit();
        }
        catch (\Exception $e)
        {
            \DB::rollBack();
            dd($e->getMessage());
        }          
        return "success";
    }
}
