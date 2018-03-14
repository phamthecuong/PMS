<?php

namespace App\Http\Controllers\FrontEnd\DAS;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use App\Models\tblOrganization;
use App\Models\tblSegment;
use App\Models\tblSectiondataMH;
use App\Models\tblSectionPCHistory;
use App\Classes\Helper;
use DB, App;
use Excel;

class DASController extends Controller
{
    var $sheet = [
        '1' => '001_target_data_1_summary_table',
        '2' => '001_target_data_2_summary_table',
        '3' => '001_target_data_3_summary_table',
        '4' => '001_target_data_4_summary_table',
        '5' => '001_target_data_5_summary_table'
    ];

    var $distress = [
        '1' => 'cracking_ratio_total',
        '2' => 'IRI',
        '3' => 'rutting_depth_max',
        '4' => 'rutting_depth_ave',
        '5' => 'MCI'
    ];

    public function __construct()
    {
       $this->middleware('dppermission:DAS.view');
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $organization_id = 1;
        if (Auth::user()->hasRole('userlv2'))
        {
            $organization_id = Auth::user()->organization_id;
        }
        return view('front-end.DAS.index', ['organization_id' => $organization_id]);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }

    public function getExportSummaryRNetwork(Request $request)
    {
        $data = [];
        $lang = \App::getLocale() == 'en' ? 'en' : 'vn';
        if (!empty($request->sb))
        {
            $sb_id = [$request->sb];
            $sb_name = tblOrganization::where('parent_id', $request->rmb)->first()->{'name_' . $lang}; 
            $excel_name = tblOrganization::where('id', $request->sb)->first()->{'name_' . $lang};
            $branch_id = DB::table('tblSection_PC_history')->where('SB_id', $sb_id)->where('branch_id', '!=', 0)
                ->groupBy('branch_id')->pluck('branch_id')->toArray();
        }
        else
        {
            $sb_id = tblOrganization::where('parent_id', $request->rmb)->pluck('id')->toArray();
            $sb_name = ($lang == 'en') ? 'All' : 'Tất cả';
            $excel_name = '';
            $branch_id = DB::table('tblSection_PC_history')->whereIn('SB_id', $sb_id)->where('branch_id', '!=', 0)
                ->groupBy('branch_id')->pluck('branch_id')->toArray();
        }
        $rmb_name = tblOrganization::where('id', $request->rmb)->first();
        
        $result = DB::table('tblSection_PC_history')
            ->select('date_y', 'surface_type', DB::raw('sum(section_length) as section_length'))
            ->whereIn('SB_id', $sb_id)
            ->whereIn('branch_id', $branch_id)
            ->groupBy('date_y')
            ->groupBy('surface_type')
            ->orderBy('date_y', 'desc')
            ->get();

//        $segment = tblSegment::whereIn('SB_id', $sb_id)->pluck('id')->toArray();

        foreach ($result as $r)
        {
            // $sql = "select (count(id) * 100) as total_length from (SELECT * FROM `tblPMS_RI_info` WHERE segment_id IN (" . implode(',', $segment) . ') ';

            $data[$r->date_y]['year'] = $r->date_y;
            $data[$r->date_y]['pc_' . strtolower($r->surface_type)] = $r->section_length;
            if (!isset($data[$r->date_y]['pc_total']))
            {
                $data[$r->date_y]['pc_total'] = 0;
            }
            $data[$r->date_y]['pc_total']+= $r->section_length;
        }

        foreach ($data as $key => $value) 
        {
            $data[$key]['ri_ac'] = \App\Classes\Helper::calculateIntegratedRILength($request->rmb, $request->sb, null, $key, 1);
            $data[$key]['ri_bst'] = \App\Classes\Helper::calculateIntegratedRILength($request->rmb, $request->sb, null, $key, 2);
            $data[$key]['ri_cc'] = \App\Classes\Helper::calculateIntegratedRILength($request->rmb, $request->sb, null, $key, 3);
            $data[$key]['ri_tt'] = $data[$key]['ri_ac'] + $data[$key]['ri_bst'] + $data[$key]['ri_cc'];
        }

        $data_export = [];
        foreach (array_values($data) as $value) 
        {
            $arr = [];
            $arr['year'] = (int)$value['year'];
            $arr['ri_ac'] = (int)$value['ri_ac'];
            $arr['pc_ac'] = isset($value['pc_ac']) ? (int)$value['pc_ac'] : 0;
            $arr['ri_bst'] = (int)$value['ri_bst'];
            $arr['pc_bst'] = isset($value['pc_bst']) ? (int)$value['pc_bst'] : 0;
            $arr['ri_cc'] = (int)$value['ri_cc'];
            $arr['pc_cc'] = isset($value['pc_cc']) ? (int)$value['pc_cc'] : 0;
            $arr['ri_tt'] = (int)$value['ri_tt'];
            $arr['pc_total'] = isset($value['pc_total']) ? (int)$value['pc_total'] : 0;
            $data_export[] = $arr;
        }
        
        $tpl_file = public_path('excel_templates/DAS/SummaryRNetwork/DAS_SummaryOfRoadNetworkStaticAndPCLength_'.strtoupper($lang) . '.xlsx');
        Excel::load($tpl_file,  function ($reader) use($data_export, $rmb_name, $sb_name, $request, $excel_name) {
            $reader->sheet(0, function($sheet) use ($data_export, $rmb_name, $sb_name, $excel_name) {
                $sheet->fromArray($data_export, NULL, 'C11', null, false);
                $sheet->cell('E4', function ($cell) use ($rmb_name) {
                    $cell->setValue($rmb_name->organization_name);
                });
                if ($excel_name != '')
                {
                    $sheet->cell('E5', function ($cell) use ($excel_name) {
                        $cell->setValue($excel_name);
                    });
                }
                else
                {
                    $sheet->cell('E5', function ($cell) use ($sb_name) {
                        $cell->setValue($sb_name);
                    });
                }
            });
            setcookie(
                'fileDownloadTokenRN',
                $request->downloadTokenValueRN,
                time() + 60*60           // expires January 1, 2038
            );
        })
            ->setFilename('DAS_SummaryOfRoadNetworkStaticAndPCLength_'.strtoupper($lang))
            ->download('xlsx');
    }

    public function _condition($segment_id) 
    {
        $data = tblSectiondataMH::with('segment', 'segment.tblOrganization')->selectRaw('YEAR(survey_time) as year, sum(actual_length) as total, segment_id')
                        ->whereIn('segment_id', $segment_id)
                        ->groupBy(DB::raw('YEAR(survey_time)'))
                        ->orderBy(DB::raw('year'), 'desc')
                        ->get();
        return $data;
    }

    public function getExportTransitionPC(Request $request)
    {
        $lang = \App::getLocale();
        $name_check = App::getLocale() == 'en' ? 'name_en' : 'name_vn';
        $distress_type = null;
        $tpl_file = public_path('excel_templates/DAS/TransitionPC/DAS_TranstationOfPavementCondition_'. strtoupper($lang). '.xlsx');
        include_once public_path("../lib/eiseXLSX/eiseXLSX.php");
        $xlsx = new \eiseXLSX($tpl_file);
            foreach ($this->distress as $key_d => $value_d)
            {
                $xml = $xlsx->arrXMLs['/xl/worksheets/sheet'. (2 * $key_d).'.xml'];
                $row_3 = $xml->sheetData->row[2];
                $dom = dom_import_simplexml($row_3);
                $dom->parentNode->removeChild($dom);
                //get data
                $total = $request->rmb == -1 ? Helper::calculateAVGPCIndexNotIntegrated($value_d, -1)
                    : Helper::calculateAVGPCIndexNotIntegrated($value_d, $request->rmb);
                //set table
                $table = $xlsx->arrXMLs['/xl/tables/table'. $key_d .'.xml'];
                $table['ref'] = "A2:C". (count($total) + 2);
                $table->autoFilter['ref'] = "A2:C". (count($total) + 2);
                // export data
                foreach ($total as $key => $value)
                {
                    $value->parent = tblOrganization::where('id', $value->parent)->first()->$name_check;
                    $value->total = $value_d == 'MCI' ? round($value->total, 1) : round($value->total, 2);
                    $new_row = $xml->sheetData->addChild('row');
                    foreach ($value as $k => $v)
                    {
                        $new_cell = $new_row->addChild('c');
                        if (is_numeric($v))
                        {
                            $new_cell->addAttribute('t', "n");
                            $new_v = $new_cell->addChild('v', $v);
                        }
                        else if ($v == null)
                        {
                            $new_cell->addAttribute('t', 'n');
                        }
                        else
                        {
                            $new_cell->addAttribute('t', "inlineStr");
                            $new_is = $new_cell->addChild('is');
                            if (!mb_check_encoding($v, 'utf-8')) $v = iconv("cp1250", "utf-8", $v);
                            $new_T = $new_is->addChild('t', htmlspecialchars($v));
                        }
                    }
                }
                $xlsx->arrXMLs['/xl/worksheets/sheet'. (2 * $key_d) .'.xml'] = $xml;
            }
        setcookie(
            'fileDownloadToken',
            $request->downloadTokenValue,
            time() + 60*60           // expires January 1, 2038
        );
        $xlsx->Output('DAS_TranstationOfPavementCondition_'.strtoupper($lang). '.xlsx', "D");
    }

    public function getExportSummaryMR(Request $request)
    {
        
    }

    private function __findSegment_id($request)
    {
        $branch_id = $request->route_name;
        $sb_id = $request->sb;
        $rmb_id = $request->rmb;
        $segment_id = [];
        $data = [];
        ($sb_id == NULL) ? $sb_id = -1 : $sb_id;
        ($branch_id == NULL) ? $branch_id = -1 : $sb_id;

        $segment = tblSegment::with('tblBranch')->select('id', 'branch_id', 'SB_id');
        if ($sb_id == -1  && $branch_id == -1) 
        {
            $sb_id = tblOrganization::where('parent_id', $rmb_id)->pluck('id')->toArray();
            $segment = $segment->whereIn('sb_id', $sb_id);
        }
        else if ($sb_id != -1 && $branch_id == -1)
        {
            $segment = $segment->where('sb_id', $sb_id);
        }
        else if ($sb_id == -1 && $branch_id != -1) 
        {   
            $sb_id = tblOrganization::where('parent_id', $rmb_id)->pluck('id')->toArray();
            $segment = $segment->where('branch_id', $branch_id)->whereIn('sb_id', $sb_id);
        }
        else
        {
            $segment = $segment->where('branch_id', $branch_id)
                            ->where('SB_id', $sb_id);
        }
        $lang = App::isLocale('en') ? 'en' : 'vn';
        $name = 'name_'.$lang;
        foreach ($segment->get() as $r)
        {
            $data_segment[$r->tblBranch->$name]['segment_id'][] = $r->id;
            $data_segment[$r->tblBranch->$name]['sb_id'][] = $r->SB_id;
        }
        dd($data_segment);
        return $data_segment;
    }

    private function __conditionMH($segment_id, $year, $repair_method, $repair_category)
    {   
        $data = [];
        $min_year = date('Y') - $year + 1;
        $sectiondataMH = tblSectiondataMH::selectRaw('YEAR(survey_time) as year, sum(actual_length) as total')->whereIn('segment_id', $segment_id);
        if ($repair_method != -1)
        {
            $sectiondataMH = $sectiondataMH->where('repair_method_id', $repair_method);
        }
        else if ($repair_category != -1)
        {
            $sectiondataMH = $sectiondataMH->where('r_category_id', $repair_category);
        }
        else 
        {
            $sectiondataMH = $sectiondataMH->where('r_category_id', $repair_category)
                                    ->where('repair_method_id', $repair_method);
        }
        $sectiondataMH = $sectiondataMH ->groupBy(DB::raw('YEAR(survey_time)'))
                                ->orderBy(DB::raw('YEAR(survey_time)'), 'desc')
                                ->whereRaw("YEAR(survey_time) <= YEAR(CURRENT_TIMESTAMP)")
                                ->whereRaw("YEAR(survey_time) >= $min_year")
                                ->get();
        foreach ($sectiondataMH as $r)
        {
            $data[] = ['survey_time' => $r->year, 'total_length' => $r->total];
        }
        return $data;
    }

    public function getExportSummaryPassedTime(Request $request)
    {
        $branch_id = ($request->route_name == NULL) ? -1 : substr($request->route_name, -1);
        $sb_id = ($request->sb == NULL) ? -1 : substr($request->sb, -1);
        $rmb_id = $request->rmb;
        $data = [];
        $lang = App::isLocale('en') ? 'en' : 'vn';
        $name = 'name_'.$lang;
        $rmb_name = tblOrganization::where('id', $rmb_id)->first()->$name;
        
        $record = DB::table('tblSectiondata_MH')
            ->select(DB::raw("tblBranch.{$name} as route_name, '{$rmb_name}' as RMB, tblOrganization.{$name} as SB, year(survey_time) as `year`, (YEAR(NOW()) - year(survey_time)) as elapsed_time, SUM(actual_length) as section_length"))
            ->join('tblSegment', 'tblSegment.id', '=', 'tblSectiondata_MH.segment_id')
            ->join('tblOrganization', 'tblOrganization.id', '=', 'tblSegment.SB_id')
            ->join('tblBranch', 'tblBranch.id', '=', 'tblSegment.branch_id')
            ->groupBy('year')
            ->groupBy('tblSegment.SB_id')
            ->groupBy('tblSegment.branch_id')
            ->orderBy('tblSegment.SB_id')
            ->where('tblOrganization.parent_id', $rmb_id);

        if ($sb_id != -1)
        {
            $record = $record->where('tblSegment.SB_id', $sb_id);
        }
        if ($branch_id != -1)
        {
            $record = $record->where('tblSegment.branch_id', $branch_id);
        }
        $record = $record->get();

        $data = [];
        foreach ($record as $r) 
        {
            $data[] = [
                'route_name' => $r->route_name,
                'RMB' => $r->RMB,
                'SB' => $r->SB,
                'year' => $r->year,
                'elapsed_time' => $r->elapsed_time,
                'section_length' => $r->section_length
            ];  
        }  

        $tpl_file = public_path('excel_templates/DAS/SummaryPT/DAS_SummaryOfPassedTimeFromLatestRepair_'.strtoupper($lang) . '.xlsx');
        include_once public_path("../lib/eiseXLSX/eiseXLSX.php");
        $xlsx = new \eiseXLSX($tpl_file);
     
        $table = $xlsx->arrXMLs['/xl/tables/table1.xml'];
        $table['ref'] = "B11:G". (count($data) + 11);
        $table->autoFilter['ref'] = "B11:G". (count($data) + 11);
        
        

        $xml = $xlsx->arrXMLs['/xl/worksheets/sheet2.xml'];
        $row = $xml->sheetData->row[11];
        $dom = dom_import_simplexml($row);
        $dom->parentNode->removeChild($dom);
        
        $index = 0;
        foreach ($data as $key => $value)
        {
            $new_row = $xml->sheetData->addChild('row');
            $new_row->addChild('c'); 
            foreach ($value as $k => $v)
            {
                $new_cell = $new_row->addChild('c'); 
                if (is_numeric($v))
                {   
                    $this->_writeNumberCell($new_cell, $v);
                }
                else
                {
                    $this->_writeStringCell($new_cell, $v);
                }
            }
        }
        $xlsx->arrXMLs['/xl/worksheets/sheet2.xml'] = $xml;
        
        // setcookie(
        //     'fileDownloadToken',
        //     $request->downloadTokenValue,
        //     time() + 60*60           // expires January 1, 2038
        // );
        $name = ($lang == 'en') ? 'DAS_SummaryOfPassedTimeFromLatestRepair' : 'DAS_Tóm lược về thời gian sửa chữa từ năm sửa chữa mới nhất';
        $xlsx->Output($name. '.xlsx', "D");
    }

    private function _writeStringCell(&$new_cell, $v)
    {
        $new_cell->addAttribute('t', "inlineStr");  
        $new_is = $new_cell->addChild('is');
        if (!mb_check_encoding($v, 'utf-8')) $v = iconv("cp1250", "utf-8", $v); 
        $new_is->addChild('t', htmlspecialchars($v)); 
    }

    private function _writeNumberCell(&$new_cell, $v)
    {
        $new_cell->addAttribute('t', "n");
        $new_v = $new_cell->addChild('v', $v);
    }

}
