<?php

namespace App\Http\Controllers\Ajax\PMOS;

use Illuminate\Http\Request;
use App\Http\Requests;
use Auth;
use Yajra\Datatables\Datatables;
use App\Models\tblPMSDataset;
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
class PMOSController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function getYear($id)
    {
        $dataset = [];

        $data_year = tblSectiondataMH::where('segment_id', $id)
        ->groupBy(\DB::raw('YEAR(survey_time)'))->get();
        if(count($data_year) > 0){
            foreach ($data_year as $year) 
            {
                $dataset[] = array(
                    'name' => substr($year->survey_time, 0, 4),
                    'value' => substr($year->survey_time, 0, 4)
                );
            }
        }
        else
        {
            $dataset[] = array(
                'name' => trans('back_end.noYear'),
                'value' => ''
            ); 
        }
        return $dataset;
    }

    public function index()
    {
        
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create(Request $request)
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
