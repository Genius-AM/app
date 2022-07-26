<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;

class CreateRouteRequest extends FormRequest
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
            'name' => 'required|string|max:25',
            'category_id' => 'required|integer|exists:categories,id',
            'subcategory_id' => 'required|integer|exists:subcategories,id',
            'prepayment' => 'required|integer',
            'duration' => 'nullable|date_format:H:i',
            'price_men' => 'required_unless:category_id,3|nullable|integer',
            'price_women' => 'required_unless:category_id,3|nullable|integer',
            'price_kids' => 'required_unless:category_id,3|nullable|integer',
            'price' => 'required_if:category_id,3|nullable|integer',
            'is_payable' => 'boolean'
        ];
    }
}
