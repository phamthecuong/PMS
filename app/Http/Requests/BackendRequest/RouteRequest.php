<?php

namespace App\Http\Requests\BackendRequest;

use Illuminate\Foundation\Http\FormRequest;

class RouteRequest extends FormRequest
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
        // dd($this->segment(3));
        return [
            'r_category' => 'required',
            'name_en' => 'required',
            'name_vi' => 'required',
            'branch_number' => 'required|digits_between:2,2',
            'road_number' => 'required|digits_between:3,3',
            'r_number_supplement' => 'required|unique:tblBranch,road_number_supplement,'.$this->segment(3).',id,road_number,' . $this->road_number. ',branch_number,'. $this->branch_number . ',road_category,'. $this->r_category
        ];
    }
//    public function messages()
//    {
//        return [
//            'branch_number.integer' => 'The branch number must be integer',
//            'branch_number.required' => 'The branch number must required',
//            'branch_number.min' => 'Min of Branch number is 1',
//            'branch_number.max' => 'Max of Branch number is 2',
//            'road_number.required' => 'The road number must required',
//            'road_number.numeric' => 'This input must is number',
//        ];
//    }
}
