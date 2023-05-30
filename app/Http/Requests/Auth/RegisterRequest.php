<?php

namespace App\Http\Requests\Auth;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Request;
use Laravel\Fortify\Rules\Password;
use App\Rules\MinWordsRule;
use App\Rules\CheckPhone;
use Laravel\Jetstream\Jetstream;
use Session;

class RegisterRequest extends FormRequest
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
     * Get the validation rules that apply to the request. not_throw_away
     *
     * @return array
     */
    public function rules(Request $request)
    {
        $rules = [
            'first_name' => ['required', 'regex:/^[\pL\s\-]+$/u', 'max:255'],
            'last_name' => ['nullable', 'regex:/^[\pL\s\-]+$/u', 'max:255'],
            // 'email'  => ['required', 'string', 'email','regex:/^([a-z0-9\+_\-]+)(\.[a-z0-9\+_\-]+)*@([a-z0-9\-]+\.)+[a-z]{2,6}$/ix','max:255', 'unique:users'],
            'email'  => ['required', 'string', 'email:rfc,dns','max:255', 'unique:users'],
            'password' => ['required', 'min:6', 'confirmed'],
            'terms'    => Jetstream::hasTermsAndPrivacyPolicyFeature() ? ['required', 'accepted'] : '',
            'phone' => ['required', 'numeric', 'min:10', 'unique:users,phone', new CheckPhone]
        ];
        // /254[0-9]{9}/
        // if(empty(Session::get('OTPVerified'))){
        //     $rules['otp'] = ['required'];
        // }
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
            'first_name.required'   => 'The Name is required field.',
            'email.required'        => 'The Email ID is required field.',
            'email.unique'          => 'This Email ID have already created. please use another email ID.',
            'phone.required'        => 'The phone is required field.',
            'phone.regex'           => 'The phone format is not valid use valid number 2547XXXXXXXXX.',
            'phone.unique'          => 'This phone have already registered. please use another phone.',
            'password.required'     => 'The Password is required field.',
            'password.regex'        => 'The Password must be at least 6 characters long, contain at least one number, one special character and have a mixture of uppercase and lowercase letters.',
            'dob.required'          => 'The Date of birth is required field.',
            'role_id.required'      => 'The Privilege is required field.',
        ];
    }

}
