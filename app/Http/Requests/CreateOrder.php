<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class CreateOrder extends FormRequest
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
            'category_id' => 'required|integer',
            'subcategory_id' => 'required|integer',
            'date' => 'required|date',
            'time' => 'required|string|max:5',
            'men' => 'required|integer',
            'women' => 'required|integer',
            'kids' => 'required|integer',
            'price' => 'required|integer',
            'prepayment' => 'required|integer',
            'name' => 'required|string|max:25',
            'phone' => 'required|string|max:25',
            'phone_2' => 'nullable|string|max:25',
            'address' => 'required|string|max:50',
            'comment' => 'nullable|string|max:150',
        ];
    }
}
