<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class BudgetProcessRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        if (\Auth::user()->hasPermission('budget_simulation.budget_simulation'))
        {
            return true;    
        }
        return false;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        $rules = [];
        switch ($_POST['scenario']) 
        {
            case '0':
                break;
            case '1':
                $rules['budget_constraint'] = 'required|numeric|min:0';
                break;
            case '2':
                break;
            case '3':
                $rules['target_risk_level'] = 'required|numeric|min:0|max:100';
                break;
            default:
                break;
        }
        return $rules;
    }
}
