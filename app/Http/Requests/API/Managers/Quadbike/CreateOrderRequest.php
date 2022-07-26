<?php

namespace App\Http\Requests\API\Managers\Quadbike;

use App\BookedTime;
use App\Cars;
use App\Category;
use App\Models\Company;
use App\PassengersAmountInExcursion;
use App\Route;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Carbon;

class CreateOrderRequest extends FormRequest
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
            'company_id' => 'required|integer|exists:companies,id',
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
            if ($this->emptyAmount()) {
                $validator->errors()->add('amount', 'Должен быть хотя бы 1 человек в заявке!');
            }
            if ($this->emptyCompanyDriver()) {
                $validator->errors()->add('driver', 'Нет водителя у компании!');
            }
            if ($this->emptyCompanyCar()) {
                $validator->errors()->add('car', 'Нет привязанной машины у компании!');
            }
            if ($this->limitAmount()) {
                $validator->errors()->add('amount', 'Заявка не создана. Мест меньше, чем вы отправили в запросе!');
            }
            if ($this->timeBooked()) {
                $validator->errors()->add('time_booked', 'Данное место забронировано!');
            }
            if ($this->uncurrentDayAndTime()) {
                $validator->errors()->add('date_time', 'Данное время недоступно для добавления!');
            }
            if($this->ageCategory()) {
                $validator->errors()->add('age_categories', 'Не верно указаны категории возрастов!');
            }
        });
    }

    /**
     * @return bool
     */
    private function emptyCompanyDriver()
    {
        if ($this->filled('company_id')) {
            try {
                $company = Company::findOrFail($this->input('company_id'));
                if (!$company->driver) {
                    return true;
                }

            } catch (\Exception $exception) {
                return false;
            }
        }

        return false;
    }

    /**
     * @return bool
     */
    private function emptyCompanyCar()
    {
        if ($this->filled('company_id')) {
            try {
                $company = Company::findOrFail($this->input('company_id'));
                if (!$company->driver->cars->first()) {
                    return true;
                }

            } catch (\Exception $exception) {
                return false;
            }
        }

        return false;
    }

    /**
     * @return bool
     */
    private function limitAmount()
    {
        try {
            $amount_people = (int)$this->request->get('men') + (int)$this->request->get('women') + (int)$this->request->get('kids');
            $date = Carbon::createFromFormat('Y-m-d\TH:i:sP', $this->request->get('dateTime'))->format('Y-m-d');
            $time_to_check = Carbon::createFromFormat('Y-m-d\TH:i:sP', $this->request->get('dateTime'))->format('H:i:s');

            $chosenCar = Cars::where('category_id', Category::QUADBIKE)
                ->with('driver')
                ->first();

            $passengers_amount_from_table = PassengersAmountInExcursion::where('route_id', $this->input('route_id'))
                ->where('date', $date)
                ->where('time', $time_to_check)
                ->first();

            $to_check_capacity = !empty($passengers_amount_from_table) ? $passengers_amount_from_table->getAmount() : $chosenCar->passengers_amount;
            if ($amount_people > $to_check_capacity) {
                return true;
            }

            return false;
        } catch (\Exception $exception) {
            return false;
        }
    }

    /**
     * @return bool
     */
    private function timeBooked()
    {
        try {
            $time = Carbon::createFromFormat('Y-m-d\TH:i:sP', $this->request->get('dateTime'))->format('H:i');
            $date = \Carbon\Carbon::createFromFormat('Y-m-d\TH:i:sP', $this->request->get('dateTime'))->format('Y-m-d');
            $time_booked_to_check = Carbon::createFromFormat('Y-m-d\TH:i:sP', $this->request->get('dateTime'))->hour . ":" . explode(":", $time)[1];

            if (!$this->checkForCanAddOrder($this->input('route_id'), $date, $time_booked_to_check)) {
                return true;
            }

            return false;

        } catch (\Exception $exception) {
            return false;
        }
    }

    /**
     * @return bool
     */
    private function uncurrentDayAndTime()
    {
        try {
        $time = Carbon::createFromFormat('Y-m-d\TH:i:sP', $this->request->get('dateTime'))->format('H:i');
        $weekday = strtolower(Carbon::createFromFormat('Y-m-d\TH:i:sP', $this->request->get('dateTime'))->locale('en_En')->isoFormat('dddd'));
        $full_route_data = Route::where('id', $this->input('route_id'))
            ->with('days.times')
            ->first();
        if (count($full_route_data->days) > 0) {
            foreach ($full_route_data->days as $key => $value) {
                if ($value->weekday == $weekday) {
                    if (count($value->times) === 0)
                        return true;
                    else {
                        $check_to_save = false;
                        foreach($value->times as $k => $v) {
                            if( Carbon::createFromFormat('H:i', $v->name)->format('H:i') == $time ) {
                                $check_to_save = true;
                            }
                        }

                        if (!$check_to_save) {
                            return true;
                        }
                    }
                }
            }

            return false;
        }
        return true;

        } catch (\Exception $exception) {
            return false;
        }
    }

    /**
     * Проверка на возможность сохранения заявки
     *  исходя от брони
     * @param $route_id
     * @param $date
     * @param $time
     * @return bool
     */
    static private function checkForCanAddOrder($route_id, $date, $time)
    {
        $data = BookedTime::where('route_id', $route_id)
            ->where('date', $date)
            ->where('time', $time)
            ->first();

        if (empty($data)) {
            return true;
        } else {
            if ($data->booked === 1)  {
                return false;
            } else {
                return true;
            }
        }
    }

    /**
     * @return bool
     */
    private function emptyAmount()
    {
        return $this->input('men') + $this->input('women') + $this->input('kids') == 0;
    }

    /**
     * @return bool
     */
    private function ageCategory()
    {
        $amount_people = $this->input('men') + $this->input('women') + $this->input('kids');
        $age_categories = $this->input('age_categories');
        $amount_age_categories = is_array($age_categories) ? (int)array_sum(array_column($age_categories, 'amount')) : 0;

        return $amount_people != $amount_age_categories;
    }
}
