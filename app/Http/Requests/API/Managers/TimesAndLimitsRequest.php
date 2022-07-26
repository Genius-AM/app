<?php

namespace App\Http\Requests\API\Managers;

use Illuminate\Foundation\Http\FormRequest;

class TimesAndLimitsRequest extends FormRequest
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
            'route_id' => 'required|integer|exists:routes,id',
            'DateTime' => 'required|date',
        ];
    }
}
