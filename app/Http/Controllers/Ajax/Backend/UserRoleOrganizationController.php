<?php

namespace App\Http\Controllers\Ajax\Backend;

use App\Models\Role;
use App\Models\tblOrganization;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class UserRoleOrganizationController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index($role_id)
    {
        $roles = Role::where('id', $role_id)->first();
        $data = $roles->code;
        if ($data == 'userlv1')
        {
            $record = tblOrganization::where('level', 1)->get();
        }
        elseif ($data == 'userlvl1p')
        {
            $record = tblOrganization::where('level', 1)->get();
        }
        elseif ($data == 'userlv2')
        {
            $record = tblOrganization::where('level', 2)->get();
        }
        else
        {
            $record = tblOrganization::where('level', 3)->get();
        }

        return $record;
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
