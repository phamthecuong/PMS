<?php

namespace App\Http\Controllers\Ajax\DAS;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\tblSegment;
use App\Models\tblSectiondataMH;
use App\Models\tblOrganization;
use DB, App;
class SummaryPassedTimeController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {   
        //DB::enableQueryLog();
        $branch_id = $request->branch_id;
        $sb_id = $request->sb_id;
        $rmb_id = $request->rmb_id;
        $segment_id = [];
        $data = [];
        $segment = tblSegment::select('id');
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
        foreach ($segment->get() as $r)
        {
            $segment_id[] = $r->id; 
        }
        $sectiondataMH = tblSectiondataMH::selectRaw('YEAR(survey_time) as year, sum(actual_length) as total')
                                    ->whereIn('segment_id', $segment_id)
                                    ->groupBy(DB::raw('YEAR(survey_time)'))
                                    ->orderBy(DB::raw('year'), 'desc')
                                    ->get();
        foreach ($sectiondataMH as $r)
        {
            $data[] = ['survey_time' => date('Y') - $r->year, 'total_length' => $r->total];
        }
        return $data;
    }

    public function getDataTable(Request $request)
    {
        $branch_id = $request->branch_id;
        $sb_id = $request->sb_id;
        $rmb_id = $request->rmb_id;
        $data = [];
        $lang = App::isLocale('en') ? 'en' : 'vn';
        $name = 'name_'.$lang;
        $rmb_name = tblOrganization::where('id', $rmb_id)->first()->$name;

        // $segment = tblSegment::Has('tblBranch')->with('tblBranch')->select('id', 'branch_id');
        // if ($sb_id == -1  && $branch_id == -1) 
        // {
        //     $sb_id = tblOrganization::where('parent_id', $rmb_id)->pluck('id')->toArray();
        //     $segment = $segment->whereIn('sb_id', $sb_id);
        // }
        // else if ($sb_id != -1 && $branch_id == -1)
        // {
        //     $segment = $segment->where('sb_id', $sb_id);
        // }
        // else if ($sb_id == -1 && $branch_id != -1) 
        // {   
        //     $sb_id = tblOrganization::where('parent_id', $rmb_id)->pluck('id')->toArray();
        //     $segment = $segment->where('branch_id', $branch_id)->whereIn('sb_id', $sb_id);
        // }
        // else
        // {
        //     $segment = $segment->where('branch_id', $branch_id)
        //                        ->where('SB_id', $sb_id);
        // }
        
        // foreach ($segment->get() as $r)
        // {
        //     $segment_data[$r->tblBranch->$name][] = $r->id;    
        // }
        // dd($segment_data);
        // foreach ($segment_data as $k => $r)
        // {
        //     $data_tmp = $this->_condition($r);
        //     if ($data_tmp)
        //     {
        //         foreach ($data_tmp as $d) {
        //             $data[] = [
        //                 'RMB' => $rmb_name,
        //                 'SB' => $d->segment->tblOrganization->$name,
        //                 'route_name' => $k,
        //                 'elapsed_time' => date('Y')- $d->year,
        //                 'year' => $d->year,
        //                 'section_length' => $d->total
        //             ];    
        //         }    
        //     }
        // }
        // print '<pre>';
        // print_r(collect($data)->sortBy('SB'));dd('');

        $record = DB::table('tblSectiondata_MH')
            ->select(DB::raw("'{$rmb_name}' as RMB, tblOrganization.{$name} as SB, SUM(actual_length) as section_length, (YEAR(NOW()) - year(survey_time)) as elapsed_time, year(survey_time) as `year`, tblBranch.{$name} as route_name, tblSegment.SB_id"))
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
        return $record;
        // $data = collect($data)->sortBy('SB');
        // return array_values($data->toArray());
    }

    public function _condition($segment_id) 
    {
        // $data = tblSectiondataMH::with('segment', 'segment.tblOrganization')->selectRaw('YEAR(survey_time) as year, sum(actual_length) as total, segment_id')
        //                 ->whereIn('segment_id', $segment_id)
        //                 ->groupBy(DB::raw('YEAR(survey_time)'))
        //                 ->orderBy(DB::raw('year'), 'desc')
        //                 ->get();
        // return $data;
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
