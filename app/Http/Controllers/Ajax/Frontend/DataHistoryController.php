<?php

namespace App\Http\Controllers\Ajax\Frontend;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\tblSectiondataRMD;
use App\Models\tblSectiondataTV;
use App\Models\tblSectiondataMH;
use App\Models\tblSegment;
use App\Models\tblRMDHistory;
use App\Models\tblMHHistory;
use App\Models\tblTVHistory;
use App\Models\tblPMSSectioning;
use App\Models\tblPMSSectioningInfo;
use App\Models\tblPMSPCInfo;
use App\Models\tblBranch;
use DB,Auth;
class DataHistoryController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function getData_TV(Request $request)
    {
        $rmb_id = $request->rmb_id;
        $sb_id = $request->sb_id;
        $branch_id = $request->branch_id;
        $segment_id = $request->segment_id;
        $from = $request->from;
        $to = $request->to;
        $st_from = substr($from, 0, -3);
        $st_to = number_format(substr($from, -3));
        $end_from = substr($to, 0, -3);
        $end_to = number_format(substr($to, -3));
        $data = [];
        $record = tblSegment::where('branch_id',$branch_id)->pluck('id');
        $tv = tblSectiondataTV::whereIn('segment_id',$record)
                        ->whereRaw('10000 * km_station + m_station >= ? AND 10000 * km_station + m_station <= ?', [10000 * $st_from + $st_to, 10000 * $end_from + $end_to])
                        ->orderBy('km_station','ASC')->get();
        $tvFrom = tblSectiondataTV::whereIn('segment_id',$record)
                        ->orderBy('km_station', 'DESC')
                        ->whereRaw('10000 * km_station + m_station <= ?', [10000 * $st_from + $st_to])
                        ->first();
        $tvTo = tblSectiondataTV::whereIn('segment_id',$record)
                        ->orderBy('km_station', 'ASC')
                        ->whereRaw('10000 * km_station + m_station >= ?', [10000 * $end_from + $end_to])
                        ->first();
        $set = count($tv);
        $data['set'] = [
            'min' => intval($from),
            'max' => intval($to) 
        ];
        if ($set!=0) 
        {
            $tottal_to_left_first = $tv[0]['total_traffic_volume_up'] + $tv[0]['heavy_traffic_up'];
            $tottal_to_left_last = $tv[$set-1]['total_traffic_volume_up'] + $tv[$set-1]['heavy_traffic_up'];
            //right
            $tottal_to_right_first = $tv[0]['total_traffic_volume_down'] + $tv[0]['heavy_traffic_down'];
            $tottal_to_right_last = $tv[$set-1]['total_traffic_volume_down'] + $tv[$set-1]['heavy_traffic_down'];
            foreach ($tv as $t) 
            {
                $survey_time = explode('-', $t->survey_time);
                $year = $survey_time[0];
                $station = ($t->km_station)*1000 + $t->m_station;
                $total_left = $t->total_traffic_volume_up + $t->heavy_traffic_up;
                $heavy_traffic_up = $t->heavy_traffic_up;
                $total_right = $t->total_traffic_volume_down + $t->heavy_traffic_down;
                $heavy_traffic_down = $t->heavy_traffic_down;
                $data['left'][] = [
                    'station' => $station,
                    'total' => $total_left,
                    'heavy' => $heavy_traffic_up
                ];
                $data['right'][] = [
                    'station' => $station,
                    'total' => $total_right,
                    'heavy' => $heavy_traffic_down
                ];
            }
            if (count($tvFrom) > 0) {
                $c = intval($from - (1000*$tvFrom->km_station+$tvFrom->m_station) );
                $d = intval( (1000*$tv[0]->km_station+$tv[0]->m_station) - $from);
                $a_left = $tvFrom['total_traffic_volume_up'];
                $b_left = $tv[0]['total_traffic_volume_up'];
                if ($a_left > $b_left) {
                    $e_left = ($b_left*$c + $b_left*$d)/(abs($a_left-$b_left));
                    $total_traffic_volume_left = ($e_left*$b_left + $b_left*$d)/$e_left;
                }
                else {
                    $x_left = ($a_left*$c + $a_left*$d)/(abs($b_left-$a_left));
                    $total_traffic_volume_left = ($a_left*$x_left + $a_left*$c)/$x_left;
                }
                //right
                $a_right = $tvFrom['total_traffic_volume_down'];
                $b_right = $tv[0]['total_traffic_volume_down'];
                if ($a_right > $b_right) {
                    $e_right = ($b_right*$c + $b_right*$d)/(abs($a_right-$b_right));
                    $total_traffic_volume_right = ($e_right*$b_right + $b_right*$d)/$e_right;
                }
                else {
                    $x_right = ($a_right*$c + $a_right*$d)/(abs($b_right-$a_right));
                    $total_traffic_volume_right = ($a_right*$x_right + $a_right*$c)/$x_right;
                }
                
                array_unshift($data['left'], [
                    'station'  => $from,
                    'total' => $total_traffic_volume_left,
                    'heavy' => 0
                ]);
                array_unshift($data['right'], [
                    'station'  => $from,
                    'total' => $total_traffic_volume_right,
                    'heavy' => 0
                ]);
            }
            else
            {
                array_unshift($data['left'], [
                    'station'  => $from,
                    'total' => $tottal_to_left_first,
                    'heavy' => $tv[0]['heavy_traffic_up']
                ]);
                array_unshift($data['right'], [
                    'station'  => $from,
                    'total' => $tottal_to_right_first,
                    'heavy' => $tv[0]['heavy_traffic_up']
                ]);
            }
            if (count($tvTo) > 0) {
                $c = intval($to - (1000*$tv[0]->km_station+$tv[0]->m_station) );
                $d = intval( (1000*$tvTo->km_station+$tvTo->m_station) - $to);
                $a_left = $tv[0]['total_traffic_volume_up'];
                $b_left = $tvTo['total_traffic_volume_up'];
                //dd('a:'.$a_left.'----- b:'.$b_left);
                if ($a_left > $b_left) {
                    $e_left = ($b_left*$c + $b_left*$d)/($a_left-$b_left);
                    $total_traffic_volume_left = ($e_left*$b_left + $b_left*$d)/$e_left;
                }
                else {
                    $x_left = ($a_left*$c + $a_left*$d)/($b_left-$a_left);
                    $total_traffic_volume_left = ($a_left*$x_left + $a_left*$c)/$x_left;
                }
                //
                $a_right = $tv[0]['total_traffic_volume_down'];
                $b_right = $tvTo['total_traffic_volume_down'];
                if ($a_right > $b_right) {
                    $e_right = ($b_right*$c + $b_right*$d)/(abs($a_right-$b_right));
                    $total_traffic_volume_right = ($e_right*$b_right + $b_right*$d)/$e_right;
                }
                else {
                    $x_right = ($a_right*$c + $a_right*$d)/(abs($b_right-$a_right));
                    $total_traffic_volume_right = ($a_right*$x_right + $a_right*$c)/$x_right;
                }

                array_push($data['left'], [
                    'station'  => $to,
                    'total' => $total_traffic_volume_left,
                    'heavy' => 0
                ]);
                array_push($data['right'], [
                    'station'  => $to,
                    'total' => $total_traffic_volume_right,
                    'heavy' => 0
                ]);
            }
            else
            {
                if ($set > 1) {
                    array_push($data['left'], [
                    'station'  => $to,
                    'total' => $tottal_to_left_last,
                    'heavy' => $tv[$set-1]['heavy_traffic_up']
                    ]);
                    array_push($data['right'], [
                        'station'  => $to,
                        'total' => $tottal_to_right_last,
                        'heavy' => $tv[$set-1]['heavy_traffic_up']
                    ]);
                }
                else
                {
                    array_push($data['left'], [
                        'station'  => $to,
                        'total' => $tottal_to_left_last,
                        'heavy' => $tv[0]['heavy_traffic_up']
                    ]);
                    array_push($data['right'], [
                        'station'  => $to,
                        'total' => $tottal_to_right_last,
                        'heavy' => $tv[0]['heavy_traffic_up']
                    ]);
                }
            }
        }
        else
        {
            $data['left'] = [];
            $data['right'] = [];
            if ($tvFrom && $tvTo) 
            {
                $c = intval($from - (1000*$tvFrom->km_station+$tvFrom->m_station) );
                $d = intval( (1000*$tvTo->km_station+$tvTo->m_station) - $from);
                $a_left = $tvFrom['total_traffic_volume_up'];
                $b_left = $tvTo['total_traffic_volume_up'];
                if ($a_left > $b_left) {
                    $e_left = ($b_left*$c + $b_left*$d)/($a_left-$b_left);
                    $total_traffic_volume_left = ($e_left*$b_left + $b_left*$d)/$e_left;
                }
                else {
                    $x_left = ($a_left*$c + $a_left*$d)/($b_left-$a_left);
                    $total_traffic_volume_left = ($a_left*$x_left + $a_left*$c)/$x_left;
                }
                //
                $a_right = $tvFrom['total_traffic_volume_down'];
                $b_right = $tvTo['total_traffic_volume_down'];
                if ($a_right > $b_right) {
                    $e_right = ($b_right*$c + $b_right*$d)/(abs($a_right-$b_right));
                    $total_traffic_volume_right = ($e_right*$b_right + $b_right*$d)/$e_right;
                }
                else {
                    $x_right = ($a_right*$c + $a_right*$d)/(abs($b_right-$a_right));
                    $total_traffic_volume_right = ($a_right*$x_right + $a_right*$c)/$x_right;
                }
                
                array_unshift($data['left'], [
                    'station'  => $from,
                    'total' => $total_traffic_volume_left,
                    'heavy' => 0
                ]);
                array_unshift($data['right'], [
                    'station'  => $from,
                    'total' => $total_traffic_volume_right,
                    'heavy' => 0
                ]);
                // to
                $c = intval($to - (1000*$tvFrom->km_station+$tvFrom->m_station) );
                $d = intval( (1000*$tvTo->km_station+$tvTo->m_station) - $to);
                $a_left = $tvFrom['total_traffic_volume_up'];
                $b_left = $tvTo['total_traffic_volume_up'];
                if ($a_left > $b_left) {
                    $e_left = ($b_left*$c + $b_left*$d)/($a_left-$b_left);
                    $total_traffic_volume_left = ($e_left*$b_left + $b_left*$d)/$e_left;
                }
                else {
                    $x_left = ($a_left*$c + $a_left*$d)/($b_left-$a_left);
                    $total_traffic_volume_left = ($a_left*$x_left + $a_left*$c)/$x_left;
                }
                 //
                $a_right = $tvFrom['total_traffic_volume_down'];
                $b_right = $tvTo['total_traffic_volume_down'];
                if ($a_right > $b_right) {
                    $e_right = ($b_right*$c + $b_right*$d)/(abs($a_right-$b_right));
                    $total_traffic_volume_right = ($e_right*$b_right + $b_right*$d)/$e_right;
                }
                else {
                    $x_right = ($a_right*$c + $a_right*$d)/(abs($b_right-$a_right));
                    $total_traffic_volume_right = ($a_right*$x_right + $a_right*$c)/$x_right;
                }

                array_push($data['left'], [
                    'station'  => $to,
                    'total' => $total_traffic_volume_left,
                    'heavy' => 0
                ]);
                array_push($data['right'], [
                    'station'  => $to,
                    'total' => $total_traffic_volume_right,
                    'heavy' => 0
                ]); 
            }
            else if($tvFrom && !$tvTo)
            {
                //From
                $total_left_from = $tvFrom['total_traffic_volume_up'] + $tvFrom['heavy_traffic_up'];
                $total_right_from = $tvFrom['total_traffic_volume_down'] + $tvFrom['heavy_traffic_down'];
                $heavy_up_from = $tvFrom['heavy_traffic_up'];
                $heavy_down_from = $tvFrom['heavy_traffic_down'];
                array_unshift($data['left'], [
                    'station'  => $from,
                    'total' => $total_left_from,
                    'heavy' => $heavy_up_from
                ]);
                array_unshift($data['right'], [
                    'station'  => $from,
                    'total' => $total_right_from,
                    'heavy' => $heavy_down_from
                ]);
                // to
                array_push($data['left'], [
                    'station'  => $to,
                    'total' => $total_left_from,
                    'heavy' => $heavy_up_from
                ]);
                array_push($data['right'], [
                    'station'  => $to,
                    'total' => $total_right_from,
                    'heavy' => $heavy_down_from
                ]);
            }
            else if(!$tvFrom && $tvTo)
            {
                // to
                $total_left_to = $tvTo['total_traffic_volume_up'] + $tvTo['heavy_traffic_up'];
                $total_right_to = $tvTo['total_traffic_volume_down'] + $tvTo['heavy_traffic_down'];
                $heavy_up_to = $tvTo['heavy_traffic_up'];
                $heavy_down_to = $tvTo['heavy_traffic_down'];
                array_push($data['left'], [
                    'station'  => $to,
                    'total' => $total_left_to,
                    'heavy' => $heavy_up_to
                ]);
                array_push($data['right'], [
                    'station'  => $to,
                    'total' => $total_right_to,
                    'heavy' => $heavy_down_to
                ]); 

                // From
                array_unshift($data['left'], [
                    'station'  => $from,
                    'total' => $total_left_to,
                    'heavy' => $heavy_up_to
                ]);
                array_unshift($data['right'], [
                    'station'  => $from,
                    'total' => $total_right_to,
                    'heavy' => $heavy_down_to
                ]);
            }
        }
        return $data;
    }
    public function getDataHistoryYear(Request $request)
    {
        $segment_id = $request->segment_id;
        $direction = $request->direction;
        $year = $request->year;
        $from = $request->from;
        $to = $request->to;
        $lane_pos_number = $request->lane_pos_number;
        $data = [];
        $lane_no = 0;
        $MHHistory = tblSectiondataMH::where('segment_id', $segment_id)
                                    ->whereRaw("YEAR(survey_time) = '" . $year . "'")
                                    ->orderBy('survey_time', 'DESC')
                                    ->get();
        $tblSectiondataMH = tblSectiondataMH::with('repairCategory')->where('segment_id', $segment_id)->orderBy('survey_time')->get();
        if (!empty($tblSectiondataMH)) {
            foreach ($tblSectiondataMH as $lane) {
                if ($lane->lane_pos_number > $lane_no)
                {
                    $lane_no = $lane->lane_pos_number;
                }
            }
        }
        if (!empty($MHHistory))
        {
            
            foreach ($MHHistory as $r) 
            {   
                $start = ($r->km_from)*1000 + $r->m_from;
                $end = ($r->km_to)*1000 + $r->m_to;
                $survey_time = explode('-', $r->survey_time);
                !empty($r->repairCategory->name) ? $r_category = $r->repairCategory->name : $r_category = '';
                
                if ($start <= $from && $to <= $end)
                {   
                    $data['MH'][] = [
                                'id' => $r->id,
                                'sectiondata_id' => $r->id,
                                'direction' => $r->direction,
                                'lane_pos_number' => $r->lane_pos_number,
                                'from' => $from,
                                'to' => $to,
                                'pavement_type' => $r->pavement_type_id,
                                'r_category' => $r_category,
                                'completion_date' => $r->completion_date,
                                'repair_duration' => $r->repair_duration
                            ];   
                }
                else if ($from <= $end && $end <= $to && $from >= $start)
                {
                    $data['MH'][] = [
                                'id' => $r->id,
                                'sectiondata_id' => $r->id,
                                'direction' => $r->direction,
                                'lane_pos_number' => $r->lane_pos_number,
                                'from' => $from,
                                'to' => $end,
                                'pavement_type' => $r->pavement_type_id,
                                'r_category' => $r_category,
                                'completion_date' => $r->completion_date,
                                'repair_duration' => $r->repair_duration
                            ];  
                }
                else if ($from <= $start && $start <= $to && $to <= $end )
                {
                    $data['MH'][] = [
                                'id' => $r->id,
                                'sectiondata_id' => $r->id,
                                'direction' => $r->direction,
                                'lane_pos_number' => $r->lane_pos_number,
                                'from' => $start,
                                'to' => $to,
                                'pavement_type' => $r->pavement_type_id,
                                'r_category' => $r_category,
                                'completion_date' => $r->completion_date,
                                'repair_duration' => $r->repair_duration
                            ]; 
                }
                else if ($from <= $start && $end <= $to )
                {
                    $data['MH'][] = [
                                'id' => $r->id,
                                'sectiondata_id' => $r->id,
                                'direction' => $r->direction,
                                'lane_pos_number' => $r->lane_pos_number,
                                'from' => $start,
                                'to' => $end,
                                'pavement_type' => $r->pavement_type_id,
                                'r_category' => $r_category,
                                'completion_date' => $r->completion_date,
                                'repair_duration' => $r->repair_duration
                            ];  
                }
                else
                {
                    $data['MH'][] = [];
                }
            }
        }
        if ($lane_no >= 1) 
        {
            $segment_info = 2 * $lane_no;
        }
        else
        {
            $segment_info = 1;
        }
        return array(
            'data' => $data,
            'lane_no' => $segment_info
        );
    }
    public function getDataPmosHistory(Request $request)
    {
        $segment_id = $request->segment_id;
        $direction = $request->direction;
        $from = $request->from;
        $to = $request->to;
        $lane_pos_number = $request->lane_pos_number;
        $data = [];
        $lane_no = 0;
        $MHHistory = tblSectiondataMH::where('segment_id', $segment_id)
                                    ->where('direction', $direction)
                                    ->where('lane_pos_number', $lane_pos_number)
                                    ->orderBy('survey_time', 'DESC')
                                    ->get();
        
        $year_now = date('Y');
        if (count($MHHistory) > 0)
        {
            foreach ($MHHistory as $r) 
            {   
                $start = ($r->km_from)*1000 + $r->m_from;
                $end = ($r->km_to)*1000 + $r->m_to;
                $survey_time = explode('-', $r->survey_time);
                $year = $survey_time[0];
                !empty($r->repairCategory->name) ? $r_category = $r->repairCategory->name : $r_category = '';
                for ($i= 0; $i < 5 ; $i++) { 
                    if ($start <= $from && $to <= $end && $year == ($year_now - $i))
                    {   
                        $data['MH'][$year_now - $i][] = [
                                    'id' => $r->id,
                                    'sectiondata_id' => $r->id,
                                    'direction' => $r->direction,
                                    'lane_pos_number' => $r->lane_pos_number,
                                    'from' => $from,
                                    'to' => $to,
                                    'pavement_type' => $r->pavement_type_id,
                                    'r_category' => $r_category,
                                    'completion_date' => $r->completion_date,
                                    'repair_duration' => $r->repair_duration
                                ];  
                    }
                    else if ($from <= $end && $end <= $to && $from >= $start && $year == ($year_now - $i))
                    {
                        $data['MH'][$year_now - $i][] = [
                                    'id' => $r->id,
                                    'sectiondata_id' => $r->id,
                                    'direction' => $r->direction,
                                    'lane_pos_number' => $r->lane_pos_number,
                                    'from' => $from,
                                    'to' => $end,
                                    'pavement_type' => $r->pavement_type_id,
                                    'r_category' => $r_category,
                                    'completion_date' => $r->completion_date,
                                    'repair_duration' => $r->repair_duration
                                ];  
                    }
                    else if ($from <= $start && $start <= $to && $to <= $end && $year == ($year_now - $i) )
                    {
                        $data['MH'][$year_now - $i][] = [
                                    'id' => $r->id,
                                    'sectiondata_id' => $r->id,
                                    'direction' => $r->direction,
                                    'lane_pos_number' => $r->lane_pos_number,
                                    'from' => $start,
                                    'to' => $to,
                                    'pavement_type' => $r->pavement_type_id,
                                    'r_category' => $r_category,
                                    'completion_date' => $r->completion_date,
                                    'repair_duration' => $r->repair_duration
                                ];  
                    }
                    else if ($from <= $start && $end <= $to && $year == ($year_now - $i) )
                    {
                        $data['MH'][$year_now - $i][] = [
                                    'id' => $r->id,
                                    'sectiondata_id' => $r->id,
                                    'direction' => $r->direction,
                                    'lane_pos_number' => $r->lane_pos_number,
                                    'from' => $start,
                                    'to' => $end,
                                    'pavement_type' => $r->pavement_type_id,
                                    'r_category' => $r_category,
                                    'completion_date' => $r->completion_date,
                                    'repair_duration' => $r->repair_duration
                                ];  
                    }
                    else
                    {
                        $data['MH'][$year_now - $i][] = [];
                    }
                } 
            }
        }
        else
        {
            for ($i= 0; $i < 5 ; $i++) { 
                $data['MH'][$year_now - $i][] = [];
            }
        }
        return $data;
    }
    // public function getDataPmosHistory(Request $request)
    // {
    //     $segment_id = $request->segment_id;
    //     $direction = $request->direction;
    //     $from = $request->from;
    //     $to = $request->to;
    //     $lane_pos_number = $request->lane_pos_number;
    //     $data = [];
    //     $lane_no = 0;
    //     $MHHistory = tblSectiondataMH::where('segment_id', $segment_id)
    //                                 // ->where('direction', $direction)
    //                                 // ->where('lane_pos_number', $lane_pos_number)
    //                                 ->orderBy('survey_time', 'DESC')
    //                                 ->get();
    //     $year_now = date('Y');
    //     if (!empty($MHHistory))
    //     {
    //         foreach ($MHHistory as $r) 
    //         {   
    //             $start = ($r->km_from)*1000 + $r->m_from;
    //             $end = ($r->km_to)*1000 + $r->m_to;
    //             $survey_time = explode('-', $r->survey_time);
    //             $year = $survey_time[0];
    //             ///dd($year);
    //             !empty($r->repairCategory->name) ? $r_category = $r->repairCategory->name : $r_category = '';
    //             for ($i= 0; $i < 5 ; $i++) { 
    //                 if ($start <= $from && $to <= $end && $year == ($year_now - $i))
    //                 {   
    //                     $data['MH'][$year_now - $i][] = [
    //                                 'id' => $r->id,
    //                                 'sectiondata_id' => $r->id,
    //                                 'direction' => $r->direction,
    //                                 'lane_pos_number' => $r->lane_pos_number,
    //                                 'from' => $from,
    //                                 'to' => $to,
    //                                 'pavement_type' => $r->pavement_type_id,
    //                                 'r_category' => $r_category,
    //                                 'completion_date' => $r->survey_time,
    //                                 'repair_duration' => $r->repair_duration
    //                             ];
    //                     if ($r->lane_pos_number > $lane_no)
    //                     {
    //                         $lane_no = $r->lane_pos_number;
    //                     }  
    //                 }
    //                 else if ($from <= $end && $end <= $to && $from >= $start && $year == ($year_now - $i))
    //                 {
    //                     $data['MH'][$year_now - $i][] = [
    //                                 'id' => $r->id,
    //                                 'sectiondata_id' => $r->id,
    //                                 'direction' => $r->direction,
    //                                 'lane_pos_number' => $r->lane_pos_number,
    //                                 'from' => $from,
    //                                 'to' => $end,
    //                                 'pavement_type' => $r->pavement_type_id,
    //                                 'r_category' => $r_category,
    //                                 'completion_date' => $r->survey_time,
    //                                 'repair_duration' => $r->repair_duration
    //                             ];
    //                     if ($r->lane_pos_number > $lane_no)
    //                     {
    //                         $lane_no = $r->lane_pos_number;
    //                     }  
    //                 }
    //                 else if ($from <= $start && $start <= $to && $to <= $end && $year == ($year_now - $i) )
    //                 {
    //                     $data['MH'][$year_now - $i][] = [
    //                                 'id' => $r->id,
    //                                 'sectiondata_id' => $r->id,
    //                                 'direction' => $r->direction,
    //                                 'lane_pos_number' => $r->lane_pos_number,
    //                                 'from' => $start,
    //                                 'to' => $to,
    //                                 'pavement_type' => $r->pavement_type_id,
    //                                 'r_category' => $r_category,
    //                                 'completion_date' => $r->survey_time,
    //                                 'repair_duration' => $r->repair_duration
    //                             ];
    //                     if ($r->lane_pos_number > $lane_no)
    //                     {
    //                         $lane_no = $r->lane_pos_number;
    //                     }  
    //                 }
    //                 else if ($from <= $start && $end <= $to && $year == ($year_now - $i) )
    //                 {
    //                     $data['MH'][$year_now - $i][] = [
    //                                 'id' => $r->id,
    //                                 'sectiondata_id' => $r->id,
    //                                 'direction' => $r->direction,
    //                                 'lane_pos_number' => $r->lane_pos_number,
    //                                 'from' => $start,
    //                                 'to' => $end,
    //                                 'pavement_type' => $r->pavement_type_id,
    //                                 'r_category' => $r_category,
    //                                 'completion_date' => $r->survey_time,
    //                                 'repair_duration' => $r->repair_duration
    //                             ]; 
    //                     if ($r->lane_pos_number > $lane_no)
    //                     {
    //                         $lane_no = $r->lane_pos_number;
    //                     } 
    //                 }
    //                 else
    //                 {
    //                     $data['MH'][$year_now - $i][] = [];
    //                 }
    //             } 
    //         }
    //     }
    //     if ($lane_no >= 1) 
    //     {
    //         $segment_info = 2 * $lane_no;
    //     }
    //     else
    //     {
    //         $segment_info = 1;
    //     }
    //     return array(
    //         'data' => $data,
    //         'lane_no' => $segment_info
    //     );
    // }
    public function getData(Request $request)
    {
        $segment_id = $request->segment_id;
        $from = $request->from;
        $to = $request->to;
        $st_from = substr($from, 0, -3);
        $st_to = number_format(substr($from, -3));
        $end_from = substr($to, 0, -3);
        $end_to = number_format(substr($to, -3));
        $segment = tblSegment::findOrFail($segment_id);
        $getData = tblPMSSectioning::with([
                    'infos' => function($q) {
                        $q->with(['pcs' => function($pc){
                            $pc->select('id', 'PMS_info_id', 'IRI', 'MCI', 'cracking', 'rutting_max', 'rutting_ave', 'IRI', 'MCI');
                        }])
                        ->select('condition_year', 'type_id', 'id', 'PMS_section_id','condition_month')
                        ->where('type_id', 3)
                        ->orderBy('condition_year');
                    }])
                    ->whereHas('infos', function($q) {
                        $q->where('type_id', 3);
                    })
                    ->select('id', 'km_from', 'm_from', 'km_to', 'm_to', 'direction', 'branch_id', 'lane_pos_no')
                    ->whereRaw("(1000000 * km_from + m_from >= ? AND 1000000 * km_to + m_to <= ?)", [1000000 * $st_from + $st_to, 1000000 * $end_from + $end_to])
                    ->where('branch_id', '=', $segment->branch_id)
                    ->get();

        $checkData = [];
        $lane_no = 0;
        foreach ($getData as $value) {
            $check = $this->_getTimeSeriesData($value,$lane_no);
            $checkData[] = $check['tmp'];
            $lane_no = $check['lane_no'];
        }
        if ($lane_no >= 1) 
        {
            $segment_info = 2 * $lane_no;
        }
        else
        {
            $segment_info = 1;
        }
        return array(
            'data' => $checkData,
            'lane_no' => $segment_info
        );
    }

    public function index(Request $request)
    {
        $segment_id = $request->segment_id;
        $direction = $request->direction;
        $from = $request->from;
        $to = $request->to;
        $lane_pos_number = $request->lane_pos_number;
        $data = [];
        $lane_no = 0;

        // RMD
        $RMDHistory = tblSectiondataRMD::where('segment_id', $segment_id)
                                    ->where('direction', $direction)
                                    ->where('lane_pos_number', $lane_pos_number)
                                    ->orderBy('survey_time', 'DESC')
                                    ->get();
       // dd($RMDHistory);
        if (!empty($RMDHistory))
        {
            foreach ($RMDHistory as $r) 
            {   
                
                $start = ($r->km_from)*1000 + $r->m_from;
                $end = ($r->km_to)*1000 + $r->m_to;
                $survey_time = explode('-', $r->survey_time);
                $year = $survey_time[0];
                if ($start <= $from && $to <= $end)
                {   
                    
                    $data['RMD'][$year][] = [
                                'id' => $r->id,
                                'sectiondata_id' => $r->id,
                                'direction' => $r->direction,
                                'lane_pos_number' => $r->lane_pos_number,
                                'from' => $from,
                                'to' => $to,
                            ];  
                }
                else if ($from <= $end && $end <= $to && $from >= $start)
                {
                    $data['RMD'][$year][] = [
                                'id' => $r->id,
                                'sectiondata_id' => $r->id,
                                'direction' => $r->direction,
                                'lane_pos_number' => $r->lane_pos_number,
                                'from' => $from,
                                'to' => $end,
                            ];  
                }
                else if ($from <= $start && $start <= $to && $to <= $end)
                {
                    $data['RMD'][$year][] = [
                                'id' => $r->id,
                                'sectiondata_id' => $r->id,
                                'direction' => $r->direction,
                                'lane_pos_number' => $r->lane_pos_number,
                                'from' => $start,
                                'to' => $to,
                            ];  
                }
                else if ($from <= $start && $end <= $to)
                {
                   $data['RMD'][$year][] = [
                                'id' => $r->id,
                                'sectiondata_id' => $r->id,
                                'direction' => $r->direction,
                                'lane_pos_number' => $r->lane_pos_number,
                                'from' => $start,
                                'to' => $end,
                            ];  
                }
            }
        }
        
        // MH
        $MHHistory = tblSectiondataMH::where('segment_id', $segment_id)
                                    ->where('direction', $direction)
                                    ->where('lane_pos_number', $lane_pos_number)
                                    ->orderBy('survey_time', 'DESC')
                                    ->get();
        if (!empty($MHHistory))
        {
            foreach ($MHHistory as $r) 
            {   
                $start = ($r->km_from)*1000 + $r->m_from;
                $end = ($r->km_to)*1000 + $r->m_to;
                $survey_time = explode('-', $r->survey_time);
                $year = $survey_time[0];
                if ($start <= $from && $to <= $end)
                {   
                    $data['MH'][$year][] = [
                                'id' => $r->id,
                                'sectiondata_id' => $r->id,
                                'direction' => $r->direction,
                                'lane_pos_number' => $r->lane_pos_number,
                                'from' => $from,
                                'to' => $to
                            ];  
                }
                else if ($from <= $end && $end <= $to && $from >= $start)
                {
                    $data['MH'][$year][] = [
                                'id' => $r->id,
                                'sectiondata_id' => $r->id,
                                'direction' => $r->direction,
                                'lane_pos_number' => $r->lane_pos_number,
                                'from' => $from,
                                'to' => $end
                            ];  
                }
                else if ($from <= $start && $start <= $to && $to <= $end)
                {
                    $data['MH'][$year][] = [
                                'id' => $r->id,
                                'sectiondata_id' => $r->id,
                                'direction' => $r->direction,
                                'lane_pos_number' => $r->lane_pos_number,
                                'from' => $start,
                                'to' => $to
                            ];  
                }
                else if ($from <= $start && $end <= $to)
                {
                    $data['MH'][$year][] = [
                                'id' => $r->id,
                                'sectiondata_id' => $r->id,
                                'direction' => $r->direction,
                                'lane_pos_number' => $r->lane_pos_number,
                                'from' => $start,
                                'to' => $end
                            ];  
                }
            }
        }   

        // TV
        $TVHistory = tblSectiondataTV::where('segment_id', $segment_id)->orderBy('survey_time', 'DESC')->get();
        if (!empty($TVHistory))
        {
            foreach ($TVHistory as $t) 
            {
                $survey_time = explode('-', $t->survey_time);
                $year = $survey_time[0];
                $station = ($t->km_station)*1000 + $t->m_station;
                if ($from <= $station && $station <=$to) 
                {
                    $data['TV'][$year][] = [
                                'id' => $t->id,
                                'sectiondata_id' => $t->sectiondata_id,
                                'station' => $station
                            ];
                }
            }
        }
        return $data;
    }   

    private function _getTimeSeriesData($data,$lane_no)
    {
        if ($data->lane_pos_no > $lane_no)
        {
            $lane_no = $data->lane_pos_no;
        }
        $tmp = [
                'km_from' => $data->km_from,
                'm_from' => $data->m_from,
                'km_to' => $data->km_to,
                'm_to' => $data->m_to,
                'direction' => $data->direction,
                'lane_pos_no' => $data->lane_pos_no
            ];
        $infos = $data->infos[0];
        $tmp['type_id'] = $infos->type_id;
        $tmp['condition_year'] = $infos->condition_year;
        $tmp['condition_month'] = $infos->condition_month;
        $pcs = $infos->pcs[0];
        $tmp['idd'] = $pcs->id;
        $tmp['cracking'] = $pcs->cracking;
        $tmp['rutting_max'] = $pcs->rutting_max;
        $tmp['rutting_ave'] = $pcs->rutting_ave;
        $tmp['IRI'] = $pcs->IRI;
        $tmp['MCI'] = $pcs->MCI;

        return array(
            'tmp' => $tmp,
            'lane_no' => $lane_no
        );
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
