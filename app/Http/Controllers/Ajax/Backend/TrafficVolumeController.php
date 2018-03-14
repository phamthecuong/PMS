<?php

namespace App\Http\Controllers\Ajax\Backend;

use App\Models\tblSectiondataTV;
use Illuminate\Http\Request;
use Auth, App;
use App\Http\Controllers\Controller;
use Yajra\Datatables\Facades\Datatables;

class TrafficVolumeController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $records = tblSectiondataTV::filterByUser()
            ->with([
                'segment.tblBranch',
                'segment.tblOrganization.rmb'
            ])
            ->filterDropdown('segment_id', 'segment_id', $request)
            ->filterByCondition($request)
            ->filterSuperInput('km_station' , 'km_station', $request)
            ->filterSuperInput('m_station' , 'm_station', $request)->get();

        return Datatables::of($records)
            ->addColumn('extra_view', function($t) {
                return view('front-end.m13.traffic_volume.extra_view', [
                    'traffic_volume' => $t
                ])->render();
            })
            ->make(true);
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
