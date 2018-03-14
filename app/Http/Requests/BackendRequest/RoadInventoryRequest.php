<?php

namespace App\Http\Requests\BackendRequest;

use Illuminate\Foundation\Http\FormRequest;

class RoadInventoryRequest extends FormRequest
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
            'segment' => 'required',
            'date_collection' => 'required',
            'km_from' => 'required',
            'm_from' => 'required',
            'km_to' => 'required',
            'm_to' => 'required',
            'lane_no' => 'required',
            'data.6.material_type' => 'required',
            'data.6.thickness' => 'required',
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
