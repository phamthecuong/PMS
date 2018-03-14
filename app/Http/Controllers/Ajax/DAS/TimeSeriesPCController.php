<?php

namespace App\Http\Controllers\Ajax\DAS;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\tblSectionPCHistory;
use App\Models\tblOrganization;
use App\Models\tblBranch;
use App,DB;
class TimeSeriesPCController extends Controller
{
    var $distress_type = [
        '1' => 'cracking_ratio_total',
        '2' => 'rutting_depth_max',
        '3' => 'rutting_depth_ave',
        '4' => 'IRI',
        '5' => 'MCI',
    ];

    var $distress_type_export = [
        'cracking_ratio_total' => '1',
        'rutting_depth_max' => '2',
        'rutting_depth_ave' => '2',
        'IRI' => '3',
        'MCI' => '4',
    ];
    var $target_type = [
        '1' => 1,
        '2' => 2,
        '3' => 2,
        '4' => 3,
        '5' => 4,
    ];
    var $unit = [
        '1' => '%',
        '2' => 'mm',
        '3' => 'mm',
        '4' => 'mm/m',
        '5' => '',
    ];
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $rmb_id = $request->rmb_id;
        $first_year = $request->first_year;
        $second_year = $request->second_year;
        //$branch_id = $request->branch_id;
        // $branch_id = tblBranch::orderBy('branch_number')->whereHas('segments.tblOrganization', function($query) use($request, $rmb_id) {
        //     $query->where('parent_id',  $rmb_id);
        // })
        // ->pluck('id')
        // ->toArray();
        $type = $request->type;
        $y_arr = [$first_year,$second_year];
        $data = [];
        $target_type = $this->target_type[$type];
        $distress_type = $this->distress_type[$type];

        $sb_id = tblOrganization::where('parent_id', $rmb_id)->pluck('id')->toArray();
        $condition_rank = DB::table('tblCondition_rank')
            ->select('from','to')
            ->where('target_type', $target_type)
            ->get();
        $label = [];
        $query = '';
        $i = 1;
        $j = count($condition_rank);
        foreach ($condition_rank as $cr)
        {
            $end = $i < $j ? ',' : '';
            if ($cr->from != null && $cr->to != null)
            {
                $query .=  'Sum(CASE WHEN '.$distress_type.' >= '.$cr->from.' AND '.$distress_type.' < '.$cr->to.' THEN section_length ELSE 0 END) as rank'.$i.$end;
            }
            else if ($cr->from == null)
            {
                $query .=  'Sum(CASE WHEN '.$distress_type.' < '.$cr->to.' THEN section_length ELSE 0 END) as rank'.$i.$end;
            }
            else
            {
                $query .=  'Sum(CASE WHEN '.$distress_type.' >= '.$cr->from.' THEN section_length ELSE 0 END) as rank'.$i.$end;
            }
            $i++;
        }
        // DB::enableQueryLog();
        $result = DB::table('tblSection_PC_history')
                           ->whereIn('SB_id', $sb_id)
                           // ->whereIn('branch_id', $branch_id)
                           ->whereIn('date_y', $y_arr);
        $result = $result->join('tblBranch', 'tblBranch.id', '=', 'tblSection_PC_history.branch_id')
            ->select(DB::raw($query.', date_y, Sum(CASE WHEN '.$distress_type.' >= 0 THEN section_length ELSE 0 END) as total_rank, Sum(CASE WHEN '.$distress_type.' >= 0 THEN ('.$distress_type.' * section_length) ELSE 0 END) / 
                Sum(CASE WHEN '.$distress_type.' >= 0 THEN section_length ELSE 0 END) as average'))
            ->groupBy('date_y')
            ->get();
        // dd(DB::getQueryLog());
        foreach ($condition_rank as $c)
        {
            if ($c->from < 1) $c->from = 0;
            if($c->to != null)
            {
                $c->to < 1 ? $label[] = number_format($c->from, 0).$this->unit[$type] : $label[] = number_format($c->from, 0).$this->unit[$type].' - '.number_format($c->to, 0).$this->unit[$type];
            }
            else
            {
                $c->to < 1 ? $label[] = number_format($c->from, 0).$this->unit[$type].' - ' : $label[] = number_format($c->from, 0).$this->unit[$type].' - '.number_format($c->to, 0).$this->unit[$type];
            }
        }
        $data_chart1 = [];
        foreach ($result as $d)
        {
            if ($type == '5')
            {
                $d->average = round($d->average, 1);    
            }
            else
            {
                $d->average = round($d->average, 2);   
            }
            
            $data_chart1[$d->date_y] = $d; //data table
        }

        // load data chart 2
        $b_id = $request->branch_id;
        $result_chart2 = DB::table('tblSection_PC_history')
                           ->whereIn('SB_id', $sb_id)
                           ->whereIn('date_y', $y_arr);
        if (!empty($b_id)) 
        {
            $result_chart2 = $result_chart2->where('branch_id', $b_id);
        }
        else
        {
            // $result_chart2 = $result_chart2->whereIn('branch_id', $branch_id);
        }
        $result_chart2 = $result_chart2->join('tblBranch', 'tblBranch.id', '=', 'tblSection_PC_history.branch_id')
            ->select(DB::raw($query.', date_y, Sum(CASE WHEN '.$distress_type.' >= 0 THEN section_length ELSE 0 END) as total_rank, Sum(CASE WHEN '.$distress_type.' >= 0 THEN ('.$distress_type.' * section_length) ELSE 0 END) / 
                Sum(CASE WHEN '.$distress_type.' >= 0 THEN section_length ELSE 0 END) as average'))
            ->groupBy('date_y')
            ->get();
        $data_chart2 = [];
        foreach ($result_chart2 as $d)
        {
            if ($type == '5')
            {
                $d->average = round($d->average, 1);    
            }
            else
            {
                $d->average = round($d->average, 2);   
            }
            $data_chart2[$d->date_y] = $d; //data table
        }
        return [$data_chart1, $data_chart2, $label];
    }

    public function loadYear(Request $request) 
    {
        $rmb_id = $request->rmb_id;
        $sb_id = tblOrganization::where('parent_id', $rmb_id)->pluck('id')->toArray();
        $PC = tblSectionPCHistory::select('date_y')
                                ->whereIn('sb_id', $sb_id)
                                ->groupBy('date_y')
                                ->orderBy('date_y', 'desc')
                                ->get();
        foreach ($PC as $k => $p)
        {
            $year[] = ['value' => $p->date_y, 'name' => $p->date_y]; 
        }
        return ['year' => $year];
    }

    public function getExportSummaryTimeSeries(Request $request)
    {
        $rmb_id = $request->rmb;
        $first_year = substr($request->first_year, 7);
        $second_year = substr($request->second_year, 7);
        $branch_id = $request->route_name;
        $y_arr = [$second_year, $first_year];
        $lang = App::isLocale('en') ? 'en' : 'vn';
        $data_export = [];
        $rmb_name = tblOrganization::where('id', $rmb_id)->first()->{'name_'.$lang};
        $sb_id = tblOrganization::where('parent_id', $rmb_id)
            ->pluck('id')
            ->toArray();
        foreach ($this->distress_type_export as $distress_type => $target_type)
        {
            $data = [];
            $condition_rank = DB::table('tblCondition_rank')->select('from','to')
                        ->where('target_type', $target_type)->get();
            $label = [];
            $query = '';
            $i = 1;
            $j = count($condition_rank);
            foreach ($condition_rank as $cr)
            {
                $end = $i < $j ? ',' : '';
                if ($cr->from != null && $cr->to != null)
                {
                    $query .=  'Sum(CASE WHEN '.$distress_type.' >= '.$cr->from.' AND '.$distress_type.' < '.$cr->to.' THEN section_length ELSE 0 END) as rank'.$i.$end;
                }
                else if ($cr->from == null)
                {
                    $query .=  'Sum(CASE WHEN '.$distress_type.' < '.$cr->to.' THEN section_length ELSE 0 END) as rank'.$i.$end;
                }
                else
                {
                    $query .=  'Sum(CASE WHEN '.$distress_type.' >= '.$cr->from.' THEN section_length ELSE 0 END) as rank'.$i.$end;
                }
                $i++;
            }

            // foreach ($sb_id as $r)
            // {
                $result = DB::table('tblSection_PC_history')->whereIn('SB_id', $sb_id);
                // if (!empty($branch_id)) 
                // {   
                //     $branch_id = substr($branch_id, 7);
                //     $result = $result->where('branch_id', $branch_id);
                // }
                $result = $result
                ->join('tblBranch', 'tblBranch.id', '=', 'tblSection_PC_history.branch_id')
                ->join('tblOrganization', 'tblOrganization.id','=', 'tblSection_PC_history.SB_id')
                ->whereIn('date_y', $y_arr)
                ->select(
                    DB::raw("'{$rmb_name}' as rmb_name"),
                    'tblOrganization.name_'.$lang.' as sb_name',
                    'tblBranch.name_'.$lang.' as route_name', 
                    'date_y',
                    DB::raw($query.', Sum(CASE WHEN '.$distress_type.' >= 0 THEN section_length ELSE 0 END) as total_rank, 
                    Sum(CASE WHEN '.$distress_type.' >= 0 THEN ('.$distress_type.' * section_length) ELSE 0 END) / Sum(CASE WHEN '.$distress_type.' >= 0 THEN section_length ELSE 0 END) as average')
                )
                ->groupBy('date_y')
                ->groupBy('SB_id')
                ->groupBy('tblBranch.id')
                ->orderBy('SB_id', 'asc')
                ->get();
                // if ($result)
                // {
                    foreach ($result as $d)
                    {
                        $d->average = $distress_type == 'MCI' ? round($d->average, 1) : round($d->average, 2);
                        // $convert = json_decode(json_encode($d), true);
                        // $result = array('rmb_name' => $rmb_name) + $convert;
                        $data[] = $d; //data table
                        //$data['average'] = round($result->average, 2);
                    }   
                // }
            // }
            $data_export[$distress_type] = $data;
           // dd($data_export);
        }
        $tpl_file = public_path('excel_templates/DAS/TimeSeries/DAS_TimesSeriesComparisonOfPavementCondition_'.strtoupper($lang) . '.xlsx');
       
        include_once public_path("../lib/eiseXLSX/eiseXLSX.php");
        $xlsx = new \eiseXLSX($tpl_file);

        // data for crack
        $table = $xlsx->arrXMLs['/xl/tables/table1.xml'];
        $table['ref'] = "C7:O". (count($data_export['cracking_ratio_total']) + 7);
        $table->autoFilter['ref'] = "C7:O". (count($data_export['cracking_ratio_total']) + 7);
        $sheet_name = '001_target_data_1_summary_table';
        $sheet_index = $xlsx->findSheetByName($sheet_name);
        $sheet = $xlsx->selectSheet($sheet_index);

        $index = 0;
       // dd($data_export);
        foreach ($data_export['cracking_ratio_total'] as $key => $value)
        {
            $cell = "C";
            foreach ($value as $sub_k => $sub_v)
            {
//                $sub_v = $sub_k == 'average' ? round($sub_v, 2) : $sub_v;
                if (is_numeric($sub_v))
                {
                    $sheet->data($cell . (8 + $index), $sub_v, 'n');
                }
                else if($sub_v == null)
                {
                    $sheet->data($cell . (8 + $index), 0);
                }
                else
                {
                    $sheet->data($cell . (8 + $index), $sub_v);
                }

                $cell++;
            }
            $index ++;
        }

        // data for IRI
        $table = $xlsx->arrXMLs['/xl/tables/table2.xml'];
        $table['ref'] = "C7:O". (count($data_export['IRI']) + 7);
        $table->autoFilter['ref'] = "C7:O". (count($data_export['IRI']) + 7);
        $sheet_name = '001_target_data_2_summary_table';
        $sheet_index = $xlsx->findSheetByName($sheet_name);
        $sheet = $xlsx->selectSheet($sheet_index);

        $index = 0;
        foreach ($data_export['IRI'] as $key => $value)
        {
            $cell = "C";
            foreach ($value as $sub_k => $sub_v)
            {
//                $sub_v = $sub_k == 'average' ? round($sub_v, 2) : $sub_v;
                if (is_numeric($sub_v))
                {
                    $sheet->data($cell . (8 + $index), $sub_v, "n");
                }
                else
                {
                    $sheet->data($cell . (8 + $index), $sub_v);
                }

                $cell++;
            }
            $index ++;
        }

        // data for RUT(max)
        $table = $xlsx->arrXMLs['/xl/tables/table3.xml'];
        $table['ref'] = "C7:S". (count($data_export['rutting_depth_max']) + 7);
        $table->autoFilter['ref'] = "C7:S". (count($data_export['rutting_depth_max']) + 7);
        $sheet_name = '001_target_data_3_summary_table';
        $sheet_index = $xlsx->findSheetByName($sheet_name);
        $sheet = $xlsx->selectSheet($sheet_index);

        $index = 0;
        foreach ($data_export['rutting_depth_max'] as $key => $value)
        {
            $cell = "C";
            foreach ($value as $sub_k => $sub_v)
            {
//                $sub_v = $sub_k == 'average' ? round($sub_v, 2) : $sub_v;

                if (is_numeric($sub_v))
                {
                    $sheet->data($cell . (8 + $index), $sub_v, "n");
                }
                else
                {
                    $sheet->data($cell . (8 + $index), $sub_v);
                }

                $cell++;
            }
            $index ++;
        }


        // data for RUT(AVG)
        $table = $xlsx->arrXMLs['/xl/tables/table4.xml'];
        $table['ref'] = "C7:S". (count($data_export['rutting_depth_ave']) + 7);
        $table->autoFilter['ref'] = "C7:S". (count($data_export['rutting_depth_ave']) + 7);
        $sheet_name = '001_target_data_4_summary_table';
        $sheet_index = $xlsx->findSheetByName($sheet_name);
        $sheet = $xlsx->selectSheet($sheet_index);

        $index = 0;
        foreach ($data_export['rutting_depth_ave'] as $key => $value)
        {
            $cell = "C";
            foreach ($value as $sub_k => $sub_v)
            {
//                $sub_v = $sub_k == 'average' ? round($sub_v, 2) : $sub_v;

                if (is_numeric($sub_v))
                {
                    $sheet->data($cell . (8 + $index), $sub_v, "n");
                }
                else
                {
                    $sheet->data($cell . (8 + $index), $sub_v);
                }

                $cell++;
            }
            $index ++;
        }

        // data for MCI
        $table = $xlsx->arrXMLs['/xl/tables/table5.xml'];
        $table['ref'] = "C7:O". (count($data_export['MCI']) + 7);
        $table->autoFilter['ref'] = "C7:O". (count($data_export['MCI']) + 7);
        $sheet_name = '001_target_data_5_summary_table';
        $sheet_index = $xlsx->findSheetByName($sheet_name);
        $sheet = $xlsx->selectSheet($sheet_index);

        $index = 0;
        foreach ($data_export['MCI'] as $key => $value)
        {
            $cell = "C";
            foreach ($value as $sub_k => $sub_v)
            {
//                $sub_v = $sub_k == 'average' ? round($sub_v, 1) : $sub_v;
                if (is_numeric($sub_v))
                {
                    $sheet->data($cell . (8 + $index), $sub_v, "n");
                }
                else
                {
                    $sheet->data($cell . (8 + $index), $sub_v);
                }

                $cell++;
            }
            $index ++;
        }
        
        setcookie(
            'downloadTokenValueTimeSeries',
            $request->downloadTokenValueTimeSeries,
            time() + 60*60 ,
            '/das'     
        );
        $name = ($lang == 'en') ? 'DAS_TimesSeriesComparisonOfPavementCondition' : 'So sánh Time_series về tình trạng mặt đường';
        $xlsx->Output($name. '.xlsx', "D");

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
}
