<?php

namespace App\Http\Requests\Lists\Subcategory;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;

class StoreRequest extends FormRequest
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
            'category_id' => 'required|integer|exists:categories,id',
            'name' => 'required|string|max:255',
            'men' => 'required|integer',
            'women' => 'required|integer',
            'kids' => 'required|integer',
        ];
    }

    public function attributes()
    {
        return [
            'category_id' => 'Категория',
            'name' => 'Название',
            'men' => 'Мужчины (Взрослые)',
            'women' => 'Женщины',
            'kids' => 'Дети',
        ];
    }
}
