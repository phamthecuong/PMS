<?php

namespace App\Http\Requests\BackendRequest;

use Illuminate\Foundation\Http\FormRequest;

class IrregularKpRequest extends FormRequest
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
            'branch_id' => 'required',
            'kp' => 'required|integer|min:0',
            'section_length' => 'required|integer|min:0',
            'direction' => 'required|unique:mstIrregular_kps,direction,'. $this->segment(3). ',id,branch_id,'. $this->branch_id. ',kp,'. $this->kp
        ];
    }
}
