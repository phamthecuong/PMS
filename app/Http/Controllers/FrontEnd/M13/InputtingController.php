<?php

namespace App\Http\Controllers\FrontEnd\M13;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\mstRoadClass;
use App\Models\tblTerrainType;
use App\Models\mstRepairMethod;
use App\Models\tblRCategory;
use App\Models\tblRStructtype;
use App\Models\mstSurface;

class InputtingController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        
        $road_class_id = mstRoadClass::pluck('id')->first();
        $terrian_type_id = tblTerrainType::pluck('id')->first();
        $repair_method = mstRepairMethod::first();
        $r_struct_type_id = tblRStructtype::pluck('id')->first();
        $surface = mstSurface::first();
        $r_category_id = $surface->repair_categories->pluck('id')->first();
        $r_classification_id = $repair_method->classification_id;
        $default_data = [
            'road_class_id' => $road_class_id,
            'terrian_type_id' => $terrian_type_id,
            'repair_method_id' => $repair_method->id,
            'r_category_id' => $r_category_id,
            'r_struct_type_id' => $r_struct_type_id,
            'r_classification_id' => $r_classification_id,
            'surface_id' => $surface->id,
        ];
        return view('front-end.m13.inputting_system.index', [
            'default_data' => $default_data
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

    public function test()
    {
        
    }
}
