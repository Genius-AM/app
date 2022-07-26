<?php

namespace App\Http\Requests\API\Managers\Sea;

use App\BookedTime;
use App\Cars;
use App\Category;
use App\PassengersAmountInExcursion;
use App\Route;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Carbon;

class CreateOrderRequest extends FormRequest
{
    protected $date;

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
            'DateTime' => 'required|date',
            'men' => 'nullable|integer',
            'women' => 'nullable|integer',
            'kids' => 'nullable|integer',
            'client_name' => 'required|string',
            'client_comment' => 'nullable|string',
            'client_prepayment' => 'required|integer',
            'client_phone' => 'required|string',
            'client_phone_2' => 'nullable|string',
            'route_id' => 'required|integer|exists:routes,id',
            'client_food' => 'boolean',
            'client_address_id' => 'nullable|integer|exists:addresses,id',
            'client_point_id' => 'required|integer|exists:addresses,id',
            'age_categories' => 'required|array',
            'age_categories.*.id' => 'required|integer|exists:age_categories,id',
            'age_categories.*.amount' => 'required|integer',
        ];
    }

    /**
     * Configure the validator instance.
     *
     * @param \Illuminate\Validation\Validator $validator
     * @return void
     */
    public function withValidator($validator)
    {
        $validator->after(function ($validator) {
            $this->date = Carbon::createFromFormat('Y-m-d\TH:i:sP', $this->input('DateTime'));

            $chosenCar = Cars::where('category_id', Category::SEA)
                ->whereHas('timetables', function (Builder $query) {
                    $query->where('day', '=', strtolower($this->date->englishDayOfWeek))
                        ->where('time', '=', $this->date->format('H:i:s'))
                        ->where('route_id', '=', $this->input('route_id'))
                        ->where(function (Builder $query) {
                            $query->whereNull('date')
                                ->orWhere('date', '=', $this->date->format('Y-m-d'));
                        });
                })
                ->with('driver')
                ->first();

            if ($this->emptyAmount()) {
                $validator->errors()->add('amount', 'Должен быть хотя бы 1 человек в заявке!');
            }
            if ($this->limitAmount($chosenCar)) {
                $validator->errors()->add('amount', 'Заявка не создана. Мест меньше, чем вы отправили в запросе!');
            }
            if ($this->timeBooked()) {
                $validator->errors()->add('time_booked', 'Данное место забронировано!');
            }
            if (empty($chosenCar)) {
                $validator->errors()->add('car', 'Нет привязанной машины!');
            }
            if (empty($chosenCar->driver)) {
                $validator->errors()->add('driver', 'Нет водителя!');
            }
            if($this->ageCategory()) {
                $validator->errors()->add('age_categories', 'Не верно указаны категории возрастов!');
            }
        });
    }

    /**
     * @param $chosenCar
     * @return bool
     */
    private function limitAmount($chosenCar): bool
    {
        try {
            $amount_people = (int)$this->input('men') + (int)$this->input('women') + (int)$this->input('kids');

            $passengers_amount_from_table = PassengersAmountInExcursion::where('route_id', $this->input('route_id'))
                ->where('date', $this->date->format('Y-m-d'))
                ->where('time', $this->date->format('H:i:s'))
                ->first();

            $to_check_capacity = !empty($passengers_amount_from_table) ? $passengers_amount_from_table->getAmount() : $chosenCar->passengers_amount;

            return $amount_people > $to_check_capacity;
        } catch (\Exception $exception) {
            return false;
        }
    }

    /**
     * @return bool
     */
    private function timeBooked(): bool
    {
        try {
            $time_booked_to_check = $this->date->hour . ":" . explode(":", $this->date->format('H:i'))[1];

            return !$this->checkForCanAddOrder($this->input('route_id'), $this->date->format('Y-m-d'), $time_booked_to_check);
        } catch (\Exception $exception) {
            return false;
        }
    }

    /**
     * Проверка на возможность сохранения заявки исходя от брони
     * @param $route_id
     * @param $date
     * @param $time
     * @return bool
     */
    static private function checkForCanAddOrder($route_id, $date, $time): bool
    {
        $data = BookedTime::where('route_id', $route_id)
            ->where('date', $date)
            ->where('time', $time)
            ->first();

        return !($data && $data->booked === 1);
    }

    /**
     * @return bool
     */
    private function emptyAmount(): bool
    {
        return $this->input('men') + $this->input('women') + $this->input('kids') == 0;
    }

    /**
     * @return bool
     */
    private function ageCategory(): bool
    {
        $amount_people = $this->input('men') + $this->input('women') + $this->input('kids');
        $age_categories = $this->input('age_categories');
        $amount_age_categories = is_array($age_categories) ? (int)array_sum(array_column($age_categories, 'amount')) : 0;

        return $amount_people != $amount_age_categories;
    }
}
