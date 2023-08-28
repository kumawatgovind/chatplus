<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use \Illuminate\Http\Request;

class PageRequest extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules(Request $request)
    {
        //dd($request->input());
        //dd($request->segment(3));
        $rules  =   [
            'title' => 'required|regex:/^[\pL\s\-]+$/u|min:3|max:150|unique:pages',
            // 'slug' => 'required|unique:pages,slug,' . $request->segment(3),
            //'description' => 'min:5',
            // 'position[]' => 'required',
            'description' => 'required|min:50',
            // 'meta_title' => 'required|max:150',
            // 'meta_keyword' => 'required|max:150',
            // 'meta_description' => 'required|max:150',
        ];
        if ($request->isMethod("PATCH") && is_numeric($request->segment(3))) {
            $rules['title'] = 'required|regex:/^[\pL\s\-]+$/u|min:2|max:100|unique:pages,title,' . $request->segment(3);
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
            'title.required'  => 'Please enter page title!',
            'title.min'  => 'Page Title must be at least 2 characters long!',
            'slug.required' => 'Please enter page slug!',
            'slug.unique' => 'Each cms pages must have a unique slug! this page slug already created!',
            'description.required'  => 'Please enter description!',
            'description.min'  => 'Description must be at least 50 characters long!',
            'meta_title.required'  => 'Please enter meta title!',
            'meta_keyword.required'  => 'Please enter meta keyword!',
            'meta_description.required'  => 'Please enter meta description!',
        ];
    }

    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return true;
    }

    protected function failedValidation(\Illuminate\Contracts\Validation\Validator $validator)
    {
        \Illuminate\Support\Facades\Session::flash('ValidatorError', 'Please check the required fields and complete them.');
        return parent::failedValidation($validator);
    }
}
