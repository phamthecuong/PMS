<?php

namespace App\Http\Requests\BackendRequest;

use Illuminate\Foundation\Http\FormRequest;

class RepairMethodRequest extends FormRequest
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
         $rules = [];
        if ($this->method() == 'POST')
        {
            return [
                'pavement_type' => 'required',
                'name_en' => 'required|unique:mstRepair_method,name_en',
                'name_vi' => 'required|unique:mstRepair_method,name_vn',
                'zone_id' => 'required'
                // 'cost' => 'required|digits_between:1,10',
            ];
        }
        else if ($this->method() == 'PUT')
        {
            return [
                'pavement_type' => 'required',
                'name_en' => 'required|unique:mstRepair_method,name_en,'.$this->segment(3),
                'name_vi' => 'required|unique:mstRepair_method,name_vn,'.$this->segment(3),
                // 'cost' => 'required|digits_between:1,10',
            ];
        }
        
    }
    public function messages()
    {
        return [
            'pavement_type.required'  =>  trans('validate.pavement_type_required'),
            'name_en.required'  =>  trans('validate.name_en_required'),
            'name_vi.required'  =>  trans('validate.name_vi_required'),
            'name_en.unique' => trans('validate.name_en_unique'),
            'name_vi.unique' => trans('validate.name_vn_unique'),
            'zone_id.required' => trans('validate.zone_id_required')
        ];
    }
}
