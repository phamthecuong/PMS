<?php

namespace App\Http\Controllers\Ajax\Frontend;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\mstPavementType;
use App\Models\mstSurface;

class MaterialTypeSurfaceController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    // public function index($material_type_id)
    // {
    //     $surface_id = $this->convertPavementType($material_type_id);
    //     $repair_categories = mstSurface::findOrFail($surface_id)->repair_categories;
    //     return response([
    //         'surface_id' => $surface_id,
    //         'repair_categories' => $repair_categories
    //     ]);
    // }
    public function index($material_type_id)
    {
        $surface_id = $this->convertSurface($material_type_id);
        $repair_categories = mstSurface::findOrFail($surface_id)->repair_categories;
        return response([
            'surface_id' => (string)$surface_id,
            'repair_categories' => $repair_categories
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
    function convertSurface($id)
    {
        $pavement_type = mstPavementType::findOrFail($id)->surface_id;
        $surface = mstSurface::where('id', $pavement_type)->first();
        return $surface->id;
    }
    function convertPavementType($id)
    {
        $rule = [
            'AC' => 'AC',
            'CC' => 'CC',
            'BST' => 'BST',
            'BPM' => 'BST',
            'BM' => 'BST',
            'WBM' => 'UP',
            'GP' => 'UP',
            'SSP' => 'UP',
            'EP' => 'UP',
            'RP' => '*',
            'Others' => '*',
        ];
        $pavement_type = mstPavementType::findOrFail($id)->code;
        $surface_code = $rule[$pavement_type];
        $surface = mstSurface::where('code_name', $surface_code)->first();
        return (string) $surface->id;
    }
}
