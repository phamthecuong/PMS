<?php

namespace App\Http\Requests\BackendRequest;

use Illuminate\Foundation\Http\FormRequest;
use Carbon\Carbon;

class TrafficVolumeRequest extends FormRequest
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
        $now = Carbon::now()->addMonths(1)->toDateString();
        $next_month = str_replace('-', '/', $now);

        $validation = [
            'rmb' => 'required',
            'sb' => 'required',
            'route' => 'required',
            'segment_id' => 'required',
            'survey_time' => 'required|date_format:m/Y|before:'.$next_month,
            'name_en' => 'required',
            'name_vn' => 'required',
            'km_station' => 'required|numeric|min:0|integer',
            'm_station' => 'required|numeric|min:0|integer',
            'lat_station' => 'numeric|min:190004.0544|max:832157.7917',
            'lng_station' => 'numeric|min:663996.8088|max:2589882.7561',
            // 'remark' => 'required',
            'car_jeep_up' => 'numeric|min:0',
            'light_truck_up' => 'numeric|min:0',
            'medium_truck_up' => 'numeric|min:0',
            'heavy_truck_up' => 'numeric|min:0',
            'heavy_truck3_up' => 'numeric|min:0',
            'small_bus_up' => 'numeric|min:0',
            'large_bus_up' => 'numeric|min:0',
            'tractor_up' => 'numeric|min:0',
            'motobike_including_3_wheeler_up' => 'numeric|min:0',
            'bicycle_pedicab_up' => 'numeric|min:0',
            'car_jeep_down' => 'numeric|min:0',
            'light_truck_down' => 'numeric|min:0',
            'medium_truck_down' => 'numeric|min:0',
            'heavy_truck_down' => 'numeric|min:0',
            'heavy_truck3_down' => 'numeric|min:0',
            'small_bus_down' => 'numeric|min:0',
            'large_bus_down' => 'numeric|min:0',
            'tractor_down' => 'numeric|min:0',
            'motobike_including_3_wheeler_down' => 'numeric|min:0',
            'bicycle_pedicab_down' => 'numeric|min:0',
        ];

        return $validation;
    }
}
