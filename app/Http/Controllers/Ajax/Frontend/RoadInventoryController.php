<?php

namespace App\Http\Controllers\Ajax\Frontend;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Requests\BackendRequest\RoadInventoryInputtingRequest;
use App\Models\tblSegment;
use App\Models\tblSectiondataRMD;
use App\Models\tblSectionLayer;
use App\Models\mstPavementType;
use App\Models\mstSurface;
use App\Models\tblCity;
use App\Models\tblDistrict;
use App\Models\tblWard;
use App\Models\tblRMDHistory;
use App\Models\tblDesignSpeed;
use App\Classes\Helper;
use Carbon\Carbon;

class RoadInventoryController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //
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
    public function store(RoadInventoryInputtingRequest $request)
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
            $data = json_decode($request->data);

            $record = new tblSectiondataRMD();
            $record->segment_id = $request->segment_id;
            $record->terrian_type_id = $request->terrian_type_id;
            $record->road_class_id = $request->road_class_id;
            $record->from_lat = @$request->from_lat;
            $record->from_lng = @$request->from_lng;
            $record->to_lat = @$request->to_lat;
            $record->to_lng = @$request->to_lng;
            $record->km_from = $request->km_from;
            $record->m_from = $request->m_from;
            $record->km_to = $request->km_to;
            $record->m_to = $request->m_to;
            $record->ward_from_id = @$request->ward_from_id;
            $record->ward_to_id = @$request->ward_to_id;
            $survey = strtotime($request->survey_time);
            $ymd = date("Y-m-d", $survey);
            $record->survey_time = $ymd;
            $record->direction = $request->direction;
            $record->lane_pos_number = $request->lane_pos_number;
            $record->lane_width = $request->lane_width;
            $record->no_lane = $request->no_lane;
            $construct_year = str_replace('/', '', $request->construct_year);
            $record->construct_year = substr($construct_year, 2, 4). substr($construct_year, 0, 2);
            $service_start_year = str_replace('/', '', $request->service_start_year);
            $record->service_start_year = substr($service_start_year, 2, 4). substr($service_start_year, 0, 2);
            $record->temperature = $request->temperature;
            $record->annual_precipitation = $request->annual_precipitation;
            $record->actual_length = $request->actual_length;
            $record->remark = $request->remark;
            $record->pavement_type_id = $this->convertSurface($data->{'6'}->material_type);
            $record->save();
            
            if (!empty($data))
            {
                $sum = 0;
                foreach ($data as $layer_id => $layer)
                {
                    if (isset($layer->thickness))
                    {
                        if (in_array($layer_id, $this->configPavementType()))
                        {
                            $sum += $layer->thickness;
                        }
                        $l = new tblSectionLayer();
                        $l->thickness = @$layer->thickness;
                        $l->description = isset($layer->desc) ? $layer->desc : '';
                        $l->type = 1;
                        $l->material_type_id = @$layer->material_type;
                        $l->layer_id = $layer_id;
                        $l->rmdSection()->associate($record);
                        $l->save();
                    }
                }
            }
            $edit = tblSectiondataRMD::find($record->id);
            $edit->pavement_thickness = $sum;
            $edit->save();

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
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $data = tblSectiondataRMD::with('layers')->findOrFail($id)->toArray();
        
        //get data design_speed
        $design_speed = tblDesignSpeed::whereTerrainId($data['terrian_type_id'])->whereRoadClassId($data['road_class_id'])->first();
        if ($design_speed)
        {
            $speed = $design_speed->speed." (km/h)";
        }
        else
        {
            $speed = "N/A (km/h)";
        }

        //get surface id
        foreach ($data['layers'] as $key => $value) {
            if ($value['layer_id'] == 6)
            {
                $material_type_id = $value['material_type_id'];
            }
        }
        if (isset($material_type_id))
        {
            $surface_id = $this->convertSurface($material_type_id);
        }
        else
        {
            $surface_id = '';
        }

        //get data province, district, ward
        $ward_from = tblWard::find($data['ward_from_id']);
        if ($ward_from)
        {
            $district_from = $ward_from->district()->first();
            $list_ward_from = tblWard::where('district_id', $district_from->id)->get();
            $province_from = $district_from->province()->first();
            $list_district_from = tblDistrict::where('province_id', $province_from->id)->get();
        }

        $ward_to = tblWard::find($data['ward_to_id']);
        if ($ward_to)
        {
            $district_to = $ward_to->district()->first();
            $list_ward_to = tblWard::where('district_id', $district_to->id)->get();
            $province_to = $district_to->province()->first();
            $list_district_to = tblDistrict::where('province_id', $province_to->id)->get();
        }
        return response([
            'section' => array_add(array_add($this->transform($data), 'design_speed', $speed), 'surface_id', (string) $surface_id),
            'data_from' => [
                'w_name' => @$ward_from->name,
                'd_id' => @$district_from->id,
                'd_name' => @$district_from->name,
                'p_id' => (string) @$province_from->id,
                'p_name' => @$province_from->name,
                'list_ward' => @$list_ward_from,
                'list_district' => @$list_district_from
            ],
            'data_to' => [
                'w_name' => @$ward_to->name,
                'd_id' => @$district_to->id,
                'd_name' => @$district_to->name,
                'p_id' => (string) @$province_to->id,
                'p_name' => @$province_to->name,
                'list_ward' => @$list_ward_to,
                'list_district' => @$list_district_to
            ],
        ]);
        // return response($this->transform($data));
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
    public function update(RoadInventoryInputtingRequest $request, $id)
    {
        \DB::beginTransaction();
        try
        {
            $validation = $this->_doValidate($request, $id);
            if ($validation !== true)
            {
                return $validation;
            }
            $data = json_decode($request->data);

            $record = tblSectiondataRMD::findOrFail($id);
            $record->segment_id = $request->segment_id;
            $record->terrian_type_id = $request->terrian_type_id;
            $record->road_class_id = $request->road_class_id;
            $record->from_lat = @$request->from_lat;
            $record->from_lng = @$request->from_lng;
            $record->to_lat = @$request->to_lat;
            $record->to_lng = @$request->to_lng;
            $record->km_from = $request->km_from;
            $record->m_from = $request->m_from;
            $record->km_to = $request->km_to;
            $record->m_to = $request->m_to;
            $record->ward_from_id = @$request->ward_from_id;
            $record->ward_to_id = @$request->ward_to_id;
            $survey = strtotime($request->survey_time);
            $ymd = date("Y-m-d", $survey);
            $record->survey_time = $ymd;
            $record->direction = $request->direction;
            $record->lane_pos_number = $request->lane_pos_number;
            $record->lane_width = $request->lane_width;
            $record->no_lane = $request->no_lane;
            $construct_year = str_replace('/', '', $request->construct_year);
            $record->construct_year = substr($construct_year, 2, 4). substr($construct_year, 0, 2);
            $service_start_year = str_replace('/', '', $request->service_start_year);
            $record->service_start_year = substr($service_start_year, 2, 4). substr($service_start_year, 0, 2);
            $record->temperature = $request->temperature;
            $record->annual_precipitation = $request->annual_precipitation;
            $record->actual_length = $request->actual_length;
            $record->remark = $request->remark;
            $record->pavement_type_id = $this->convertSurface($data->{'6'}->material_type);
            $record->save();
            
            if (!empty($data))
            {
                $sum = 0;
                foreach ($data as $layer_id => $layer)
                {
                    if (isset($layer->thickness))
                    {
                        if (in_array($layer_id, $this->configPavementType()))
                        {
                            $sum += $layer->thickness;
                        }
                        $l = tblSectionLayer::where('sectiondata_id', $id)->where('layer_id', $layer_id)->where('type', 1)->first();
                        if (!empty($l))
                        {
                            $l->thickness = @$layer->thickness;
                            $l->description = isset($layer->desc) ? $layer->desc : '';
                            $l->material_type_id = @$layer->material_type;
                            $l->save();
                        }
                        else
                        {
                            $sl = new tblSectionLayer();
                            $sl->thickness = @$layer->thickness;
                            $sl->description = isset($layer->desc) ? $layer->desc : '';
                            $sl->type = 1;
                            $sl->material_type_id = @$layer->material_type;
                            $sl->layer_id = $layer_id;
                            $sl->rmdSection()->associate($record);
                            $sl->save();
                        }
                    }
                }
            }
            $edit = tblSectiondataRMD::find($id);
            $edit->pavement_thickness = $sum;
            $edit->save();

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
            $record = tblSectiondataRMD::findOrFail($id);
            $record->delete();
            // $histories = $record->histories;
            
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
            //     if (count($record->layers) > 0)
            //     {
            //         foreach ($record->layers as $layer) 
            //         {
            //             $layer->delete();
            //         }
            //     }

            //     if (count($history->layers) > 0)
            //     {
            //         foreach ($history->layers as $layer)
            //         {
            //             $l = new tblSectionLayer();
            //             $l->thickness = $layer->thickness;
            //             $l->description = $layer->description;
            //             $l->type = 1;
            //             $l->material_type_id = $layer->material_type_id;
            //             $l->layer_id = $layer->layer_id;
            //             $l->rmdSection()->associate($record);
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
            
            if ($key == 'layers' && count($value) > 0)
            {
                $arr = [];
                foreach ($value as $k => $v) 
                {
                    $arr[$v['layer_id']] = [
                        'desc' => $v['description'],
                        'material_type' => (string) $v['material_type_id'],
                        'thickness' => (float) $v['thickness'],
                    ];
                }
                $array['data'] = $arr;
            }
            elseif ($key == 'survey_time') {
                $survey = strtotime($value);
                $ymd = date("d-m-Y", $survey);
                $array['survey_time'] = $ymd;
            }
            elseif ($key == 'construct_year')
            {
                $array['construct_year'] = substr($value, 4, 2). '/'. substr($value, 0, 4);
            }
            elseif ($key == 'service_start_year')
            {
                if (empty($value))
                {
                    $array['service_start_year'] = '';
                }
                else
                {
                    $array['service_start_year'] = substr($value, 4, 2). '/'. substr($value, 0, 4);
                }
            }
            else
            {
                $array[$key] = $value;
            }
        }
        return $array;
    }

    private function _doValidate($request, $id = FALSE)
    {   
        $validation = [];
        if ($id)
        {
            $history = tblSectiondataRMD::where('id', $id)
                ->orderBy('survey_time', 'desc')
                ->skip(1)->take(1)
                ->first();
            $section = tblSectiondataRMD::findOrFail($id);
            if ($history)
            {
                if (Carbon::parse($request->survey_time)->year < Carbon::parse($history->survey_time)->year)
                {
                    return response([
                        'survey_time' => [trans('backend.survey_time_must_bigger')],
                    ], 422);
                }
            }
            else
            {
                if (Carbon::parse($request->survey_time)->year < Carbon::parse($section->survey_time)->year)
                {
                    return response([
                        'survey_time' => [trans('backend.survey_time_must_bigger')],
                    ], 422);
                }
            }

        }

        //direction check
        if ($request->direction == 3 && $request->lane_pos_number >= 1)
        {
            return response([
                'lane_pos_number' => [trans('backend.lane_pos_number_must_be_at_1')],
            ], 422);
        }

        if ($request->direction == 3 && $request->lane_pos_number == 0 && $request->no_lane != 1)
        {
            return response([
                'no_lane' => [trans('backend.no_lane_must_be_at_1')],
            ], 422);
        }

        if (($request->direction == 1 && $request->lane_pos_number < 1) || ($request->direction == 2 && $request->lane_pos_number < 1))
        {
            return response([
                'lane_pos_number' => [trans('backend.lane_pos_number_must_be_at_least_1')],
            ], 422);
        }

        if ($request->lane_pos_number > $request->no_lane)
        {
            return response([
                'lane_pos_number' => [trans('backend.no_lane_more_than_lane_pos_number')],
            ], 422);
        }

        //material type check
        $data = json_decode($request->data);
        $layers = \App\Models\mstPavementLayer::with('pavementTypes')
            ->whereNotNull('parent_id')
            ->get();
        foreach ($layers as $l) 
        {
            if (!empty($data->{"$l->id"}->thickness))
            {
                if ($data->{"$l->id"}->thickness < 0) 
                {
                    $validation += [
                        "data[$l->id][thickness]" => [trans('backend.thickness_must_be_at_least_0')]
                    ]; 
                }
            }
            
            if ($l->id == 6)
            {
                if (empty($data->{'6'}->material_type))
                {
                    $validation += [ 
                            "data[6][material_type]" => [trans('backend.required')]
                        ];
                }
                if (!isset($data->{'6'}->thickness)) 
                {
                    $validation += [
                            "data[6][thickness]" => [trans('backend.required')]
                        ];
                }
            }
            else
            {
                if (!empty($data->{"$l->id"}->material_type) && !isset($data->{"$l->id"}->thickness))
                {
                    $validation += [
                            "data[$l->id][thickness]" => [trans('backend.required')]
                        ];
                }
                else if (!empty($data->{"$l->id"}->thickness) && empty($data->{"$l->id"}->material_type))
                {
                    $validation += [
                            "data[$l->id][material_type]" => [trans('backend.required')]
                        ];   
                }
            }
           
        }

        if (count($validation) > 0) {
            return response($validation, 422);
        }

        $adjust = 100000;
        if ($adjust*$request->km_to + $request->m_to - $adjust*$request->km_from - $request->m_from <= 0)
        {
            return response([
                    'km_from' => [trans('back_end.invalid_chainage')],
                ], 422);

        }
        // segment check
        $segment = tblSegment::find($request->segment_id);

        if (
            ($adjust*$request->km_from + $request->m_from < $adjust*$segment->km_from + $segment->m_from) ||
            ($adjust*$request->km_to + $request->m_to > $adjust*$segment->km_to + $segment->m_to)
        )
        {   
            return response([
                    'km_from' => [trans('back_end.chainage_need_fit_segment')],
                ], 422);
        }
        // check overlap
        $data = $request->except('_token', 'rmb', 'sb', 'route');
        if (!$id)
        {
            $check_overlap = Helper::validationOverlapping($data, [tblSectiondataRMD::config()], NULL, '\App\Models\tblSectiondataRMD');
        }
        if ($id)
        {            
            $check_overlap = Helper::validationOverlapping($data, [tblSectiondataRMD::config()], $id, '\App\Models\tblSectiondataRMD');
        }
        
        if (!$check_overlap)
        {
            return response([
                'km_from' => [trans('back_end.overlap_with_existing_section')],
            ], 422);
        }

        return true;
    }
    
    function convertSurface($id)
    {
        $pavement_type = mstPavementType::findOrFail($id)->surface_id;
        $surface = mstSurface::where('id', $pavement_type)->first();
        return $surface->id;
    }

    function convertPavementType($id)
    {
        $rule = [
            'AC' => 'AC',
            'CC' => 'CC',
            'BST' => 'BST',
            'BPM' => 'BST',
            'BM' => 'BST',
            'WBM' => 'UP',
            'GP' => 'UP',
            'SSP' => 'UP',
            'EP' => 'UP',
            'RP' => '*',
            'Others' => '*',
        ];
        $pavement_type = mstPavementType::findOrFail($id)->code;
        $surface_code = $rule[$pavement_type];
        $surface = mstSurface::where('code_name', $surface_code)->first();
        return $surface->id;
    }

    public function configPavementType()
    {
        return ['6','7','8','9'];
    }
}
