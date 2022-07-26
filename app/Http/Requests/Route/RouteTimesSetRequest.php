<?php

namespace App\Http\Requests\Route;

use Illuminate\Foundation\Http\FormRequest;

class RouteTimesSetRequest extends FormRequest
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
            'datetime' => 'sometimes|array|distinct',
            'datetime.*' => 'required|date',
            'datetime_amount' => 'sometimes|array',
            'datetime_amount.*' => 'required|integer',
        ];
    }
}
