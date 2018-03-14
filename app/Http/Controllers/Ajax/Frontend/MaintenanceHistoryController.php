<?php

namespace App\Http\Controllers\Ajax\Frontend;

use App\Classes\Helper;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Requests\BackendRequest\MaintenanceHistoryRequest;
use App\Models\tblSectiondataMH;
use App\Models\tblMHHistory;
use App\Models\tblSectionLayer;
use App\Models\tblSegment;
use App\Models\tblSectiondataRMD;
use Akeneo\Component\SpreadsheetParser\SpreadsheetParser;
use App\Models\tblBranch;
use App\Models\tblOrganization;
use App\Models\mstPavementType;
use App\Models\mstSurface;
use App\Models\tblCity;
use App\Models\tblDistrict;
use App\Models\tblWard;
use App\Models\tblRCategory;
use Carbon\Carbon;
use Excel;

class MaintenanceHistoryController extends Controller
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
    public function store(MaintenanceHistoryRequest $request)
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

            $record = new tblSectiondataMH();
            $record->segment_id = $request->segment_id;
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
            $completion_date = strtotime(@$request->completion_date);
            $ymd = date("Y-m-d", @$completion_date);
            $record->survey_time = @$ymd;
            $record->completion_date = @$ymd;
            $record->repair_duration = @$request->repair_duration;
            $record->direction = @$request->direction;
            $record->actual_length = @$request->actual_length;
            $record->lane_pos_number = $request->lane_pos_number;
            $record->total_width_repair_lane = @$request->total_width_repair_lane;
            $record->r_classification_id = @$request->r_classification_id;
            $record->r_structType_id = @$request->r_struct_type_id;
            $record->r_category_id = @$request->r_category_id;
            $record->distance = @$request->distance;
            $record->direction_running = @$request->direction_running;
            $record->remark = @$request->remark;
            $record->pavement_type_id = $this->convertSurface($data->{'6'}->material_type);
            $record->repair_method_id = @$request->repair_method_id;
            $record->save();

            if (!empty($data))
            {
                foreach ($data as $layer_id => $layer) 
                {
                    if (isset($layer->thickness))
                    {
                        $l = new tblSectionLayer();
                        $l->thickness = @$layer->thickness;
                        $l->description = isset($layer->desc) ? $layer->desc : '';
                        $l->type = 2;
                        $l->material_type_id = @$layer->material_type;
                        $l->layer_id = $layer_id;
                        $l->mhSection()->associate($record);
                        $l->save();
                    }
                }
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

    private function _doValidate($request, $id = FALSE)
    {
        $validation = [];
        if ($id)
        {
            $history = tblSectiondataMH::where('id', $id)
                ->orderBy('survey_time', 'desc')
                ->skip(1)->take(1)
                ->first();
            $section = tblSectiondataMH::findOrFail($id);
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

        if (($request->direction == 1 && $request->lane_pos_number < 1) || ($request->direction == 2 && $request->lane_pos_number < 1))
        {
            return response([
                'lane_pos_number' => [trans('backend.lane_pos_number_must_be_at_least_1')],
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
            $check_overlap = Helper::validationOverlapping($data, [tblSectiondataMH::config()], NULL, '\App\Models\tblSectiondataMH');
        }
        if ($id)
        {
            $check_overlap = Helper::validationOverlapping($data, [tblSectiondataMH::config()], $id, '\App\Models\tblSectiondataMH');
        }
        
        if (!$check_overlap)
        {
            return response([
                'km_from' => [trans('back_end.overlap_with_existing_section')],
            ], 422);
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
        $data = tblSectiondataMH::with('layers')->findOrFail($id)->toArray();
        $ward_from = tblWard::find($data['ward_from_id']);
        $surface_id = tblRCategory::whereId($data['r_category_id'])->pluck('surface_id')->first();
        $check_surface = mstSurface::find($surface_id);
        if (!$check_surface)
        {
            $data_classification = [];
        }
        else
        {
            $data_classification = $check_surface->repair_categories;
        }
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
            'section' => array_add($this->transform($data), 'surface_id', (string) $surface_id),
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
            'data_classification' => $data_classification
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
    public function update(MaintenanceHistoryRequest $request, $id)
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

            $record = tblSectiondataMH::findOrFail($id);
            $record->segment_id = $request->segment_id;
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
            $completion_date = strtotime(@$request->completion_date);
            $ymd = date("Y-m-d", @$completion_date);
            $record->survey_time = @$ymd;
            $record->completion_date = @$ymd;
            $record->repair_duration = @$request->repair_duration;
            $record->direction = @$request->direction;
            $record->actual_length = @$request->actual_length;
            $record->lane_pos_number = $request->lane_pos_number;
            $record->total_width_repair_lane = @$request->total_width_repair_lane;
            $record->r_classification_id = @$request->r_classification_id;
            $record->r_structType_id = @$request->r_struct_type_id;
            $record->r_category_id = @$request->r_category_id;
            $record->distance = @$request->distance;
            $record->direction_running = @$request->direction_running;
            $record->remark = @$request->remark;
            $record->pavement_type_id = $this->convertSurface($data->{'6'}->material_type);
            $record->repair_method_id = @$request->repair_method_id;
            $record->save();

            if (!empty($data))
            {
                $sum = 0;
                foreach ($data as $layer_id => $layer)
                {
                    if (isset($layer->thickness))
                    {
                        $sum += $layer->thickness;
                        $l = tblSectionLayer::where('sectiondata_id', $id)->where('layer_id', $layer_id)->where('type', 2)->first();
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
                            $sl->type = 2;
                            $sl->material_type_id = @$layer->material_type;
                            $sl->layer_id = $layer_id;
                            $sl->mhSection()->associate($record);
                            $sl->save();
                        }
                    }
                }
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
            $record = tblSectiondataMH::findOrFail($id);
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
            //     $data_update = $history->makeHidden(['id', 'sectiondata_id', 'name', 'status', 'created_at', 'updated_at', 'created_by', 'updated_by'])->toArray();
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
            //             $l->type = 2;
            //             $l->material_type_id = $layer->material_type_id;
            //             $l->layer_id = $layer->layer_id;
            //             $l->mhSection()->associate($record);
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
            elseif ($key == 'completion_date') {
                $completion_date = strtotime($value);
                $ymd = date("d-m-Y", $completion_date);
                $array['completion_date'] = $ymd;
            }
            else
            {
                $array[$key] = $value;
            }
        }
        return $array;
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
}
