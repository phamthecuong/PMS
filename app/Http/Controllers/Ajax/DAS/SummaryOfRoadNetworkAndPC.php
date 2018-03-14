<?php

namespace App\Http\Controllers\Ajax\DAS;

use App\Models\tblOrganization;
use App\Models\tblPMSRIInfo;
use App\Models\tblSectionPCHistory;
use App\Models\tblSegment;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;

class SummaryOfRoadNetworkAndPC extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */

    // public function getData($id)
    // {
    //     $data = [];
    //     $segment = tblSegment::where('SB_id', $id)->pluck('id')->toArray();
    //     $ri_info = DB::table('tblPMS_RI_info')->whereIn('segment_id', $segment)->pluck('id')->toArray();
    //     $result = DB::table('tblSection_PC_history')->where('SB_id', $id)->groupBy('date_y')->pluck('date_y')->toArray();
    //     foreach ($result as $r)
    //     {
    //         $obj = (object) [];
    //         $obj->year = $r;
    //         $obj->pc_ac = @DB::table('tblSection_PC_history')->where('SB_id', $id)
    //             ->where('date_y', $r)->where('surface_type', 'AC')->sum('section_length');
    //         $obj->pc_bst = @DB::table('tblSection_PC_history')->where('SB_id',$id)
    //             ->where('date_y', $r)->where('surface_type', 'BST')->sum('section_length');
    //         $obj->pc_cc = @DB::table('tblSection_PC_history')->where('SB_id', $id)
    //             ->where('date_y', $r)->where('surface_type', 'CC')->sum('section_length');
    //         $obj->pc_total = @DB::table('tblSection_PC_history')->where('SB_id', $id)
    //             ->where('date_y', $r)->sum('section_length');
    //         $data[] = $obj;
    //     }
    //     return $data;
    // }

    public function index(Request $request)
    {
        $data = [];
        if (!empty($request->sb))
        {
            $sb_id = [$request->sb];
            $branch_id = DB::table('tblSection_PC_history')->where('SB_id', $sb_id)->where('branch_id', '!=', 0)
                ->groupBy('branch_id')->pluck('branch_id')->toArray();
        }
        else
        {
            $sb_id = tblOrganization::where('parent_id', $request->rmb)->pluck('id')->toArray();
            $branch_id = DB::table('tblSection_PC_history')->whereIn('SB_id', $sb_id)->where('branch_id', '!=', 0)
               ->groupBy('branch_id')->pluck('branch_id')->toArray();
        }
        
        $result = DB::table('tblSection_PC_history')
            ->select('date_y', 'surface_type', DB::raw('sum(section_length) as section_length'))
            ->whereIn('SB_id', $sb_id)
            ->whereIn('branch_id', $branch_id)
            ->groupBy('date_y')
            ->groupBy('surface_type')
            ->orderBy('date_y', 'desc')
            ->get();
        
        foreach ($result as $r)
        {
            $data[$r->date_y]['pc_' . strtolower($r->surface_type)] = $r->section_length;
            if (!isset($data[$r->date_y]['pc_total']))
            {
                $data[$r->date_y]['pc_total'] = 0;
            }
            $data[$r->date_y]['pc_total']+= $r->section_length;
            $data[$r->date_y]['year'] = $r->date_y;
        }
        return array_values($data);
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

    function getRILength(Request $request)
    {
        // if (!empty($request->sb))
        // {
        //     $sb_id = [$request->sb];
        // }
        // else
        // {
        //     $sb_id = tblOrganization::where('parent_id', $request->rmb)->pluck('id')->toArray();
        // }
        // $segment = tblSegment::whereIn('SB_id', $sb_id)->pluck('id')->toArray();

        // $sql = "select (count(id) * 100) as total_length from (SELECT * FROM `tblPMS_RI_info` WHERE segment_id IN (" . implode(',', $segment) . ') ';
        // if (!empty($request->pavement_type))
        // {
        //     $sql.= "and pavement_type_code = " . $request->pavement_type ;
        // }
        // $sql.= " and PMS_info_id IN (SELECT id FROM `tblPMS_sectioning_info` i where type_id = 1 and id = (SELECT id from tblPMS_sectioning_info where PMS_section_id = i.`PMS_section_id` and type_id = 1 and condition_year <= ? order by condition_year desc limit 1))) a";
        

        // $rsl = DB::select($sql, [$request->year]);
        // return $rsl[0]->total_length;
        return \App\Classes\Helper::calculateIntegratedRILength($request->rmb, $request->sb, null, $request->year, $request->pavement_type);
    }
}
