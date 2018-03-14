<?php

namespace App\Http\Requests\BackendRequest;

use Illuminate\Foundation\Http\FormRequest;

class ImportRequest extends FormRequest
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
            'file' => 'required|mimes:xlsx|max:20000'
        ];
    }
    public function messages()
    {
        return [
            'file.required'  =>  trans('roadasset.import_required'),
            'file.mimes'  =>  trans('roadasset.import_format'),
            'file.max' => trans('roadasset.import_limited'),
        ];
    }
}
