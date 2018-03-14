<?php

namespace App\Http\Controllers\Ajax\Backend;

use App\Models\tblSectiondataMH;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Auth, App;
use Yajra\Datatables\Facades\Datatables;

class MaintenanceHistoryController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $records = tblSectiondataMH::filterByUser()
            ->with([
                'segment.tblBranch',
                'segment.tblOrganization.rmb'
            ])
            ->filterDropdown('segment_id', 'segment_id', $request)
            ->filterDropdown('direction', 'direction', $request)
            ->filterByCondition($request)
            ->filterSuperInput('km_from' , 'km_from', $request)
            ->filterSuperInput('km_to' , 'km_to', $request)
            ->filterSuperInput('m_from' , 'm_from', $request)
            ->filterSuperInput('m_to' , 'm_to', $request)
            ->filterSuperInput('lane_pos_number' , 'lane_pos_number', $request)
            ->get();


        return Datatables::of($records)
            ->editColumn('direction', function($r) {
                switch ($r->direction) 
                {
                    case 1:
                        return trans('back_end.left_direction');
                    case 2:
                        return trans('back_end.right_direction');
                    case 3:
                        return trans('back_end.single_direction');
                    default:
                        return '';
                }
            })
            ->addColumn('extra_view', function($r) {
                return view('front-end.m13.maintenance_history.extra_view', [
                        'h' => $r
                    ]);
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

    public function review(Request $request)
    {
        $chainage = $request->chainage;
        $time_data = $request->time_data;
        if (@$request->width_min != "") $width_min = $request->width_min;
        if (@$request->width_max != "") $width_max = $request->width_max;
        if (@$request->distance_min != "") $distance_min = $request->distance_min;
        if (@$request->distance_max != "") $distance_max = $request->distance_max;
        $errors = [];

        if (!$chainage['three'] && !$time_data['one'] && !$time_data['two'] && !isset($width_min) && !isset($width_max) && !isset($distance_min) && !isset($distance_max))
        {
            $mh = [];
        }
        else
        {
            $mh = tblSectiondataMH::filterByUser()->with('segment.tblBranch', 'segment.tblOrganization.rmb', 'segment.tblCity_from', 'segment.tblCity_to', 'repairMethod', 'repairStructType', 'repairClassification', 'repairCategory')
                ->filterDropdown('segment_id', 'segment_id', $request)
                ->filterDropdown('direction', 'direction', $request)
                ->filterDropdownByProvince('prfrom_id', 'prfrom_id', $request)
                ->filterDropdownByProvince('prto_id', 'prto_id', $request)
                ->filterByCondition($request)
                ->filterSuperInput('km_from' , 'km_from', $request)
                ->filterSuperInput('km_to' , 'km_to', $request)
                ->filterSuperInput('m_from' , 'm_from', $request)
                ->filterSuperInput('m_to' , 'm_to', $request)
                ->filterSuperInput('lane_pos_number' , 'lane_pos_number', $request)
                ->filterSuperInput('total_width_repair_lane' , 'total_width_repair_lane', $request)
                ->get()->toArray();
        }

        foreach ($mh as $record) 
        {
            $err_name = [];
            $err_id = [];

            //Check actual length in chainage
            if ($chainage['three'] == '1')
            {
                if ($record['actual_length'] < 0)
                {
                    $err_name[] = trans('review_tool.invalid_actual_length');
                    $err_id[] = 1;
                }
            }

            //Check completion date
            if ($time_data['one'] == '1')
            {
                if (empty($record['completion_date']))
                {
                    $err_name[] = trans('review_tool.invalid_completion_date');
                    $err_id[] = 2;
                }
            }

            //Check repair duration
            if ($time_data['two'] == '1')
            {
                if ($record['repair_duration'] <= 0 || $record['repair_duration'] === '')
                {
                    $err_name[] = trans('review_tool.invalid_repair_duration');
                    $err_id[] = 3;
                }
            }

            //Check total width repair lane
            if (isset($width_min) && !isset($width_max))
            {
                if ($record['total_width_repair_lane'] < $width_min)
                {
                    $err_name[] = trans('review_tool.invalid_total_width_repair_lane');
                    $err_id[] = 4;
                }
            }
            elseif (!isset($width_min) && isset($width_max)) 
            {
                if ($record['total_width_repair_lane'] > $width_max)
                {
                    $err_name[] = trans('review_tool.invalid_total_width_repair_lane');
                    $err_id[] = 4;
                }
            }
            elseif (isset($width_min) && isset($width_max))
            {
                if ($record['total_width_repair_lane'] < $width_min || $record['total_width_repair_lane'] > $width_max)
                {
                    $err_name[] = trans('review_tool.invalid_total_width_repair_lane');
                    $err_id[] = 4;
                }
            }

            //Check distance
            if (isset($distance_min) && !isset($distance_max))
            {
                if ($record['distance'] < $distance_min)
                {
                    $err_name[] = trans('review_tool.invalid_distance');
                    $err_id[] = 5;
                }
            }
            elseif (!isset($distance_min) && isset($distance_max)) 
            {
                if ($record['distance'] > $distance_max)
                {
                    $err_name[] = trans('review_tool.invalid_distance');
                    $err_id[] = 5;
                }
            }
            elseif (isset($distance_min) && isset($distance_max))
            {
                if ($record['distance'] < $distance_min || $record['distance'] > $distance_max)
                {
                    $err_name[] = trans('review_tool.invalid_distance');
                    $err_id[] = 5;
                }
            }

            //Add error
            if (count($err_id) > 0)
            {
                $record['err_name'] = implode(', ', $err_name);
                $record['err_id'] = $err_id;
                $errors[] = $record;
            }
        }

        

        return Datatables::of(collect($errors))
            ->editColumn('direction', function($r) {
                switch ($r['direction'])
                {
                    case 1:
                        return trans('back_end.left_direction');
                    case 2:
                        return trans('back_end.right_direction');
                    case 3:
                        return trans('back_end.single_direction');
                    default:
                        return '';
                }
            })
            ->addColumn('extra_view', function($r) {
                return view('front-end.m13.maintenance_history.extra_view_review', [
                    'mh' => $r,
                    'err_id' => $r['err_id']
                ]);  
            })
            ->editColumn('total_width_repair_lane', function($r) {
                if (in_array(4, $r['err_id']))
                {
                    return '<div class="error">'.$r['total_width_repair_lane'].'</div>';
                }
                else
                {
                    return $r['total_width_repair_lane'];
                }
            })

            ->make(true);
    }
}
