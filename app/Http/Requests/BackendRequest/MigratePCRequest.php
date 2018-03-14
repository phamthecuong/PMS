<?php

namespace App\Http\Requests\BackendRequest;

use Illuminate\Foundation\Http\FormRequest;

class MigratePCRequest extends FormRequest
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
            'pc_file' => 'required|unique:tblMigrate_PC,pc_file',
            'image_file' => 'required|unique:tblMigrate_PC,image_file',
        ];
    }
    public function messages()
    {
        return [
            'pc_file.required'  =>  trans('back_end.required'),
            'image_file.required'  =>  trans('back_end.required'),
            'pc_file.unique'  =>  trans('back_end.file_already_used'),
            'image_file.unique' => trans('back_end.file_already_used'),
        ];
    }
}
