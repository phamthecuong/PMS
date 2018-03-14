<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\mstIrregularKp;
use Auth, DB;
use App\Http\Requests\BackendRequest\IrregularKpRequest;

class IrregularKpController extends Controller
{
    function __construct()
    {
        $this->middleware("dppermission:irregular_kp.all");
    }
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return view('admin.irregular_kp.index');
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return view('admin.irregular_kp.add');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(IrregularKpRequest $request)
    {
        DB::beginTransaction();
        try
        {
            $irregular_kp = new mstIrregularKp;
            $irregular_kp->fill($request->all());
            $irregular_kp->created_by = Auth::user()->id;
            $irregular_kp->save();
            DB::commit();
            return redirect(url('/admin/irregular_kp'))->with([
                'flash_level' => 'success',
                'flash_message' => trans('back_end.create_success')
            ]);       
        }
        catch (\Exception $e)
        {
            DB::rollBack();
            dd($e->getMessage());
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
        $irregular_kp = mstIrregularKp::findOrFail($id);
        return view('admin.irregular_kp.add', ['irregular_kp' => $irregular_kp]);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(IrregularKpRequest $request, $id)
    {
        DB::beginTransaction();
        try
        {
            $irregular_kp = mstIrregularKp::findOrFail($id);
            $irregular_kp->fill($request->all());
            $irregular_kp->updated_by = Auth::user()->id;
            $irregular_kp->save();
            DB::commit();
            return redirect(url('/admin/irregular_kp'))->with([
                'flash_level' => 'success',
                'flash_message' => trans('back_end.update_success')
            ]);       
        }
        catch (\Exception $e)
        {
            DB::rollBack();
            dd($e->getMessage());
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
        DB::beginTransaction();
        try
        {
            $irregular_kp = mstIrregularKp::findOrFail($id);
            $irregular_kp->delete();
            DB::commit();
            return redirect(url('/admin/irregular_kp'))->with([
                'flash_level' => 'success',
                'flash_message' => trans('back_end.delete_success')
            ]);       
        }
        catch (\Exception $e)
        {
            DB::rollBack();
            dd($e->getMessage());
        }
    }
}
