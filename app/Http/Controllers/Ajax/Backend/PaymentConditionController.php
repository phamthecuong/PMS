<?php

namespace App\Http\Controllers\Ajax\Backend;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\tblSectionPCHistory;
use Auth, App, DB, Helper;
use Yajra\Datatables\Facades\Datatables;
class PaymentConditionController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $PC_history = DB::table('tblSection_PC_history') ->select('O.sb_name_en', 'O.sb_name_vn', 'O.rmb_name_en',
            'O.rmb_name_vn','O.rmb_id', 'tblBranch.road_number','tblBranch.road_number_supplement',
            'tblBranch.name_en as branch_name_en ','tblBranch.branch_number', 'tblBranch.name_vn as branch_name_vn',
            'tblSection_PC_history.SB_id', 'tblSection_PC_history.branch_id' , 'tblSection_PC_history.direction' ,
            'tblSection_PC_history.geographical_area', 'tblSection_PC_history.km_from', 'tblSection_PC_history.m_from',
            'tblSection_PC_history.km_to', 'tblSection_PC_history.m_to', 'tblSection_PC_history.section_length',
            'tblSection_PC_history.analysis_area', 'tblSection_PC_history.structure', 'tblSection_PC_history.intersection',
            'tblSection_PC_history.overlapping', 'tblSection_PC_history.number_of_lane_U', 'tblSection_PC_history.number_of_lane_D',
            'tblSection_PC_history.lane_position_no', 'tblSection_PC_history.surface_type', 'tblSection_PC_history.date_y',
            DB::raw('(tblSection_PC_history.date_m+0) as date_m'), 'tblSection_PC_history.cracking_ratio_cracking',
            'tblSection_PC_history.cracking_ratio_patching', 'tblSection_PC_history.cracking_ratio_pothole',
            'tblSection_PC_history.cracking_ratio_total', 'tblSection_PC_history.rutting_depth_max',
            'tblSection_PC_history.rutting_depth_ave', 'tblSection_PC_history.IRI', 'tblSection_PC_history.MCI',
            'tblSection_PC_history.note')
            ->join(DB::raw('(select sb.id as id,sb.name_en as sb_name_en, sb.name_vn as sb_name_vn, rmb.id as rmb_id ,rmb.name_en as rmb_name_en, rmb.name_vn as rmb_name_vn from tblOrganization as sb, tblOrganization as rmb where sb.parent_id = rmb.id) as O'), 'O.id','=', 'tblSection_PC_history.SB_id' )
            ->join('tblBranch', 'tblBranch.id', '=', 'tblSection_PC_history.branch_id');
         
            $PC_history = $this->filterByCondition($PC_history, $request);
            $PC_history = $this->filterSuperInput($PC_history, 'km_from' , 'km_from', $request);
            $PC_history = $this->filterSuperInput($PC_history, 'km_to' , 'km_to', $request);
            $PC_history = $this->filterSuperInput($PC_history, 'm_from' , 'm_from', $request);
            $PC_history = $this->filterSuperInput($PC_history, 'm_to' , 'm_to', $request);
            $PC_history = $this->filterInput($PC_history, 'direction' , 'direction', $request);
            $PC_history = $this->filterSuperInput($PC_history, 'lane_position_no' , 'lane_position_no', $request);
            $PC_history = $this->filterSuperInput($PC_history, 'date_y' , 'date_y', $request);
            $PC_history = $this->filterSuperInput($PC_history, 'date_m' , 'date_m', $request);
            
        return Datatables::of($PC_history)->make(true);
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

    function filterSuperInput($query, $key_in_request, $key_in_db,  $request)
    {   
        if ($request->{$key_in_request} != '')
        {   
            $data_request = $request->{$key_in_request};
            $parseSuperInput = Helper::parseSuperInput($data_request);
            if (is_numeric($parseSuperInput[1]))
            {
               // dd(($parseSuperInput[0]));
                $query =  $query->having("{$key_in_db}", $parseSuperInput[0], $parseSuperInput[1]);
                //dd($query->toSql());
            }
            // else if(is_string($parseSuperInput[1]))
            // {
            //     $query = $query->where($key_in_db , $parseSuperInput[1]);
            // }
            else
            {
                $query = $query->where("tblSection_PC_history.{$key_in_db}" , null);
            }
        }
        return $query;
    }

    function filterInput($query, $key_in_request, $key_in_db,  $request)
    {
        if ($request->{$key_in_request} != '')
        {   
            $query = $query->where($key_in_db , $request->{$key_in_request});
        }
        return $query;
    }

    function scopeFilterDropdown($query, $key_in_request, $key_in_db, $request)
    {
        if (isset($request->{$key_in_request}) && !empty($request->{$key_in_request}))
        {
            $query =  $query->where($key_in_db, $request->{$key_in_request});
        }
        return $query;
    }

    function filterByCondition($query, $request)
    {   
        $lang = App::isLocale('en') ? 'en' : 'vn';
        $rmb_name = "rmb_name_{$lang}";
        $sb_name = "sb_name_{$lang}";
        if ((int)$request->$rmb_name != -1)
        {
            $query = $query->where('O.rmb_id', $request->$rmb_name);
        }
        if ((int)$request->$sb_name != -1)
        {
            $query = $query->where('O.id', $request->$sb_name);
        }
        return $query;
        
    }
}
