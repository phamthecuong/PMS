<?php

namespace App\Http\Requests\BackendRequest;

use Illuminate\Foundation\Http\FormRequest;
use Carbon\Carbon;

class MaintenanceHistoryRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        $validation = [
            'rmb' => 'required',
            'sb' => 'required',
            'route' => 'required',
            'segment_id' => 'required',
            'survey_time' => 'required|date|before:now',
            'completion_date' => 'required|date|before:now',
            'km_from' => 'required|numeric|min:0|integer',
            'm_from' => 'required|numeric|min:0|integer',
            'km_to' => 'required|numeric|min:0|integer',
            'm_to' => 'required|numeric|min:0|integer',
            'from_lat' => 'numeric|min:190004.0544|max:832157.7917',
            'from_lng' => 'numeric|min:663996.8088|max:2589882.7561',
            'to_lat' => 'numeric|min:190004.0544|max:832157.7917',
            'to_lng' => 'numeric|min:663996.8088|max:2589882.7561',
            'lane_pos_number' => 'required|numeric|min:0|integer',
            'direction' => 'required',
            'actual_length' => 'numeric|min:0',
            'total_width_repair_lane' => 'required|numeric|min:0',
            'r_classification_id' => 'required',
            'r_struct_type_id' => 'required',
            'repair_duration' => 'required|integer|min:1',
            'r_category_id' => 'required',
            'distance' => 'required|numeric|min:0',
            'direction_running' => 'required',
            'repair_method_id' => 'required',
            // 'remark' => 'required',
        ];
        $layers = \App\Models\mstPavementLayer::with('pavementTypes')
            ->whereNotNull('parent_id')
            ->get();
        foreach ($layers as $l) 
        {
            if (!empty($_REQUEST["data"][$l->id]["material_type"]))
            {
                $validation+= ["data.{$l->id}.thickness" => 'required'];
            }
            else if (!empty($_REQUEST["data"][$l->id]["thickness"]))
            {
                $validation+= ["data.{$l->id}.material_type" => 'required'];   
            }
        }

        return $validation;
    }
}
