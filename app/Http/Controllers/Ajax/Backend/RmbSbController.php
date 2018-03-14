<?php

namespace App\Http\Controllers\Ajax\Backend;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Auth, App;
use Yajra\Datatables\Facades\Datatables;
use App\Models\tblOrganization;

class RmbSbController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index($rmb_id)
    {
        $records = tblOrganization::orderBy('code_id');
        if ($rmb_id == -1)
        {
            $records = $records->whereLevel(3)->whereNotNull('parent_id');
        }
        elseif (Auth::user()->hasRole('userlv3'))
        {
            $records = tblOrganization::where('id', Auth::user()->organization_id);
        }
        else
        {
            $records = $records->whereParentId($rmb_id);
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
