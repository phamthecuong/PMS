<?php

namespace App\Http\Requests\BackendRequest;

use Illuminate\Foundation\Http\FormRequest;

class RepairMethodCostRequest extends FormRequest
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
        $user = \Auth::user();
        if ($user->hasRole("userlv2"))
        {
            $organizations = \App\Models\tblOrganization::where('id', $user->organization_id)->get();
        }
        else
        {
            $organizations = \App\Models\tblOrganization::where('level', 2)->get();
        }

        $rules = [];
        foreach ($organizations as $o) 
        {
            $rules['cost.' . $o->id] = 'required|numeric|min:1|max:10000';
        }
        return $rules;
    }
}
