<?php

namespace App\Http\Controllers\Ajax\PMSDataset;

use Illuminate\Http\Request;
use App\Http\Requests;
use Auth;
use Yajra\Datatables\Datatables;
use App\Models\tblPMSDataset;
use App\Http\Controllers\Controller;

class IndexController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $data = tblPMSDataset::orderBy('year', 'desc')
            ->get();
        
        return Datatables::of($data)
            ->addColumn('progress', function($d) {
                $percent = ($d->total_segment == 0) ? 0 : round(100 * $d->completed_segment/$d->total_segment);
                return '<div class="progress progress-xs" data-progressbar-value="' . $percent . '"><div class="progress-bar"></div></div>';
            })
            ->addColumn('action', function($d) {
                $actions = [];
                if($d->completed_segment == $d->total_segment)
                {
                    $actions[] = \Form::lbButton(url('user/pms_dataset/report/'. $d->year), 'GET', trans('pms_dataset.data_report'), ["class" => "btn btn-xs btn-warning"])->toHtml();   
                }
                return implode(' ', $actions);
            })
            ->make(true);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create(Request $request)
    {
        
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
