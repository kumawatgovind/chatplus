<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use App\Rules\CheckPhone;

class ContactRequest extends FormRequest
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
     * regex:/2547[0-9]{6}/
     * 
     * ^(254|0)([7][0-9]|[1][0-1]){1}[0-9]{1}[0-9]{6}$
     * 
     * @return array
     */
    public function rules()
    {
        $rule['subject']        = 'required';
        $rule['name']           = 'required|string|min:2|max:100';
        $rule['email']          = 'required|email:rfc,dns';
        $rule['phone']          = ['required', 'numeric', 'min:10', new CheckPhone];
        $rule['message']        = 'required|string';
        return $rule;
    }

    /**
     * Get the error messages for the defined validation rules.
     *
     * @return array
     */
    public function messages()
    {
        return [
            'first_name.required'   => 'The name is required field.',
            'email.required'        => 'The email  is required field',
            'phone.required'        => 'The phone is required field',
            'phone.regex'           => 'The phone format is not valid use valid number 2547XXXXXXXXX.',
        ];
    }
}
