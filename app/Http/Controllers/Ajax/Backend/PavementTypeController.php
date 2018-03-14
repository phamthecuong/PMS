<?php

namespace App\Http\Controllers\Ajax\Backend;

use App\Models\mstPavementType;
use App\Models\mstSurface;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Yajra\Datatables\Facades\Datatables;

class PavementTypeController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $pavement_type = mstPavementType::with('mstPavementLayer', 'userCreate', 'userUpdate')->get();

        
        return Datatables::of($pavement_type)
            
            ->addColumn('referred', function ($pt) {
                if ($pt->surface_id != null) 
                {
                    $result = mstSurface::findOrFail($pt->surface_id)->code_name;
                }
                else
                {
                    $result = '';
                }
                return $result;
            })
            ->addColumn('action', function ($pt) {
                $result = "<div><div style='float: left;'>";
                if (Auth::user()->hasPermission('pavement_type_management.Edit'))
                {
                    $result .= "<a class='btn btn-xs btn-primary' href='/admin/pavement_types/".$pt->id."/edit'><i class='fa fa-pencil' aria-hidden='true'></i></a>";
                }
                if (Auth::user()->hasPermission('pavement_type_management.Delete'))
                {   
                    $confirm = trans('back_end.are_you_sure');
                    $result .= '</div>'.
                        '<div style="float: left; margin-left:5%">'.
                        view('admin.templates.del_btn')->with(['route' => ['pavement_types.destroy', $pt->id], 'title' => 'Delete', 'confirm' => $confirm])->render().
                        '</div>'.
                        '</div>';
                }
                return $result;
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
