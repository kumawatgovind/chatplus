<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use \Illuminate\Http\Request;

class StateRequest extends FormRequest
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
    public function rules(Request $request)
    {
        $rules = [];
        if ($request->isMethod("PATCH") && is_numeric($request->segment(3))) {
            $rules['name'] = 'required|unique:states,name,' . $request->segment(3);
        } else {
            $rules['name'] = 'required|unique:states,name';
        }
        return $rules;
    }

    /**
     * Get the error messages for the defined validation rules.
     *
     * @return array
     */
    public function messages()
    {
        return [
            'name.required'  => 'Please enter name!',
        ];
    }



    protected function failedValidation(\Illuminate\Contracts\Validation\Validator $validator)
    {
        \Illuminate\Support\Facades\Session::flash('ValidatorError', 'Please check the required fields and complete them.');
        return parent::failedValidation($validator);
    }
}
