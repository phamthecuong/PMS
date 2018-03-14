<?php

namespace App\Http\Controllers\Ajax\Frontend;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\tblSectiondataRMD;
use App\Models\tblSectiondataMH;
use App\Models\tblSectiondataTV;
use App\Models\tblSegment;
use DB;
class DataSegmentController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {   
        $sg_id = $request->segment_id;
        $data_RMD = [];
        $no_lane = 0;
        $info_segment = tblSegment::find($sg_id);
        $RMD = tblSectiondataRMD::where('segment_id', $sg_id)
                            ->select('km_from', 'm_from', 'km_to', 'm_to', 'direction', 'lane_pos_number')
                            ->get();
        if (!empty($RMD))
        {
            foreach ($RMD as $r)
            {
                $data_RMD[] = [
                        'km_from' => $r->km_from, 
                        'm_from' => $r->m_from,
                        'km_to' => $r->km_to,
                        'm_to' => $r->m_to,
                        'direction' => $r->direction,
                        'lane_pos_number' => $r->lane_pos_number,
                    ];
                if ($r->lane_pos_number > $no_lane) 
                {
                    $no_lane = $r->lane_pos_number;
                }
            }
        }
        $data_MH = [];
        $MH = tblSectiondataMH::where('segment_id', $sg_id)
                            ->select('km_from', 'm_from', 'km_to', 'm_to', 'direction', 'lane_pos_number')
                            ->get();
        if (!empty($MH))
        {
            foreach ($MH as $r)
            {
                $data_MH[] = [
                        'km_from' => $r->km_from, 
                        'm_from' => $r->m_from,
                        'km_to' => $r->km_to,
                        'm_to' => $r->m_to,
                        'direction' => $r->direction,
                        'lane_pos_number' => $r->lane_pos_number,

                    ];
                if ($r->lane_pos_number > $no_lane) 
                {
                    $no_lane = $r->lane_pos_number;
                }
            }
        }
        $data_TV = [];
        $TV = tblSectiondataTV::where('segment_id', $sg_id)
                            ->select('km_station', 'm_station')
                            ->get();
        if (!empty($TV))
        {
            foreach ($TV as $r)
            {
                $data_TV[] = [
                        'km_station' => $r->km_station,
                        'm_station' => $r->m_station,
                    ];
            }    
        }

        $info = [
                'km_from' => $info_segment->km_from,
                'm_from' => $info_segment->m_from,
                'km_to' => $info_segment->km_to,
                'm_to' => $info_segment->m_to,
                'no_lane' => 2*$no_lane
            ];
        return [ 
                'RMD' => $data_RMD, 
                'MH' => $data_MH, 
                'TV' => $data_TV, 
                'info' => $info
            ];
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
