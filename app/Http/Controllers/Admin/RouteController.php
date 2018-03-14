<?php

namespace App\Http\Controllers\Admin;

use App\Http\Requests\BackendRequest\RouteRequest;
use App\Models\mstRoadCategory;
use App\Models\mstRoadNumberSupplement;
use App\Models\tblBranch;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;

class RouteController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return view('admin.route.index');
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $road_category = mstRoadCategory::allOptionToAjax();
//        $rms = mstRoadNumberSupplement::getData(1);
        return view('admin.route.add',
            ['road_category' => $road_category]);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response
     */
    public function store(RouteRequest $request)
    {
        try {
            $branch = new tblBranch();
            $branch->road_category = $request->r_category;
            $branch->name_en = $request->name_en;
            $branch->name_vn = $request->name_vi;
            $branch->branch_number = $request->branch_number;
            $branch->road_number = $request->road_number;
            $branch->road_number_supplement = $request->r_number_supplement;
            $branch->created_by = Auth::user()->id;
            $branch->save();
            return redirect(url('/admin/routes'));
        } catch (\Exception $e) {
            dd($e->getMessage());
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  int $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $branch = tblBranch::find($id);
//        $road_category = mstRoadCategory::getData();
//        $rms = mstRoadNumberSupplement::getData($id);
        return view('admin.route.add', [
            'branch' => $branch,
//            'road_category' => $road_category,
//            'rms'=>$rms
        ]);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request $request
     * @param  int $id
     * @return \Illuminate\Http\Response
     */
    public function update(RouteRequest $request, $id)
    {
        try {
            $branch = tblBranch::find($id);
            $branch->road_category = $request->r_category;
            $branch->name_en = $request->name_en;
            $branch->name_vn = $request->name_vi;
            $branch->branch_number = $request->branch_number;
            $branch->road_number = $request->road_number;
            $branch->road_number_supplement = $request->r_number_supplement;
            $branch->updated_by = Auth::user()->id;
            $branch->save();
            return redirect(url('/admin/routes'));
        } catch (\Exception $e) {
            dd($e);
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $cate_branch = tblBranch::find($id);
        $cate_branch->delete();
        return redirect(url('/admin/routes'));
    }
}
