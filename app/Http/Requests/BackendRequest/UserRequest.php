<?php

namespace App\Http\Requests\BackendRequest;

use Illuminate\Foundation\Http\FormRequest;

class UserRequest extends FormRequest
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
            $rules =
                [
                    'name' => 'required|without_spaces|unique:users,name,|min:4',
                    'email' => 'required|email|max:255|unique:users,email',
                ];
            if (isset($_REQUEST['password']) || isset($_REQUEST['confirm_pass']))
            {
                $rules = array_merge_recursive($rules, [
                    'password' => [
                        'required',
                        'regex:/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[!@#$%^&*()_+])(?=.*(_|[^\w])).+$/',
                        'min:8',
                    ]
                ]);

                $rules = array_merge_recursive($rules, [
                    'confirmPassword' => [
                        'required',
                        'same:password'
                    ]
                ]);

            }
        }
        elseif($this->method() == 'PUT')
        {
            $rules =
                [
                    'name' => 'required|without_spaces||min:4|unique:users,name,'.$this->segment(3),
                    'email' => 'required|email|max:255|unique:users,email,'. $this->segment(3),
                ];
        }
        return $rules;
    }
    public function messages()
    {
        return [
            'name.required'  =>  trans('validate.name_required'),
            'name.unique' => trans('validate.name_unique'),
            'name.without_spaces' => trans('validate.without_spaces'),
            'name.min' => trans('validate.min'),
            'email.required'  =>  trans('validate.email_required'),
            'email.email'  =>  trans('validate.email'),
            'email.max' => trans('validate.email_max'),
            'email.unique' => trans('validate.email_unique'),
            'password.min'  => trans('validate.password_min'),
            'password.regex' => trans('validate.password_regex'),
            'password.required'  =>  trans('validate.password_required'),
            'confirm_password.required'  =>  trans('validate.confirm_password_required'),
            'confirm_password.same' => trans('validate.confirm_password_same'),
        ];
    }
}

