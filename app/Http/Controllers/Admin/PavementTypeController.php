<?php

namespace App\Http\Controllers\Admin;

use App\Http\Requests\BackendRequest\PavementTypeRequest;
use App\Models\mstPavementType;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use App\Models\tblSectionLayer;

class PavementTypeController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return view('admin.pavement_type.index');
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
//        $pavement_layer = mstPavementLayer::getData();
        return view('admin.pavement_type.add'/*,['pavement_layer' => $pavement_layer]*/);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(PavementTypeRequest $request)
    {
        try
        {
            $pavement_type = new mstPavementType();
            $pavement_type->name_en = $request->name_en;
            $pavement_type->name_vn = $request->name_vn;
            $pavement_type->code = $request->code;
            $pavement_type->pavement_layer_id = $request->pavement_layer;
            if ($request->surface_id != 0) {
                $pavement_type->surface_id = $request->surface_id;
            }
            $pavement_type->created_by = Auth::user()->id;
            $pavement_type->save();
            return redirect()->route('pavement_types.index')->with('change', trans('back_end.add_new_success'));
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
        $pavement_type = mstPavementType::find($id);
//        $pavement_layer = mstPavementLayer::getData();
        return view('admin.pavement_type.add',['pavement_type' => $pavement_type/*, 'pavement_layer' => $pavement_layer*/]);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(PavementTypeRequest $request, $id)
    {
        try
        {
            $pavement_type = mstPavementType::find($id);
            $pavement_type->name_en = $request->name_en;
            $pavement_type->name_vn = $request->name_vn;
            $pavement_type->code = $request->code;
            $pavement_type->pavement_layer_id = $request->pavement_layer;
            $pavement_type->updated_by = Auth::user()->id;
            $pavement_type->save();
            return redirect()->route('pavement_types.index')->with('update', trans('back_end.add_new_success'));
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
        $pavement_type = mstPavementType::find($id);
        if (count(tblSectionLayer::where('material_type_id', $id)->get()) > 0) 
        {
            return redirect(url('/admin/pavement_types'))->with('delete', trans('back_end.not_delete_because_have_data'));
        }
        else
        {
            $pavement_type->delete();    
            return redirect(url('/admin/pavement_types'))->with('change', trans('back_end.delete_success'));
        }
        
    }
}
