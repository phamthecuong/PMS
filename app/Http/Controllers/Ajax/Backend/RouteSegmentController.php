<?php

namespace App\Http\Controllers\Ajax\Backend;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Auth, App;
use Yajra\Datatables\Facades\Datatables;
use App\Models\tblSegment;
use App\Models\tblOrganization;

class RouteSegmentController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index($route_id, Request $request)
    {
        $records =  new tblSegment();
        if ($request->sb_id == -1)
        {
            $rmb_id = @$_GET['rmb_id'];
            if (Auth::user()->hasRole('userlv1') || Auth::user()->hasRole('userlvl1p'))
            {
                $records = $records->whereHas('tblOrganization', function($query) use($rmb_id) {
                    $query->where('level', 3)->where('parent_id', $rmb_id);
                });
            }
            else if(Auth::user()->hasRole('userlv2'))
            {
                
                $records = $records->whereHas('tblOrganization', function($query) use($rmb_id) {
                    $query->where('level', 3)->where('parent_id', Auth::user()->organization_id);
                });
            }
            else
            {
                $records = $records->whereHas('tblOrganization', function($query) use($rmb_id) {
                    $query->where('level', 3)->where('id', Auth::user()->organization_id);
                });
            }
        }
        else
        {
            $records = $records->where('SB_id', $request->sb_id);
        }
        $records = $records->whereBranchId($route_id)
            ->orderBy('km_from')
            ->orderBy('m_from')
            ->orderBy('km_to')
            ->orderBy('m_to')
            ->get();
        return $records;
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
     * @param  \Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     *
     * @param  int $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request $request
     * @param  int $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }
}
