<?php

namespace App\Http\Requests\API\Drivers;

use Illuminate\Foundation\Http\FormRequest;

class ExcursionCancelRequest extends FormRequest
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
            'excursion_id' => 'required|integer|exists:excursions,id',
            'message' => 'required|string'
        ];
    }
}
