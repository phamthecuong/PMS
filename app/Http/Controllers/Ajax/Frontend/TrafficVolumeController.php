<?php

namespace App\Http\Controllers\Ajax\Frontend;

use App\Models\tblTVHistory;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Akeneo\Component\SpreadsheetParser\SpreadsheetParser;
use App\Http\Requests\BackendRequest\TrafficVolumeRequest;
use App\Models\tblBranch;
use Carbon\Carbon;
use App\Models\tblSectiondataTV;
use App\Models\tblTVVehicleDetails;
use App\Models\tblSegment;
use App\Models\tblOrganization;
use App\Models\tblCity;
use App\Models\tblDistrict;
use App\Models\tblWard;
use Excel;

class TrafficVolumeController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        dd(1);
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
    public function store(TrafficVolumeRequest $request)
    {
        // dd($request->all());
        \DB::beginTransaction();
        try
        {
            $validation = $this->_doValidate($request);
            if ($validation !== true)
            {
                return $validation;
            }
            $record = new tblSectiondataTV();
            $record->segment_id = $request->segment_id;
            $record->name_en = $request->name_en;
            $record->name_vn = $request->name_vn;
            $record->km_station = $request->km_station;
            $record->m_station = $request->m_station;
            $record->lat_station = @$request->lat_station;
            $record->lng_station = @$request->lng_station;
            $record->ward_id = @$request->ward_id;
            $survey_time = str_replace("/", "", $request->survey_time);
            $record->survey_time = substr($survey_time, 2, 4).'-'. substr($survey_time, 0, 2)."-01";
            $record->remark = $request->remark;
            $record->total_traffic_volume_up = $request->total_traffic_volume_up;
            $record->total_traffic_volume_down = $request->total_traffic_volume_down;
            $record->heavy_traffic_up = $request->heavy_traffic_up;
            $record->heavy_traffic_down = $request->heavy_traffic_down;
            $record->save();

            $vehicle_detail = [
                1 => [
                    'up' => @$request->car_jeep_up,
                    'down' => @$request->car_jeep_down
                ],
                2 => [
                    'up' => @$request->light_truck_up,
                    'down' => @$request->light_truck_down
                ],
                3 => [
                    'up' => @$request->medium_truck_up,
                    'down' => @$request->medium_truck_down
                ],
                4 => [
                    'up' => @$request->heavy_truck_up,
                    'down' => @$request->heavy_truck_down
                ],
                5 => [
                    'up' => @$request->heavy_truck3_up,
                    'down' => @$request->heavy_truck3_down
                ],
                6 => [
                    'up' => @$request->small_bus_up,
                    'down' => @$request->small_bus_down
                ],
                7 => [
                    'up' => @$request->large_bus_up,
                    'down' => @$request->large_bus_down
                ],
                8 => [
                    'up' => @$request->tractor_up,
                    'down' => @$request->tractor_down
                ],
                9 => [
                    'up' => @$request->motobike_including_3_wheeler_up,
                    'down' => @$request->motobike_including_3_wheeler_down
                ],
                10 => [
                    'up' => @$request->bicycle_pedicab_up,
                    'down' => @$request->bicycle_pedicab_down
                ]    
            ];

            foreach ($vehicle_detail as $vehicle_id => $info) 
            {
                $l = new tblTVVehicleDetails();
                $l->up = isset($info['up']) ? $info['up'] : 0;
                $l->down = isset($info['down']) ? $info['down'] : 0;
                $l->vehicle_type_id = $vehicle_id;
                $l->section()->associate($record);
                $l->save();
            }

            \DB::commit();
            return response([
                'success' => 1
            ]);
        }
        catch (\Exception $e)
        {
            \DB::rollBack();
            dd($e);
        }
    }

    private function _doValidate($request, $id = null)
    {
        
        $survey_time = str_replace("/", "", $request->survey_time);
        $survey_time_convert = substr($survey_time, 2, 4).'-'. substr($survey_time, 0, 2)."-01";
        if ($id)
        {
            $history = tblSectiondataTV::where('id', $id)
                ->orderBy('survey_time', 'desc')
                ->skip(1)->take(1)
                ->first();
            $section = tblSectiondataTV::findOrFail($id);
            if ($history)
            {
                if (Carbon::parse($survey_time_convert)->year < Carbon::parse($history->survey_time)->year)
                {
                    return response([
                        'survey_time' => [trans('backend.survey_time_must_bigger')],
                    ], 422);
                }
            }
            else
            {
                if (Carbon::parse($survey_time_convert)->year < Carbon::parse($section->survey_time)->year)
                {
                    return response([
                        'survey_time' => [trans('backend.survey_time_must_bigger')],
                    ], 422);
                }
            }

        }

        $adjust = 100000;
        
        //check overlap and segment check
        $segment_check = tblSegment::find($request->segment_id);
        $segment_m_from_convert = $segment_check->km_from * 1000000 + $segment_check->m_from;
        $segment_m_to_convert = $segment_check->km_to * 1000000 + $segment_check->m_to;
        $sub_record_station = $request->km_station * 1000000 + $request->m_station;
        if ($sub_record_station < $segment_m_from_convert || $sub_record_station > $segment_m_to_convert)
        {
            return response([
                'km_station' => [trans('back_end.chainage_need_fit_segment')],
            ], 422);
        }
        else
        {
            $tv_check = tblSectiondataTV::where('id', '!=', $id)
                ->where('segment_id', $request->segment_id)
                ->where('km_station', $request->km_station)
                ->where('m_station', $request->m_station)
                ->whereRaw("YEAR(survey_time) = '" . substr($request->survey_time, 0, 4) . "'")
                ->count();
            if ($tv_check > 0)
            {
                return response([
                    'km_station' => [trans('back_end.overlap_with_existing_section')],
                ], 422);
            }

        }

        return true;
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $data = tblSectiondataTV::with('vehicleInfos')->findOrFail($id)->toArray();
        $ward = tblWard::find($data['ward_id']);
        if ($ward)
        {
            $district = $ward->district()->first();
            $list_ward = tblWard::where('district_id', $district->id)->get();
            $province = $district->province()->first();
            $list_district = tblDistrict::where('province_id', $province->id)->get();
        }
        return response([
            'section' => $this->transform($data),
            'data_station' => [
                'w_name' => @$ward->name,
                'd_id' => @$district->id,
                'd_name' => @$district->name,
                'p_id' => (string) @$province->id,
                'p_name' => @$province->name,
                'list_ward' => @$list_ward,
                'list_district' => @$list_district
            ]
        ]);
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
    public function update(TrafficVolumeRequest $request, $id)
    {
        // dd($request->all());
        \DB::beginTransaction();
        try
        {
            $validation = $this->_doValidate($request, $id);
            if ($validation !== true)
            {
                return $validation;
            }
            $record = tblSectiondataTV::findOrFail($id);
            $record->segment_id = $request->segment_id;
            $record->name_en = $request->name_en;
            $record->name_vn = $request->name_vn;
            $record->km_station = $request->km_station;
            $record->m_station = $request->m_station;
            $record->lat_station = @$request->lat_station;
            $record->lng_station = @$request->lng_station;
            $record->ward_id = @$request->ward_id;
            $survey_time = str_replace("/", "", $request->survey_time);
            $record->survey_time = substr($survey_time, 2, 4).'-'. substr($survey_time, 0, 2)."-01";
            $record->remark = $request->remark;
            $record->total_traffic_volume_up = $request->total_traffic_volume_up;
            $record->total_traffic_volume_down = $request->total_traffic_volume_down;
            $record->heavy_traffic_up = $request->heavy_traffic_up;
            $record->heavy_traffic_down = $request->heavy_traffic_down;
            $record->save();

            $vehicle_detail = [
                1 => [
                    'up' => @$request->car_jeep_up,
                    'down' => @$request->car_jeep_down
                ],
                2 => [
                    'up' => @$request->light_truck_up,
                    'down' => @$request->light_truck_down
                ],
                3 => [
                    'up' => @$request->medium_truck_up,
                    'down' => @$request->medium_truck_down
                ],
                4 => [
                    'up' => @$request->heavy_truck_up,
                    'down' => @$request->heavy_truck_down
                ],
                5 => [
                    'up' => @$request->heavy_truck3_up,
                    'down' => @$request->heavy_truck3_down
                ],
                6 => [
                    'up' => @$request->small_bus_up,
                    'down' => @$request->small_bus_down
                ],
                7 => [
                    'up' => @$request->large_bus_up,
                    'down' => @$request->large_bus_down
                ],
                8 => [
                    'up' => @$request->tractor_up,
                    'down' => @$request->tractor_down
                ],
                9 => [
                    'up' => @$request->motobike_including_3_wheeler_up,
                    'down' => @$request->motobike_including_3_wheeler_down
                ],
                10 => [
                    'up' => @$request->bicycle_pedicab_up,
                    'down' => @$request->bicycle_pedicab_down
                ]    
            ];

            foreach ($vehicle_detail as $vehicle_id => $info) 
            {
                $l = tblTVVehicleDetails::where('vehicle_type_id', $vehicle_id)->where('sectiondata_TV_id', $id)->first();
                $l->up = isset($info['up']) ? $info['up'] : 0;
                $l->down = isset($info['down']) ? $info['down'] : 0;
                $l->save();
            }

            \DB::commit();
            return response([
                'success' => 1
            ]);
        }
        catch (\Exception $e)
        {
            \DB::rollBack();
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
        \DB::beginTransaction();
        try
        {
            $record = tblSectiondataTV::findOrFail($id);
            $record->delete();
            
            // if (count($histories) == 1)
            // {
            //     $record->delete();
            //     $histories->first()->delete();
            // }
            // else
            // {
            //     $history = $histories[1];
            //     $data_update = $history->makeHidden(['id', 'sectiondata_id', 'status', 'name', 'created_at', 'updated_at', 'created_by', 'updated_by'])->toArray();
            //     $record->update($data_update);

            //     if (count($record->vehicleInfos) > 0)
            //     {
            //         foreach ($record->vehicleInfos as $vehicle)
            //         {
            //             $vehicle->delete();
            //         }
            //     }

            //     if (count($history->vehicleInfos) > 0)
            //     {
            //         foreach ($history->vehicleInfos as $vehicle)
            //         {
            //             $l = new tblTVVehicleDetails();
            //             $l->up = $vehicle->up;
            //             $l->down = $vehicle->down;
            //             $l->vehicle_type_id = $vehicle->vehicle_type_id;
            //             $l->section()->associate($record);
            //             $l->save();
            //         }
            //     }
            //     $histories[0]->delete();
            // }

            \DB::commit();
            return response([
                'success' => 1
            ]);
        }
        catch (\Exception $e)
        {
            \DB::rollBack();
            dd($e);
        }
    }

    function transform($data)
    {
        $array = [];
        foreach ($data as $key => $value) 
        {
            if ($key == 'vehicle_infos' && count($value) > 0)
            {
                foreach ($value as $v) 
                {
                    switch ($v['vehicle_type_id']) 
                    {
                        case 1:
                            $array['car_jeep_up'] = (float) $v['up'];
                            $array['car_jeep_down'] = (float) $v['down'];
                            break;
                        case 2:
                            $array['light_truck_up'] = (float) $v['up'];
                            $array['light_truck_down'] = (float) $v['down'];
                            break;
                        case 3:
                            $array['medium_truck_up'] = (float) $v['up'];
                            $array['medium_truck_down'] = (float) $v['down'];
                            break;
                        case 4:
                            $array['heavy_truck_up'] = (float) $v['up'];
                            $array['heavy_truck_down'] = (float) $v['down'];
                            break;
                        case 5:
                            $array['heavy_truck3_up'] = (float) $v['up'];
                            $array['heavy_truck3_down'] = (float) $v['down'];
                            break;
                        case 6:
                            $array['small_bus_up'] = (float) $v['up'];
                            $array['small_bus_down'] = (float) $v['down'];
                            break;
                        case 7:
                            $array['large_bus_up'] = (float) $v['up'];
                            $array['large_bus_down'] = (float) $v['down'];
                            break;
                        case 8:
                            $array['tractor_up'] = (float) $v['up'];
                            $array['tractor_down'] = (float) $v['down'];
                            break;
                        case 9:
                            $array['motobike_including_3_wheeler_up'] = (float) $v['up'];
                            $array['motobike_including_3_wheeler_down'] = (float) $v['down'];
                            break;
                        case 10:
                            $array['bicycle_pedicab_up'] = (float) $v['up'];
                            $array['bicycle_pedicab_down'] = (float) $v['down'];
                            break;
                        default:
                            # code...
                            break;
                    }
                }
            }
            elseif ($key == 'survey_time') 
            {
                $array[$key] = substr($value, 5, 2).'/'.substr($value, 0, 4) ;
            }
            else
            {
                $array[$key] = $value;
            }
        }
        return $array;
    }
}
