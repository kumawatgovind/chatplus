<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use \Illuminate\Http\Request;

class SettingRequest extends FormRequest
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
        $rule = [
            'title'         => 'required|regex:/^[\pL\s\-]+$/u|min:2|max:100',
            'slug'          => 'nullable|alpha_dash|unique:settings,slug',
            'field_type'    => 'required',
            'config_value'  => 'required'
        ];
        if ($request->isMethod("PATCH") && is_numeric($request->segment(3))) {
            $rule['slug']      = 'alpha_dash|unique:settings,slug,' . $request->segment(3);
        }
        return $rule;
    }

    /**
     * Handle a failed validation attempt.
     *
     * @param  \Illuminate\Validation\Validator  $validator
     * @return mixed
     */

    protected function failedValidation(\Illuminate\Contracts\Validation\Validator $validator)
    {
        \Illuminate\Support\Facades\Session::flash('ValidatorError', 'Please check the required fields and complete.');
        return parent::failedValidation($validator);
    }
}
