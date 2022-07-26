<?php

namespace App\Http\Requests\Route;

use Illuminate\Foundation\Http\FormRequest;

class RouteCarCreateRequest extends FormRequest
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
            'car' => 'required|integer|exists:cars,id',
            'prepayment' => 'required|integer',
            'duration' => 'nullable|date_format:H:i',
            'price_men' => 'required_without:price|nullable|integer',
            'price_women' => 'required_without:price|nullable|integer',
            'price_kids' => 'required_without:price|nullable|integer',
            'price' => 'required_without_all:price_men,price_women,price_kids|nullable|integer',
            'is_payable' => 'boolean'
        ];
    }
}
