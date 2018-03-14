<?php

namespace App\Http\Controllers\Ajax\Backend;

use App\Models\tblRepairMatrix;
use App\Models\tblRepairMatrixCell;
use App\Models\tblRepairMatrixCellValue;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App, Auth;
use Illuminate\Support\Facades\DB;
use Yajra\Datatables\Facades\Datatables;
use Carbon\Carbon;

class RepairMatrixController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $rec = tblRepairMatrix::with('userUpdate')->get();
        return Datatables::of($rec)
            ->editColumn('name', function ($rp) {
                return trans('back_end.matrix_' . $rp->name);
            })
            ->editColumn('type', function ($rp) {
                return ($rp->type == 1) ? trans('back_end.budget') : trans('back_end.work_planning');
            })
            ->addColumn('updatedBy', function ($rp) {
                return @$rp->userUpdate->name;
            })
            ->addColumn('action', function ($rm) {
                $result = [];
                
                $result[] = \Form::lbButton(
                    '/admin/repair_matrix/'.$rm->id.'/edit', 
                    'GET', 
                    "<i class='fa fa-delicious' aria-hidden='true'></i>", 
                    ["class" => "btn btn-xs btn-warning"]
                )->toHtml();
                
                return implode(' ', $result);
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
        \DB::beginTransaction();
        try
        {
            $repair_matrix = tblRepairMatrix::find($request->repair_matrix_id);
            $repair_matrix->updated_by = \Auth::user()->id;
            $repair_matrix->updated_at = Carbon::now();
            $repair_matrix->save();

            tblRepairMatrixCell::where('repair_matrix_id', $repair_matrix->id)->delete();

            foreach ($request->matrix as $road_type => $road_classes) 
            {
                foreach ((array)$road_classes as $road_class => $pavement_types) 
                {
                    foreach ((array)$pavement_types as $pavement_type => $cracks) 
                    {
                        foreach ((array)$cracks as $cindex => $ruts) 
                        {
                            foreach ((array)$ruts as $rindex => $repair_method) 
                            {
                                if ($repair_method != 0)
                                {
                                    $rec = new tblRepairMatrixCell;
                                    $rec->repair_matrix_id = $repair_matrix->id;
                                    $rec->user_id = null;
                                    $rec->target_type = $repair_matrix->type;
                                    $rec->created_by = \Auth::user()->id;
                                    $rec->row = $cindex;
                                    $rec->column = $rindex;
                                    $rec->save();
        
                                    $rec->saveRelation($repair_method, $road_type, $road_class, $pavement_type, $cindex, $rindex);
                                }
                            }
                        }
                    }
                }
            }
            \DB::commit();
            return ['code' => 200];
        }
        catch (\Exception $e)
        {
            \DB::rollBack();
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
