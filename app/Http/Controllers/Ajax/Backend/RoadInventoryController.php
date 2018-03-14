<?php

namespace App\Http\Controllers\Ajax\Backend;

use App\Models\tblSectiondataRMD;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Auth, App;
use Yajra\Datatables\Facades\Datatables;

class RoadInventoryController extends Controller
{


    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $records = tblSectiondataRMD::filterByUser()
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
            ->filterSuperInput('lane_pos_number' , 'lane_pos_number', $request)->get();

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
                return view('front-end.m13.road_inventory.extra_view',[
                    'road_inventory' => $r
                ]);
            })
            ->addColumn('construct_year_col', function($r) {
                return substr((string) $r->construct_year, 0, 4);
            })
            ->addColumn('service_start_year_col', function($r) {
                return substr((string) $r->service_start_year, 0, 4);
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
        if (@$request->temperature_min != "") $temperature_min = $request->temperature_min;
        if (@$request->temperature_max != "") $temperature_max = $request->temperature_max;
        if (@$request->precipitation_min != "") $precipitation_min = $request->precipitation_min;
        if (@$request->precipitation_max != "") $precipitation_max = $request->precipitation_max;
        $errors = [];

        if (!$chainage['three'] && !$time_data['one'] && !$time_data['two'] && !isset($width_min) && !isset($width_max) && !isset($temperature_min) && !isset($temperature_max) && !isset($precipitation_min) && !isset($precipitation_max))
        {
            $rmd = [];
        }
        else
        {
            $rmd = tblSectiondataRMD::filterByUser()->with('segment.tblBranch', 'segment.tblOrganization.rmb', 'segment.tblCity_from', 'segment.tblCity_to', 'terrianType', 'routeClass')
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
                ->filterSuperInput('lane_width' , 'lane_width', $request)
                ->get()->toArray();
        }

        foreach ($rmd as $record) 
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

            //Check construct year
            if ($time_data['one'] == '1')
            {
                if (strlen($record['construct_year']) < 5)
                {
                    $err_name[] = trans('review_tool.invalid_construct_year');
                    $err_id[] = 2;
                }
            }

            //Check service start year
            if ($time_data['two'] == '1')
            {
                if (strlen($record['service_start_year']) < 5)
                {
                    $err_name[] = trans('review_tool.invalid_service_start_year');
                    $err_id[] = 3;
                }
            }

            //Check lane width
            if (isset($width_min) && !isset($width_max))
            {
                if ($record['lane_width'] < $width_min)
                {
                    $err_name[] = trans('review_tool.invalid_lane_width');
                    $err_id[] = 4;
                }
            }
            elseif (!isset($width_min) && isset($width_max)) 
            {
                if ($record['lane_width'] > $width_max)
                {
                    $err_name[] = trans('review_tool.invalid_lane_width');
                    $err_id[] = 4;
                }
            }
            elseif (isset($width_min) && isset($width_max))
            {
                if ($record['lane_width'] < $width_min || $record['lane_width'] > $width_max)
                {
                    $err_name[] = trans('review_tool.invalid_lane_width');
                    $err_id[] = 4;
                }
            }

            //Check temperature
            if (isset($temperature_min) && !isset($temperature_max))
            {
                if ($record['temperature'] < $temperature_min)
                {
                    $err_name[] = trans('review_tool.invalid_temperature');
                    $err_id[] = 5;
                }
            }
            elseif (!isset($temperature_min) && isset($temperature_max)) 
            {
                if ($record['temperature'] > $temperature_max)
                {
                    $err_name[] = trans('review_tool.invalid_temperature');
                    $err_id[] = 5;
                }
            }
            elseif (isset($temperature_min) && isset($temperature_max))
            {
                if ($record['temperature'] < $temperature_min || $record['temperature'] > $temperature_max)
                {
                    $err_name[] = trans('review_tool.invalid_temperature');
                    $err_id[] = 5;
                }
            }

            //Check precipitation
            if (isset($precipitation_min) && !isset($precipitation_max))
            {
                if ($record['annual_precipitation'] < $precipitation_min)
                {
                    $err_name[] = trans('review_tool.invalid_precipitation');
                    $err_id[] = 6;
                }
            }
            elseif (!isset($precipitation_min) && isset($precipitation_max)) 
            {
                if ($record['annual_precipitation'] > $precipitation_max)
                {
                    $err_name[] = trans('review_tool.invalid_precipitation');
                    $err_id[] = 6;
                }
            }
            elseif (isset($precipitation_min) && isset($precipitation_max))
            {
                if ($record['annual_precipitation'] < $precipitation_min || $record['annual_precipitation'] > $precipitation_max)
                {
                    $err_name[] = trans('review_tool.invalid_precipitation');
                    $err_id[] = 6;
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
                return view('front-end.m13.road_inventory.extra_view_review', [
                    'rmd' => $r,
                    'err_id' => $r['err_id']
                ]);  
            })
            ->editColumn('lane_width', function($r) {
                if (in_array(4, $r['err_id']))
                {
                    return '<div class="error">'.$r['lane_width'].'</div>';
                }
                else
                {
                    return $r['lane_width'];
                }
            })

            ->make(true);
    }
}
