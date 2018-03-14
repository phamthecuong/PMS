<?php

namespace App\Http\Controllers\Ajax\Frontend;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

use App\Models\tblCity;
use App\Models\tblDistrict;
use App\Models\tblWard;

class WardInfoPDController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index($ward_id)
    {
        $ward = tblWard::findOrFail($ward_id);
        $district = $ward->district()->first();
        $list_ward = tblWard::where('district_id', $district->id)->get();
        $province = $district->province()->first();
        $list_district = tblDistrict::where('province_id', $province->id)->get();
        // dd($province);
        return response([
            'w_name' => $ward->name,
            'd_id' => $district->id,
            'd_name' => $district->name,
            'p_id' => (string) $province->id,
            'p_name' => $province->name,
            'list_ward' => $list_ward,
            'list_district' => $list_district
        ]);
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
