<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use App\Models\tblRepairMatrix;
use App\Models\tblRepairMatrixCell;

class RepairMatrixController extends Controller
{
    function __construct()
    {
        $this->middleware("dppermission:repair_matrix.all");
    }
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return view('admin.repair_matrix.index');
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return view('admin.repair_matrix.edit');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(RepairMethodRequest $request)
    {
        try 
        {
            $repair_method = new mstRepairMethod();
            $repair_method->pavement_type = $request->pavement_type;
            $repair_method->name_en = $request->name_en;
            $repair_method->name_vn = $request->name_vi;
            // $repair_method->cost = $request->cost;
            $repair_method->created_by = Auth::user()->id;
            $repair_method->save();
            return redirect(url('/admin/repair_methods'));
        }
        catch (\Exception $e)
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
        try
        {
            $repair_matrix = tblRepairMatrix::find($id);
            $crack_ranks = \App\Models\tblConditionRank::where('target_type', 1)->orderBy('rank')->get();
            $rut_ranks = \App\Models\tblConditionRank::where('target_type', 2)->orderBy('rank')->get();
            $road_category = \App\Models\mstRoadCategory::whereIn('code_id', [0, 1])->get();
            $road_class = \App\Models\mstRoadClass::whereIn('code_id', [1, 2, 3, 4, 5, 6])->get();
            $pavement_type = \App\Models\mstSurface::whereIn('code_id', [1, 2, 3])->get();
            if (in_array($repair_matrix->id, [1, 2]))
            {
                $repair_methods = $this->_getAllRepairMethods();
                $matrix = $this->_getSavedMatrix($crack_ranks->count(), $rut_ranks->count(), $repair_matrix);
                $saved_zone = $this->_getRestrictZone($repair_matrix);

                return view('admin.repair_matrix.edit', [
                    'repair_matrix_id' => $repair_matrix->id,
                    'repair_matrix_name' => trans('back_end.matrix_' . $repair_matrix->name),
                    'crack_ranks' => $crack_ranks,
                    'rut_ranks' => $rut_ranks,
                    'road_category' => $road_category,
                    'road_class' => $road_class,
                    'pavement_type' => $pavement_type,
                    'zones' => $repair_methods,
                    'matrix' => $matrix,
                    'saved_zone' => $saved_zone

                ]);
            }
            else
            {
                $zones = $this->_getAllZones();
                $matrix = $this->_getSavedMatrix($crack_ranks->count(), $rut_ranks->count(), $repair_matrix);
                return view('admin.repair_matrix.edit', [
                    'repair_matrix_id' => $repair_matrix->id,
                    'repair_matrix_name' => trans('back_end.matrix_' . $repair_matrix->name),
                    'crack_ranks' => $crack_ranks,
                    'rut_ranks' => $rut_ranks,
                    'road_category' => $road_category,
                    'road_class' => $road_class,
                    'pavement_type' => $pavement_type,
                    'zones' => $zones,
                    'matrix' => $matrix
                ]);
            }
        }
        catch (\Exception $e)
        {
            dd($e);
        }
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(RepairMethodRequest $request, $id)
    {
        try
        {
            $repair_method = mstRepairMethod::find($id);
            $repair_method->pavement_type = $request->pavement_type;
            $repair_method->name_en = $request->name_en;
            $repair_method->name_vn = $request->name_vi;
            // $repair_method->cost = $request->cost;
            $repair_method->updated_by = Auth::user()->id;
            $repair_method->save();
            return redirect(url('/admin/repair_methods'));
        }
        catch (\Exception $e)
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
            $repair_method = mstRepairMethod::find($id);
            $repair_method->delete();
            return redirect(url('/admin/repair_methods'));
        }
        catch (\Exception $e)
        {
            dd($e);
        }
    }

    private function _getAllZones()
    {
        $lang = \App::isLocale('en') ? 'en' : 'vn';
        $records = \App\Models\mstRepairMethod::groupBy('zone_id')
            ->select('zone_id', 'pavement_type', 'code', \DB::raw("group_concat(name_{$lang} separator ' + ') as mdname"))
            ->orderBy('zone_id')
            ->get();
        $data = [];
        foreach ($records as $index => $r) 
        {
            $data[] = [
                'id' => $r->zone_id,
                'color' => $r->code,
                'name' => $r->mdname,
                'pavement_type' => $r->pavement_type
            ];
        }
        return $data;
    }

    private function _getAllRepairMethods()
    {
        $lang = \App::isLocale('en') ? 'en' : 'vn';
        $records = \App\Models\mstRepairMethod::select('id', 'zone_id', 'pavement_type', 'code', \DB::raw("name_{$lang} as mdname"))
            ->orderBy('zone_id')
            ->get();
        $data = [];
        foreach ($records as $index => $r) 
        {
            $data[] = [
                'id' => $r->id,
                'color' => $r->code,
                'name' => $r->mdname,
                'pavement_type' => $r->pavement_type,
                'zone_id' => $r->zone_id
            ];
        }
        return $data;
    }

    private function _getSavedMatrix($crack_rank, $rut_rank, $repair_matrix)
    {
        $structure = [
            0 => [
                1, 2, 3, 4
            ],
            1 => [
                1, 2, 3, 4, 5, 6
            ]
        ];
        $dataset = [];
        foreach ($structure as $road_type => $road_classes) 
        {
            foreach ($road_classes as $road_class) 
            {
                $dataset[$road_type][$road_class][1] = array_fill(0, $crack_rank, array_fill(0, $rut_rank, 0));
                $dataset[$road_type][$road_class][2] = array_fill(0, $crack_rank, array_fill(0, $rut_rank, 0));
                $dataset[$road_type][$road_class][3] = array_fill(0, $crack_rank, array_fill(0, 1, 0));
            }
        }

        $info_matrix = tblRepairMatrixCell::where('repair_matrix_id', $repair_matrix->id)
            ->whereNull('user_id')
            ->with([
                'crackValue',
                'rutValue',
                'repairMethodValue',
                'roadTypeValue',
                'roadClassValue',
                'surfaceValue'
            ])
            ->get();

        foreach ($info_matrix as $i) 
        {
            $dataset[$i->roadTypeValue->value][$i->roadClassValue->value][$i->surfaceValue->value][$i->row][$i->column] = $i->repairMethodValue->value;
        }
        return $dataset;
    }

    private function _getRestrictZone($repair_matrix)
    {
        $zone_repair_matrix = tblRepairMatrix::where('type', $repair_matrix->type)
            ->where('id', '<>', $repair_matrix->id)
            ->first();

        $dataset = [];
        $info_matrix = tblRepairMatrixCell::where('repair_matrix_id', $zone_repair_matrix->id)
            ->whereNull('user_id')
            ->with([
                'crackValue',
                'rutValue',
                'repairMethodValue',
                'roadTypeValue',
                'roadClassValue',
                'surfaceValue'
            ])
            ->get();

        foreach ($info_matrix as $i) 
        {
            $dataset[$i->roadTypeValue->value][$i->roadClassValue->value][$i->surfaceValue->value][$i->row][$i->column] = $i->repairMethodValue->value;
        }
        return $dataset;

    }
}
