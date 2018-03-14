<?php

namespace App\Http\Controllers\Ajax\Backend;

use App\Models\tblBranch;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Auth, App;
use Yajra\Datatables\Facades\Datatables;

class RouteController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $lang = (App::getLocale() == 'en') ? 'en' : 'vn';
        $branch = tblBranch::select(\DB::raw("*, name_{$lang} as rcName"))
            ->with('mstRoadCategory')->get();

        return Datatables::of($branch)
//            ->addColumn('rcName', function ($b) {
//                $lang = App::getLocale();
//                $rcName;
//                if ($lang == 'en')
//                {
//                    $rcName = $b->name_en;
//                }
//                elseif ($lang == 'vi')
//                {
//                    $rcName = $b->name_vn;
//                }
//                return $rcName;
//            })
            ->addColumn('action', function ($b) {
                $result = "<div><div style='float: left;' >";
                if (Auth::user()->hasPermission('route_management.Edit'))
                {
                    $result .= "<a class='btn btn-xs btn-primary' title = 'edit' href='/admin/routes/".$b->id."/edit'><i class='fa fa-pencil' aria-hidden='true'></i></a>";
                }
                if (Auth::user()->hasPermission('route_management.Delete'))
                {
                    $result .= '</div>'.
                        '<div style="float: left; margin-left:5%">'.
                        view('admin.templates.del_btn')->with(['route' => ['routes.destroy', $b->id], 'confirm' => 'Are you sure'])->render().
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
     * @param  \Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     *
     * @param  int $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        return tblBranch::find($id);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request $request
     * @param  int $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }
}
