<?php

namespace App\Http\Controllers\Admin;

use App\Http\Requests\BackendRequest\RoadClassRequest;
use App\Models\mstRoadClass;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Yajra\Datatables\Facades\Datatables;
use App\Models\tblSectiondataRMD;
use App\Models\tblRMDHistory;
use App\Models\tblRepairMatrixCellValue;
use Auth;
class RoadClassController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return view('admin.road_class.index');
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return view('admin.road_class.add');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(RoadClassRequest $request)
    {
        try {
            $road_class = new mstRoadClass();
            $road_class->name_en = $request->name_en;
            $road_class->name_vn = $request->name_vi;
            $road_class->code_id = $request->code_id;
            $road_class->created_by = Auth::user()->id;
            $road_class->save();
            return redirect(url('/admin/road_class'))->with('change', trans('back_end.create_success'));
        }catch (\Exception $e)
        {
            dd($e);
        }
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
        $road_class = mstRoadClass::find($id);
        return view('admin.road_class.add', ['road_class'=>$road_class]);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(RoadClassRequest $request, $id)
    {   

        try
        {
            $road_class = mstRoadClass::find($id);
            $road_class->name_en = $request->name_en;
            $road_class->name_vn = $request->name_vi;
            $road_class->code_id = $request->code_id;
            $road_class->updated_by = Auth::user()->id;
            $road_class->save();
            return redirect(url('/admin/road_class'))->with('update', trans('back_end.create_success'));
        }catch (\Exception $e)
        {
            dd($e);
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        try
        {
            $road_class = mstRoadClass::find($id);
            $code_id = $road_class->code_id;
            $repair = tblRepairMatrixCellValue::where('parameter_id', 'road_class')
                                            ->where('value', $code_id)->get();
            $RMD = tblRMDHistory::where('road_class_id', $id)->get();
            if (count($repair) > 0 || count($RMD) > 0)
            {
                return redirect('/admin/road_class')->with('delete', trans('back_end.not_delete_becase_have_data'));
            }
            else
            {
                $road_class->delete();
                return redirect('/admin/road_class')->with('change', trans('back_end.delete_success'));
            }
            
        }catch (\Exception $e)
        {
            dd($e);
        }
    }
}
