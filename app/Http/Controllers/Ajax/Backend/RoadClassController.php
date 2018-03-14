<?php

namespace App\Http\Controllers\Ajax\Backend;

use App\Models\mstRoadClass;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Yajra\Datatables\Facades\Datatables;

class RoadClassController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $lang = (App::getLocale() == 'en') ? 'en' : 'vn';
        $road_class = mstRoadClass::select(DB::raw("*, name_{$lang} as name"))
            ->with('userCreate', 'userUpdate')
            ->get();
        return Datatables::of($road_class)
           
            /*->addcolumn('name', function ($rc) {
                $name;
                $lang = App::getLocale();
                if ($lang == 'en')
                {
                    $name = $rc->name_en;
                }
                if ($lang == 'vi')
                {
                    $name =$rc->name_vn;
                }
                return $name;
            })*/
            ->addcolumn('action', function ($rc) {
                $result = "<div><div style='float: left;'>";
                if (Auth::user()->hasPermission('road_class_management.Edit'))
                {
                    $result .= "<a class='btn btn-xs btn-primary' href='/admin/road_class/" . $rc->id . "/edit'><i class='fa fa-pencil' aria-hidden='true'></i></a>";
                }
                if (Auth::user()->hasPermission('road_class_management.Delete'))
                {
                    $confirm = trans('back_end.are_you_sure');
                    $result .= '</div>' .
                        '<div style="float: left; margin-left:5%">' . view('admin.templates.del_btn')->with(['route' => ['admin.road_class.destroy', $rc->id], 'title' => 'Delete', 'confirm' => $confirm])->render() .
                        '</div>' .
                        '</div>';
                }
                return $result;
        })->make(true);

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
       
    }
}
