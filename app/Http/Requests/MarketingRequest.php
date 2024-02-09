<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Request;

class MarketingRequest extends FormRequest
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
        if($this->type == 1) {
            $rules  =   [
                'type' => 'required|integer',
                'name' => 'required',
                'media_name' => 'required|mimes:jpg,png,gif,jpeg',
            ];
        } else {
            $rules  =   [
                'type' => 'required|integer',
                'name' => 'required',
                'url_link' => 'required|url',
            ];
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
            'type.required'  => 'The media type field is required.',
            'url_link.required'  => 'The youtube link field is required.',
        ];
    }

    // protected function getValidatorInstance() {
    //     $validator = parent::getValidatorInstance();
    //     $validator->sometimes('type', 'required|integer', function($input) {
    //         return $input->type_id == 3;
    //     });
    //     return $validator;
    // }

    protected function failedValidation(\Illuminate\Contracts\Validation\Validator $validator)
    {
        \Illuminate\Support\Facades\Session::flash('ValidatorError', 'Please check the required fields and complete them.');
        return parent::failedValidation($validator);
    }
}
