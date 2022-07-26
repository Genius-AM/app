<?php

namespace App\Http\Requests\Lists\Car;

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
            'name' => 'required|string',
            'car_number' => 'required|string',
            'owner_name' => 'required_if:owner,partner',
            'owner' => 'required|string',
            'category_id' => 'required|integer|exists:categories,id',
            'order' => 'required|integer',
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
            'name' => 'Название',
            'car_number' => 'Гос. номер',
            'owner_name' => 'Имя партнера',
            'owner' => 'Владелец транспорта',
            'category_id' => 'Категория ТС',
            'order' => 'Сортировка',
            'default_seats_on_vehicle' => 'Максимальное число мест по умолчанию',
            'men' => 'Мужчины (Взрослые)',
            'women' => 'Женщины',
            'kids' => 'Дети',
        ];
    }
}
