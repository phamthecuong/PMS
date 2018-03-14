<?php

namespace App\Http\Controllers\Admin;

use App\Http\Requests\BackendRequest\RepairMethodRequest;
use App\Models\mstSurface;
use App\Models\mstRepairMethod;
use App\Models\tblRepairMethodCost;
use App\Models\tblOrganization;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use App\Http\Requests\BackendRequest\RepairMethodCostRequest;

class RepairMethodController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return view('admin.repair_method.index');
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $pavement = mstSurface::getData();
        return view('admin.repair_method.add', ['pavement' => $pavement]);
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
            $repair_method->code = $request->code;
            $repair_method->unit_id = $request->unit;
            $repair_method->classification_id = $request->classification;
            $repair_method->zone_id = $request->zone_id;
            $repair_method->created_by = Auth::user()->id;
            $repair_method->save();
            return redirect(url('/admin/repair_methods'))->with('change', trans('back_end.create_success'));
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
        $repair_method = mstRepairMethod::find($id);
        $pavement = mstSurface::getData();
        return view('admin.repair_method.add',['repair_method' => $repair_method,'pavement' => $pavement]);
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
            $repair_method->code = $request->code;
            $repair_method->unit_id = $request->unit;
            // $repair_method->classification_id = $request->classification;
            // $repair_method->zone_id = $request->zone_id;
            $repair_method->updated_by = Auth::user()->id;
            $repair_method->save();
            return redirect(url('/admin/repair_methods'))->with('update', trans('back_end.update_success'));
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
            return redirect(url('/admin/repair_methods'))->with('change', trans('back_end.delete_success'));
        }
        catch (\Exception $e)
        {
            dd($e);
        }
    }

    function getCostSetting($id)
    {
        try
        {
            $user = \Auth::user();
            $method = mstRepairMethod::where('id', $id)->with('costs')->first();

            $organizations = tblOrganization::listRMBByUserRole();
            
            return view('admin.repair_method.cost', ['method' => $method, 'orgs' => $organizations]);
        }
        catch (\Exception $e)
        {
            dd($e->getMessage());
        }
    }

    function postCostSetting($id, RepairMethodCostRequest $request)
    {
        \DB::beginTransaction();
        try
        {
            $costs = $request->cost;
            $method = mstRepairMethod::findOrFail($id);

            // tblRepairMethodCost::where('repair_method_id', $id)->delete();
            foreach ($costs as $org_id => $cost) 
            {
                $rec = tblRepairMethodCost::where('repair_method_id', $id)
                    ->where('organization_id', $org_id)
                    ->first();
                if (!$rec)
                {
                    $rec = new tblRepairMethodCost();    
                    $rec->created_by = \Auth::user()->id;
                }
                else
                {
                    $rec->updated_by = \Auth::user()->id;   
                }
                
                $rec->repair_method_id = $id;
                $rec->organization_id = $org_id;   
                $rec->cost = $cost;
                
                $rec->save();
            }

            \DB::commit();   
            return back();
        }
        catch (\Exception $e)
        {
            \DB::rollBack();
            dd($e->getMessage());
        }
    }
}
