<?php

namespace App\Http\Requests\Lists\Category;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;

class UpdateRequest extends FormRequest
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
            'default_seats_on_vehicle' => 'required|integer',
            'men' => 'required|integer',
            'women' => 'required|integer',
            'kids' => 'required|integer',
        ];
    }

    public function withValidator($validator)
    {
        $validator->after(function ($validator) {
            if ($this->input('default_seats_on_vehicle') != ($this->input('men') + $this->input('women') + $this->input('kids'))) {
                $validator->errors()->add('default_seats_on_vehicle', 'Не верно указано общее количество!');
            }
        });
    }

    public function attributes()
    {
        return [
            'default_seats_on_vehicle' => 'Мест по умолчанию',
            'men' => 'Мужчины (Взрослые)',
            'women' => 'Женщины',
            'kids' => 'Дети',
        ];
    }
}
