<?php

namespace App\Http\Controllers\Ajax\DAS;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\tblSegment;
use App\Models\tblSectiondataMH;
use App\Models\tblOrganization;
use App\Models\mstRepairMethod;
use App\Models\tblRCategory;
use DB, App;
class SummaryMRController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {   
        $data = $this->_conditionMH($request, true, false, true, false, 1, true);
        return $data;
    }

    public function repairWork(Request $request)
    {   
        $data_segment = [];
        $data_branch = [];
        $year = $request->year;
        $min_year = date('Y') - $request->year + 1;
        //
        $survey_year = $this->_getYearSeries($year);
        $ri_length = $this->__getRILength($request);
        $data_length_ri = [];
        // data length ri branch_ic
        foreach ($ri_length as $r)
        {
            $data_length_ri[$r->branch_id] = $r->total_length;
        }
        
        $data_branch = $this->_conditionMH($request, false, true, true, true, 2, true);
        
        $left_data = [];
        $right_data = [];
        $labels = [];

        foreach ($survey_year as $year) 
        {
            $left_data[$year] = [];
        }
        foreach ($data_branch as $branch_id => $d) 
        {
            $labels[] = $d[0]['route_name'];

            $ri_length = intval(@$data_length_ri[$branch_id]);
            $total_mh = array_sum(array_column($d, 'sum_length'));
            $right_data[] = ($ri_length == 0) ? 0 : round(100 * $total_mh / $ri_length, 2);
            foreach ($survey_year as $year) 
            {
                $found = 0;
                foreach ($d as $mh_info) 
                {
                    if ($mh_info['survey_time'] == $year)
                    {
                        $found = $mh_info['sum_length'];
                        break;
                    }
                }
                $left_data[$year][] = 0.001 * $found;
            }
        }

        // if (count($data_branch) > 0)
        // {
        //     foreach ($data_branch as $b => $r)
        //     {
        //         $survey_time = [];
        //         foreach ($r as $v)
        //         {
        //             $survey_time[] = $v['survey_time'];

        //         }
        //         for ($i = $min_year; $i <= date('Y'); $i++)
        //         {   
        //             if (in_array($i, $survey_time))
        //             {
        //                 $k = array_search($i, $survey_time); // get key of year in array
        //                 $data[$b][] = $data_branch[$b][$k];
        //             }
        //             else
        //             {
        //                 $data[$b][] = [
        //                     'sum_length' => 0, 
        //                     'survey_time' => $i,
        //                     'route_name' => $data_branch[$b][0]['route_name']
        //                 ];
        //             }
        //         }
        //     }
        // }

        // $left_data = [];
        // $right_data = [];
        // $total = [];
        // $labels = [];
        // $count = 0;

        // if (count($data) > 0)
        // {
        //     for($count; $count < $year; $count ++)
        //     {   
        //         $label = [];
        //         foreach ($data as $k => $r)
        //         {
        //             $label[] = $r[0]['route_name']; // get labels
        //             $v = $r[$count];
        //             $left_data[$v['survey_time']][] = round($v['sum_length']/1000, 2); // get left data
        //         }
        //         $labels = $label;
        //     }
        //     //dd($left_data);
        //     foreach ($data as $k => $r)
        //     {
        //         $total[$k] = 0;
        //         foreach ($r as $value) {
        //             $total[$k] += round($value['sum_length']/1000, 2);
        //         }
        //         if (!isset($data_length_ri[$k]))
        //         {
        //             $right_data[] = 0;
        //         }
        //         else
        //         {
        //             $length_ri = round($data_length_ri[$k]/1000, 2);
        //             $right_data[] = round(($total[$k] / $length_ri )*100, 2); // get right data
        //         }   
        //     }
        // }
        return ['left_data' => $left_data, 'right_data' => $right_data, 'labels' => $labels];

    }

    private function _getYearSeries($year)
    {
        $max_s = date('Y');
        $min_s = date('Y') - $year + 1;
        $sum = 0;
        for ($i = $min_s; $i <= $max_s; $i ++)
        {
            $survey_data[] = $i;
        }
        return $survey_data;
    }

    private function _convertToSummary($data, $survey_year)
    {
        $result = $data;

        $total_result = [];
        $total = 0;
        foreach ($data as $k => $d) 
        {
            foreach ($survey_year as $y => $year) 
            {
                if (!isset($d['data'][$year]))
                {
                    $result[$k]['data'][$year] = [
                        'sum_length' => 0, 
                        'survey_time' => $year
                    ];
                }
                if (!isset($total_result[$year]))
                {
                    $total_result[$year] = [
                        'sum_length' => 0, 
                        'survey_time' => $year
                    ];
                }
                $total_result[$year]['sum_length']+= $result[$k]['data'][$year]['sum_length'];
            }
            $total+= $d['total'];
        }
        if (count($data) > 0)
        {
            $text = App::islocale('en') ? 'Total' : 'Tổng';
            $result[$text] = [
                'data' => $total_result,
                'total' => $total
            ];
        }
        return $result;
    }

    public function getDataTable(Request $request)
    {
        $rmb = tblOrganization::where('id', $request->rmb_id)->first()->organization_name;
        $data_table = [];
        $data_segment = [];
        $data = [];
        $year = $request->year;
        // start process
        $survey_year = $this->_getYearSeries($year);
        $data = $this->_conditionMH($request, false, true, true, true, 3);
        return [$this->_convertToSummary($data, $survey_year), $survey_year, $rmb];
    }

    private function _conditionMH($request, $jurisdiction_flg = false, $year_flg = false, $group_by_year_flg =false, $group_by_branch_flg = false, $transform_type)
    {
        $segment_ids = $this->_getSegmentList($request, $jurisdiction_flg);
        $name = App::isLocale('en') ? 'name_en' : 'name_vn';
        $rmb_name = tblOrganization::find($request->rmb_id)->{$name};
        $data_MH = [];
        // \DB::enableQueryLog();
        $records = \DB::table('tblSectiondata_MH')
            ->select(
                DB::raw('SUM(tblSectiondata_MH.actual_length) as sum_length'), 
                DB::raw('YEAR(survey_time) as year_st'), 
                DB::raw("tblBranch.{$name} as branch_name"),
                DB::raw("tblOrganization.{$name} as sb_name"),
                DB::raw("'{$rmb_name}' as rmb_name"),
                'tblSegment.branch_id',
                'tblBranch.branch_number'
            )
            ->join('tblSegment', 'tblSegment.id', '=', 'tblSectiondata_MH.segment_id')
            ->join('tblBranch', 'tblBranch.id', '=', 'tblSegment.branch_id')
            ->join('tblOrganization', 'tblOrganization.id', '=', 'tblSegment.SB_id')
            ->whereIn('tblSegment.id', $segment_ids);

        if ($request->repair_method != -1)
        {
            $records = $records->where('repair_method_id', $request->repair_method);
        }
        if ($request->repair_category != -1)
        {
            $records = $records->where('r_category_id', $request->repair_category);
        }
        if ($year_flg)
        {
            $min_year = date('Y') - $request->year + 1;
            $records = $records->whereRaw('YEAR(survey_time) <= YEAR(CURRENT_TIMESTAMP)')
                ->whereRaw("YEAR(survey_time) >= {$min_year}");
        }

        if ($group_by_year_flg)
        {
            $records = $records->groupBy('year_st');
        }
        if ($group_by_branch_flg)
        {
            $records = $records->groupBy('tblBranch.id');
        }
        $records = $records->get();
        // dd(\DB::getQueryLog());
        return $this->_transformMH($records, $transform_type);
    }

    private function _transformMH($records, $type)
    {
        $data = [];
        foreach ($records as $r)
        {
            switch ($type) 
            {
                case 1:
                    $data[$r->year_st] = [
                        'survey_time' => $r->year_st, 
                        'total_length' => $r->sum_length
                    ];
                    break;
                case 2:
                    $data[$r->branch_id][] = [
                        'sum_length' => $r->sum_length, 
                        'survey_time' => $r->year_st,
                        'route_name' => $r->branch_name
                    ];
                    break;
                case 3:
                    if (!isset($data[$r->branch_id]))
                    {
                        $data[$r->branch_id] = [
                            'data' => [],
                            'total' => 0,
                            'branch_name' => $r->branch_name,
                            'sb_name' => $r->sb_name,
                            'rmb_name' => $r->rmb_name
                        ];
                    }
                    $data[$r->branch_id]['data'][$r->year_st] = [
                        'sum_length' => $r->sum_length, 
                        'survey_time' => $r->year_st
                    ];
                    $data[$r->branch_id]['total']+= $r->sum_length;
                    break;
                default:
                    # code...
                    break;
            }
        }
        return $data;
    }

    private function _addRepairRate($data, $request)
    {
        $ri_length = $this->__getRILength($request);
        $data_length_ri = [];
        foreach ($ri_length as $r)
        {
            $data_length_ri[$r->branch_id] = $r->total_length;
        }
        $result = $data;
        foreach ($data as $branch_id => $d)
        {
            $ri_length = intval(@$data_length_ri[$branch_id]);
            $total_mh = $d['total'];
            $repair_rate = ($ri_length == 0) ? 0 : round(100 * $total_mh / $ri_length, 2);
            $result[$branch_id]['repair_rate'] = $repair_rate;
        }
        return $result;
    }

    public function setDataBySheetData($data, $xlsx1)
    {
        $sheet_index1 = $xlsx1->findSheetByName('Summary_Table');
        $sheet1 = $xlsx1->selectSheet($sheet_index1);
//        $sheet1->data('F5', 2015, "n");
        $cell = "F";
        ksort($data);
        foreach ($data as $key => $value)
        {
            if (is_numeric($key))
            {
                $sheet1->data($cell.'6', $key, "n");
            }
            else
            {
                $sheet1->data($cell.'6', $key);
            }
            $cell++;
        }
//        dd($sheet1->arrXMLs['/xl/worksheets/sheet2.xml']->sheetData);

    }

    public function exportSummaryMR(Request $request)
    {
        $data_list_MH = []; // data sheet 3
        $rmb_id = $request->rmb_id;
        $year = $request->year;
        $name = App::isLocale('en') ? 'name_en' : 'name_vn';
        $survey_year = $this->_getYearSeries($year);
        $data = $this->_conditionMH($request, false, true, true, true, 3);
        $data_table = $this->_addRepairRate($this->_convertToSummary($data, $survey_year), $request);
        $rmb_name = tblOrganization::where('id', $rmb_id)->first()->$name;
        $sectiondataMH = tblSectiondataMH::with('repairMethod', 'repairCategory','segment', 'segment.tblBranch','segment.tblOrganization')
            ->selectRaw('YEAR(survey_time) as year, actual_length, segment_id, repair_method_id, r_category_id')
            ->whereHas('segment.tblOrganization', function($q) use($rmb_id) {
                $q->where('parent_id', $rmb_id);
            })
            ->get();

        foreach ($sectiondataMH as $r)
        {
            $data_list_MH[] = [
                'rmb' => $rmb_name,
                'sb' => $r->segment->tblOrganization->$name,
                'road_name' => $r->segment->tblBranch->$name,
                'branch_no' => $r->segment->tblBranch->branch_number,
                'actual_length' => $r->actual_length,
                'repair_method' => ($r->repairMethod) ? $r->repairMethod->$name: '',
                'repair_category' => ($r->repairCategory) ? $r->repairCategory->$name: '',
                'year' => $r->year, 
            ];
        }
        
        $lang = App::isLocale('en')? 'en' : 'vn';
        if ($year == 5)
        {
            $tpl_file = public_path('excel_templates/DAS/SummaryMR/DAS_SummaryOfMaintenanceRecord_5_'.strtoupper($lang).'.xlsx');
        }
        else
        {
            $tpl_file = public_path('excel_templates/DAS/SummaryMR/DAS_SummaryOfMaintenanceRecord_10_'.strtoupper($lang).'.xlsx');
        }
        include_once public_path("../lib/eiseXLSX/eiseXLSX.php");
        $xlsx = new \eiseXLSX($tpl_file);
        if(!empty($data_table))
        {
            $year_excel = array_slice($data_table, 1, 1)[0]['data'];
            $this->setDataBySheetData($year_excel, $xlsx);
        }
        $xml = $xlsx->arrXMLs['/xl/worksheets/sheet3.xml'];
        $row = $xml->sheetData->row[2];
        $dom = dom_import_simplexml($row);
        $dom->parentNode->removeChild($dom);
        //
        $table = $xlsx->arrXMLs['/xl/tables/table2.xml'];
        $table['ref'] = "A2:H".(count($data_list_MH) + 2);
        $table->autoFilter['ref'] = "A2:H".(count($data_list_MH) + 2);
        foreach ($data_list_MH as $key => $value)
        {
            $new_row = $xml->sheetData->addChild('row');
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
        $xlsx->arrXMLs['/xl/worksheets/sheet3.xml'] = $xml;

        $xml = $xlsx->arrXMLs['/xl/worksheets/sheet2.xml'];
        $row = $xml->sheetData->row[6];
        $dom = dom_import_simplexml($row);
        $dom->parentNode->removeChild($dom);
        $table = $xlsx->arrXMLs['/xl/tables/table1.xml'];
        if ($year == 5)
        {
            $table['ref'] = "C6:L".(count($data_table) + 5);
            $table->autoFilter['ref'] = "C6:L".(count($data_table) + 5);
        }
        else
        {
            $table['ref'] = "C6:Q".(count($data_table) + 5);
            $table->autoFilter['ref'] = "C6:Q".(count($data_table) + 5);
        }
        foreach ($data_table as $value)
        {
            if (!isset($value['rmb_name'])) continue;
            $new_row = $xml->sheetData->addChild('row');
            $new_row->addChild('c'); 
            $new_row->addChild('c');
            $new_cell = $new_row->addChild('c');
            $this->_writeStringCell($new_cell, $value['rmb_name']);
            $new_cell = $new_row->addChild('c');
            $this->_writeStringCell($new_cell, $value['sb_name']);
            $new_cell = $new_row->addChild('c');
            $this->_writeStringCell($new_cell, $value['branch_name']);

            $year_data = $value['data'];
            ksort($year_data);
            foreach ($year_data as $mh_by_year)
            {
                $new_cell = $new_row->addChild('c');
                $this->_writeNumberCell($new_cell, $mh_by_year['sum_length']);
            }
            $new_cell = $new_row->addChild('c');
            $this->_writeNumberCell($new_cell, $value['repair_rate']);
            $new_cell = $new_row->addChild('c');
            $this->_writeNumberCell($new_cell, $value['total']);
        }
        $xlsx->arrXMLs['/xl/worksheets/sheet2.xml'] = $xml;

        $name = ($lang == 'en') ? 'DAS_SummaryOfMaintenanceRecord' : 'Tóm lược về lịch sử bảo trì';
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

    /**
     * get segment ID list that fits the condition
     */
    private function _getSegmentList($request, $jurisdiction_flg = false)
    {
        $branch_id = $request->branch_id;
        $sb_id = $request->sb_id;
        $rmb_id = $request->rmb_id;

        $segment = tblSegment::select('id', 'branch_id');

        if (($sb_id == -1  && $branch_id == -1) || !$jurisdiction_flg) 
        {
            $sb_id = tblOrganization::where('parent_id', $rmb_id)->pluck('id')->toArray();
            $segment = $segment->whereIn('sb_id', $sb_id);
        }
        else if ($jurisdiction_flg)
        {
            if ($sb_id != -1 && $branch_id == -1)
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
                $segment = $segment->where('branch_id', $branch_id)->where('SB_id', $sb_id);
            }
        }
        $segment_ids = $segment->get()->pluck('id')->toArray();
        if (count($segment_ids) == 0)
        {
            $segment_ids = [-1];
        }
        return $segment_ids;
    }
   
    private function __getRILength($request)
    {
        $rmb_id = $request->rmb_id;
        $segment_ids = tblSegment::whereHas('tblOrganization', function($q) use($rmb_id) {
            $q->where('parent_id', $rmb_id);
        })->get()->pluck('id')->toArray();
        if (count($segment_ids) > 0)
        {
            $segment_ids = implode(',', $segment_ids);
        }
        else
        {
            $segment_ids = -1;
        }

        $sql = "SELECT (COUNT(id) * 100) AS total_length, branch_id
                FROM (
                    SELECT `tblPMS_RI_info`.id, `tblSegment`.branch_id
                    FROM `tblPMS_RI_info`
                    JOIN `tblSegment` ON `tblPMS_RI_info`.`segment_id` = `tblSegment`.id
                    WHERE
                        `tblSegment`.id IN ({$segment_ids})
                        AND PMS_info_id IN (
                            SELECT id FROM `tblPMS_sectioning_info` i
                            WHERE type_id = 1 AND id = (
                                SELECT id FROM tblPMS_sectioning_info WHERE PMS_section_id = i.`PMS_section_id` AND type_id = 1 AND condition_year <= YEAR(CURRENT_TIMESTAMP) ORDER BY condition_year DESC LIMIT 1
                            )
                        )
                ) a GROUP BY branch_id";

        $rsl = DB::select(DB::raw($sql));
        return $rsl;
    }

    function _getNameFromNumber($num)
    {
        $numeric = $num % 26;
        $letter = chr(65 + $numeric);
        $num2 = intval($num / 26);
        if ($num2 > 0)
        {
            return $this->_getNameFromNumber($num2 - 1) . $letter;
        }
        else
        {
            return $letter;
        }
    }


    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {

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
}
