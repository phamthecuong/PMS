<?php

namespace App\Http\Controllers\Ajax\Backend;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Auth, App;
use Yajra\Datatables\Facades\Datatables;
use App\Models\tblBranch;
use App\Models\tblOrganization;
use DB;

class SbRouteController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index( $sb_id, $flash = true)
    {   
        $records = tblBranch::orderBy('branch_number');
        $rmb_id = @$_GET['rmb_id'];
        if (isset($_GET['flash']))
        {
            // if ($sb_id == -1)
            // {
                $sb_id = tblOrganization::where('parent_id', $rmb_id)->pluck('id')->toArray();
                $branch_id = DB::table('tblSection_PC')->whereIn('SB_id', $sb_id)->where('branch_id', '!=', 0)                   ->groupBy('branch_id')
                                           ->pluck('branch_id')
                                           ->toArray();
                $records = $records->whereIn('id', $branch_id);
            // }
            // else
            // {
                // $branch_id = DB::table('tblSection_PC')->where('SB_id', $sb_id)->where('branch_id', '!=', 0)                    ->groupBy('branch_id')
                //                         ->pluck('branch_id')
                //                         ->toArray();
                // $records = $records->whereIn('id', $branch_id);
            //}
             $records = $records->get();
             return $records;
        }
        else
        {
            if ($sb_id == -1)
            {
                if (isset($rmb_id) && $rmb_id != -1)
                {
                    if (Auth::user()->hasRole('userlv1')||  Auth::user()->hasRole('userlvl1p'))
                    {
                        $records = $records->whereHas('segments.tblOrganization', function($query) use($rmb_id) {
                            $query->where('level', 3)->where('parent_id', $rmb_id);
                        });
                    }
                    else if(Auth::user()->hasRole('userlv2'))
                    {
                        $records = $records->whereHas('segments.tblOrganization', function($query) use($rmb_id) {
                            $query->where('level', 3)->where('parent_id', Auth::user()->organization_id);
                        });
                    }
                    else
                    {
                        $records = $records->whereHas('segments.tblOrganization', function($query) use($rmb_id) {
                            $query->where('level', 3)->where('id', Auth::user()->organization_id);
                        });
                    }
                }

            }
            else
            {
                $records = $records->whereHas('segments', function($query) use($sb_id) {
                    $query->where('SB_id', $sb_id);
                });
            }
        }

        $records = $records->get();
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
