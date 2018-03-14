<?php

namespace App\Http\Controllers\Ajax\Backend;

use App\Models\mstRepairMethod;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App, Auth;
use Illuminate\Support\Facades\DB;
use Yajra\Datatables\Facades\Datatables;

class RepairMethodController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $repair_method = mstRepairMethod::with('surface', 'userCreate', 'userUpdate', 'unit', 'classification', 'costs', 'repairCategory')->get();

        $dttable = Datatables::of($repair_method)
            ->addColumn('color', function ($rp) {
                return "<div style='display: inline-block;height: 22px;width: 22px;background-color: {$rp->code}'></div>";
            })
            // ->addColumn('updatedBy', function ($rp) {
            //     return @$rp->userUpdate->name;
            // })
            ->addColumn('action', function ($rm) {
                $result = [];
                if (Auth::user()->hasPermission('repair_method_management.Edit'))
                {
                    $result[] = \Form::lbButton(
                        '/admin/repair_methods/'.$rm->id.'/edit', 
                        'GET', 
                        "<i class='fa fa-pencil' aria-hidden='true'></i>", 
                        ["class" => "btn btn-xs btn-warning"]
                    )->toHtml();
                }
                if (Auth::user()->hasPermission('repair_method_management.cost'))
                {
                    $result[] = \Form::lbButton(
                        '/admin/repair_methods/'.$rm->id.'/cost', 
                        'GET', 
                        "<i class='fa fa-money' aria-hidden='true'></i>", 
                        ["class" => "btn btn-xs btn-success"]
                    )->toHtml();
                }
                if (Auth::user()->hasPermission('repair_method_management.Delete'))
                {
                    $result[] = \Form::lbButton(
                        route('repair_methods.destroy', [$rm->id]), 
                        'DELETE', 
                        "<i class='fa fa-trash-o' aria-hidden='true'></i>", 
                        [
                            "class" => "btn btn-xs btn-danger", 
                            "onclick" => "return confirm('" . trans('back_end.are_you_sure') . "')"
                        ]
                    )->toHtml();
                }
                return implode(' ', $result);
            });
        $organizations = \App\Models\tblOrganization::listRMBByUserRole();
        foreach ($organizations as $rec) 
        {
            $dttable = $dttable->addColumn("cost_" . $rec->code_id, function ($rp) use($rec) {
                $value = 0;
                foreach ($rp->costs as $c) 
                {
                    if ($c->organization_id == $rec->id)
                    {
                        $value = $c->cost;
                        break;
                    }
                }
                return number_format($value);
            });
        }

        $dttable = $dttable->make(true);
        return $dttable;
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
