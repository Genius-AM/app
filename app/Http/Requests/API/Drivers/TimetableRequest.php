<?php

namespace App\Http\Requests\API\Drivers;

use Illuminate\Foundation\Http\FormRequest;

class TimetableRequest extends FormRequest
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
        return [

        ];
    }

    /**
     * Configure the validator instance.
     *
     * @param \Illuminate\Validation\Validator $validator
     * @return void
     */
    public function withValidator($validator)
    {
        $validator->after(function ($validator) {
            if ($this->emptyCar()) {
                $validator->errors()->add('car', 'У водителя нет машины!');
            }
        });
    }

    /**
     * @return bool
     */
    public function emptyCar()
    {
        if ($this->user()->car) {
            return false;
        }

        return true;
    }
}
