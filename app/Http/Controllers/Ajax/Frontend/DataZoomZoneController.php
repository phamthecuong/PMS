<?php

namespace App\Http\Controllers\Ajax\Frontend;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\tblSectiondataRMD;
use App\Models\tblSectiondataMH;
use App\Models\tblSectiondataTV;
use App\Models\tblSegment;
use App\Models\tblPMSSectioning;
use App\Models\tblPMSSectioningInfo;
use App\Models\tblPMSPCInfo;
use DB;
use App\Classes\Helper;
class DataZoomZoneController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $segment_id = $request->segment_id;
        $from = $request->limit_left;
        $to = $request->limit_right; 
        $data = [];
        $lane_no = 0;
        $tblSectiondataMH = tblSectiondataMH::with('repairCategory')
                            ->where('segment_id', $segment_id)
                            ->orderBy('survey_time')
                            ->get();

        if (!empty($tblSectiondataMH))
        {
            $data['MH'] = [];
            $tmp_data = [];
            foreach ($tblSectiondataMH as $m) 
            {
                !empty($m->repairCategory->name) ? $r_category = $m->repairCategory->name : $r_category = '';
                $start = ($m->km_from)*1000 + $m->m_from;
                $end = ($m->km_to)*1000 + $m->m_to;
                if ($start <= $from && $to <= $end)
                {
                    $tmp_data = [
                                'id' => $m->id,
                                'from' => $from, 
                                'to' => $to,
                                'direction' => $m->direction,
                                'lane_width' => $m->total_width_repair_lane,
                                'lane_pos_number' => $m->lane_pos_number,
                                'direction_running' => $m->direction_running,
                                'distance' => $m->distance,
                                'pavement_type' => $m->pavement_type_id,
                                'classification' => $m->r_classification_id,
                                'r_category' => $r_category,
                                'completion_date' => $m->completion_date,
                                'repair_duration' => $m->repair_duration
                            ];
                    if ($m->lane_pos_number > $lane_no)
                    {
                        $lane_no = $m->lane_pos_number;
                    }
                }
                else if ($from <= $end && $end <= $to && $from >= $start)
                {
                    $tmp_data = [
                                'id' => $m->id,
                                'from' => $from, 
                                'to' => $end, 
                                'direction' => $m->direction,
                                'lane_width' => $m->total_width_repair_lane,
                                'lane_pos_number' => $m->lane_pos_number,
                                'direction_running' => $m->direction_running,
                                'distance' => $m->distance,
                                'pavement_type' => $m->pavement_type_id,
                                'classification' => $m->r_classification_id,
                                'r_category' => $r_category,
                                'completion_date' => $m->completion_date,
                                'repair_duration' => $m->repair_duration
                            ];
                    if ($m->lane_pos_number > $lane_no)
                    {
                        $lane_no = $m->lane_pos_number;
                    }
                }
                else if ($from <= $start && $start <= $to && $to <= $end)
                {
                    $tmp_data = [
                                'id' => $m->id,
                                'from' => $start,
                                'to' => $to, 
                                'direction' => $m->direction,
                                'lane_width' => $m->total_width_repair_lane,
                                'lane_pos_number' => $m->lane_pos_number,
                                'direction_running' => $m->direction_running,
                                'distance' => $m->distance,
                                'pavement_type' => $m->pavement_type_id,
                                'classification' => $m->r_classification_id,
                                'r_category' => $r_category,
                                'completion_date' => $m->completion_date,
                                'repair_duration' => $m->repair_duration
                            ];
                    if ($m->lane_pos_number > $lane_no)
                    {
                        $lane_no = $m->lane_pos_number;
                    }
                }
                else if ($from <= $start && $end <= $to)
                {
                    $tmp_data = [
                                'id' => $m->id,
                                'from' => $start, 
                                'to' => $end, 
                                'direction' => $m->direction,
                                'lane_width' => $m->total_width_repair_lane,
                                'lane_pos_number' => $m->lane_pos_number,
                                'direction_running' => $m->direction_running,
                                'distance' => $m->distance,
                                'pavement_type' => $m->pavement_type_id,
                                'classification' => $m->r_classification_id,
                                'r_category' => $r_category,
                                'completion_date' => $m->completion_date,
                                'repair_duration' => $m->repair_duration
                            ];
                    if ($m->lane_pos_number > $lane_no)
                    {
                        $lane_no = $m->lane_pos_number;
                    }
                }
                
                $data['MH'] = $this->_getLastestData($data['MH'], $tmp_data);
            }
            
        }
        $tblSectiondataRMD = tblSectiondataRMD::where('segment_id', $segment_id)->orderBy('survey_time')->get();
        if (!empty($tblSectiondataRMD))
        {   
            $data['RMD'] = [];
            $tmp_data = [];
            foreach ($tblSectiondataRMD as $m) 
            {
                $start = ($m->km_from)*1000 + $m->m_from;
                $end = ($m->km_to)*1000 + $m->m_to;
                $year_of_const =  isset($m->construct_year) ? (substr($m->construct_year, 4, 2)) . '/' . substr($m->construct_year, 0, 4) : '';
                $service_start_year =  isset($m->service_start_year) ? (substr($m->service_start_year, 4, 2)) . '/' . substr($m->service_start_year, 0, 4) : '';

                if ($start <= $from && $to <= $end)
                {
                    $tmp_data = [
                                'id' => $m->id,
                                'from' => $from, 
                                'to' => $to,
                                'lane_width' => $m->lane_width ,
                                'direction' => $m->direction,
                                'lane_pos_number' => $m->lane_pos_number,
                                'pavement_type' => $m->pavement_type_id,
                                'year_of_const' => $year_of_const,
                                'service_start_year' => $service_start_year
                            ];
                    if ($m->lane_pos_number > $lane_no)
                    {
                        $lane_no = $m->lane_pos_number;
                    }
                }
                else if ($from <= $end && $end <= $to && $from >= $start)
                {
                    $tmp_data = [
                                'id' => $m->id,
                                'from' => $from, 
                                'to' => $end, 
                                'lane_width' => $m->lane_width ,
                                'direction' => $m->direction,
                                'lane_pos_number' => $m->lane_pos_number,
                                'pavement_type' => $m->pavement_type_id,
                                'year_of_const' => $year_of_const,
                                'service_start_year' => $service_start_year

                            ];
                    if ($m->lane_pos_number > $lane_no)
                    {
                        $lane_no = $m->lane_pos_number;
                    }
                }
                else if ($from <= $start && $start <= $to && $to <= $end)
                {
                    $tmp_data = [
                                'id' => $m->id,
                                'from' => $start,
                                'to' => $to, 
                                'lane_width' => $m->lane_width ,
                                'direction' => $m->direction,
                                'lane_pos_number' => $m->lane_pos_number,
                                'pavement_type' => $m->pavement_type_id,
                                'year_of_const' => $year_of_const,
                                'service_start_year' => $service_start_year
                            ];
                    if ($m->lane_pos_number > $lane_no)
                    {
                        $lane_no = $m->lane_pos_number;
                    }
                }
                else if ($from <= $start && $end <= $to)
                {
                    $tmp_data = [
                                'id' => $m->id,
                                'from' => $start, 
                                'to' => $end, 
                                'lane_width' => $m->lane_width ,
                                'direction' => $m->direction,
                                'lane_pos_number' => $m->lane_pos_number,
                                'pavement_type' => $m->pavement_type_id,
                                'year_of_const' => $year_of_const,
                                'service_start_year' => $service_start_year
                            ];
                    if ($m->lane_pos_number > $lane_no)
                    {
                        $lane_no = $m->lane_pos_number;
                    }
                }
                $data['RMD'] = $this->_getLastestData($data['RMD'] ,$tmp_data);
            }
        }
      
        $tblSectiondataTV = tblSectiondataTV::where('segment_id', $segment_id)->get();
        if (!empty($tblSectiondataTV))
        {
            foreach ($tblSectiondataTV as $t)
            {
                $tv = $t->km_station*1000 + $t->m_station;
                if ($from <= $tv && $tv <= $to)
                {
                    $data['TV'][] = [ 'id' => $t->id, 'station' => $tv];

                }
            }
        }
        if ($lane_no >= 1) 
        {
            $data['segment_info'] = ['lane_no' => 2 * $lane_no];
        }
        else
        {
            $data['segment_info'] = ['lane_no' => 1];
        }
        
        return $data;
    }

    /*
    * $data[] = array();
    */
    private function _getLastestData($ouput_data, $data)
    {
        if (!empty($ouput_data) && !empty($data))
        {
            foreach ($ouput_data as $k =>  $r)
            {
                if ($r['lane_pos_number'] == $data['lane_pos_number'] && $r['direction'] == $data['direction'])
                {
                    if ($r['from'] < $data['to'] && $r['from'] >= $data['from'] && $r['to'] > $data['to'])
                    {
                        $ouput_data[$k]['from'] = $data['to'] ;
                    }
                    else if ($r['to'] > $data['from'] && $r['from'] < $data['from'] && $r['to'] <= $data['to'])
                    {
                        $ouput_data[$k]['to'] = $data['from'];
                    }
                    else if($r['from'] < $data['from'] && $r['to'] > $data['to'])
                    {
                        $a = $ouput_data[$k];
                        $b = $ouput_data[$k];
                        unset($ouput_data[$k]);  
                        $a['to'] = $data['from'];
                        $b['from'] = $data['to'];   
                        $ouput_data[]= $a;                      
                        $ouput_data[]= $b;
                    }
                    else if ($r['from'] >= $data['from'] && $r['to'] <= $data['to']) 
                    {
                        unset($ouput_data[$k]);
                    }
                }
            }
        }
        if (!empty($data))
        {
            $ouput_data[] = $data;    
        }
        return $ouput_data;
    }

    function JoinData()
    {   
        $data = DB::table('tblPMS_sectioning AS p')
            ->select(DB::raw('p.km_from, p.m_from, p.km_to, p.m_to, p.direction, p.branch_id,p.lane_pos_no,pc.cracking,pc.IRI,pc.MCI'))
            ->join('tblPMS_sectioning_info AS h', 'p.id', '=', 'h.PMS_section_id')
            ->join('tblPMS_PC_info AS pc', 'h.id', '=', 'pc.PMS_info_id');
        return $data;
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
