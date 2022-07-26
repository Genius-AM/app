<?php

namespace App\Http\Requests\Lists\Promotion;

use App\Category;
use Illuminate\Foundation\Http\FormRequest;

class CreateRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return request()->user()->isAdmin() ||
            (request()->user()->isRole('dispatcher') && request()->user()->category_id == Category::DJIPPING);
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'category_id' => 'required|integer|exists:categories,id',
            'subcategory_id' => 'nullable|integer|exists:subcategories,id',
            'text' => 'required|string',
        ];
    }
}
