<?php

namespace App\Http\Requests\Lists\AgeCategory;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;

class EditRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return Auth::check();
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'from' => 'nullable|integer',
            'to' => 'nullable|integer',
        ];
    }

    /**
     * @param $validator
     */
    public function withValidator($validator)
    {
        $validator->after(function ($validator) {
            if (!$this->input('from') && !$this->input('to')) {
                $validator->errors()->add('error', 'Необходимо выбрать "От" и/или "До"!');
            }
        });
    }
}
