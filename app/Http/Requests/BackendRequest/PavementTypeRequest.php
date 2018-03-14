<?php

namespace App\Http\Requests\BackendRequest;

use Illuminate\Foundation\Http\FormRequest;

class PavementTypeRequest extends FormRequest
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
        return [
            'name_en' => 'required|unique:mstPavement_type,name_en,'.$this->segment(3),
            'name_vn' => 'required|unique:mstPavement_type,name_vn,'.$this->segment(3),
            'code' => 'required',
            'pavement_layer' => 'required',
        ];
    }
    public function messages()
    {
        return [
            'name_en.required'  =>  trans('validate.name_en_required'),
            'name_vn.required'  =>  trans('validate.name_vn_required'),
            'name_en.unique' => trans('validate.name_en_unique'),
            'name_vn.unique' => trans('validate.name_vn_unique'),
        ];
    }
}
