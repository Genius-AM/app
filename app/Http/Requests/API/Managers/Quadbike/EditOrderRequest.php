<?php

namespace App\Http\Requests\API\Managers\Quadbike;

use App\BookedTime;
use App\Cars;
use App\Category;
use App\Excursion;
use App\ExcursionOrder;
use App\Http\Controllers\API\HelperController;
use App\Models\Company;
use App\Order;
use App\PassengersAmountInExcursion as PassengersAmountInExcursionAlias;
use App\Route;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Config;

class EditOrderRequest extends FormRequest
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
            'order_id' => 'required|integer|exists:orders,id',
            'company_id' => 'required|integer|exists:companies,id',
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
            if ($this->emptyAmount()) {
                $validator->errors()->add('amount', 'Должен быть хотя бы 1 человек в заявке!');
            }
            if ($this->emptyCompanyDriver()) {
                $validator->errors()->add('driver', 'Нет водителя у компании!');
            }
            if ($this->emptyCompanyCar()) {
                $validator->errors()->add('car', 'Нет привязанной машины у компании!');
            }
            if($this->ageCategory()) {
                $validator->errors()->add('age_categories', 'Не верно указаны категории возрастов!');
            }

            try {
                $date = \Carbon\Carbon::createFromFormat('Y-m-d\TH:i:sP', $this->input('DateTime'))->format('Y-m-d');
                $time = \Carbon\Carbon::createFromFormat('Y-m-d\TH:i:sP', $this->input('DateTime'))->format('H:i');
                $time_to_check = HelperController::getTimeWithGoodSeconds($time);

                $chosenCar = Cars::where('category_id', Category::QUADBIKE)
                    ->with('driver')
                    ->first();

                /** @var Excursion $excursion */
                $excursion = Excursion::where('date', $date)
                    ->where('time', $time_to_check)
                    ->where('car_id', $chosenCar->id)
                    ->with('orders')
                    ->first();

                $passengers_amount_from_table = PassengersAmountInExcursionAlias::where('route_id', $this->input('route'))
                    ->where('date', $date)
                    ->where('time', $time_to_check)
                    ->first();

                if(!empty($excursion)) {
                    if ($this->uncorrectRoute($excursion)) {
                        $validator->errors()->add('order', 'Заявка к заказу не назначена, так как заявка и рейс имеют разные маршруты!');
                    }
                    if ($this->countLimit($excursion)) {
                        $validator->errors()->add('order', 'Заявка к заказу не назначена, привышен лимит на количество заявок!');
                    }

                    if ($this->availableSeats($passengers_amount_from_table, $excursion)) {
                        $validator->errors()->add('availableSeats', 'Заявка к заказу не назначена. Мест меньше чем вы запросили!');
                    }

                } else {
                    if ($this->limitAmount()) {
                        $validator->errors()->add('amount', 'Экскурсия не создана. Мест меньше, чем вы отправили в запросе!');
                    }
                }

                if ($this->badStatus()) {
                    $validator->errors()->add('status', 'Редактировать заявки можно только со статусом Принят, Отказ, Отказ после принятия и Сформирован!');
                }

                if ($this->uncurrentDayAndTime()) {
                    $validator->errors()->add('date_time', 'Данное время недоступно для обновления!');
                }

            } catch (\Exception $exception) {

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
    private function uncurrentDayAndTime()
    {
        try {
            $time = Carbon::createFromFormat('Y-m-d\TH:i:sP', $this->request->get('dateTime'))->format('H:i');
            $weekday = strtolower(Carbon::createFromFormat('Y-m-d\TH:i:sP', $this->request->get('dateTime'))->locale('en_En')->isoFormat('dddd'));
            $full_route_data = Route::where('id', $this->request->get('route'))
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
     * @return bool
     */
    private function limitAmount()
    {
        try {
            $amount_people = (int)$this->input('men') + (int)$this->input('women') + (int)$this->input('kids');
            $date = Carbon::createFromFormat('Y-m-d\TH:i:sP', $this->input('dateTime'))->format('Y-m-d');
            $time_to_check = Carbon::createFromFormat('Y-m-d\TH:i:sP', $this->input('dateTime'))->format('H:i:s');

            $chosenCar = Cars::where('category_id', 3)
                ->with('driver')
                ->first();

            $passengers_amount_from_table = PassengersAmountInExcursionAlias::where('route_id', $this->input('route'))
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
     * @param Excursion $excursion
     * @return bool
     */
    private function uncorrectRoute(Excursion $excursion)
    {
        if ($excursion->route_id != $this->input('route_id')) {
            return true;
        }

        return false;
    }

    /**
     * @param Excursion $excursion
     * @return bool
     */
    private function countLimit(Excursion $excursion)
    {
        $orders = ExcursionOrder::where('excursion_id', $excursion['id'])->get();
        $app_in_excur = (int) Config::get('constants.applications_in_excursion');

        if( count($orders) >= $app_in_excur ) {
            return true;
        }

        return false;
    }

    /**
     * @param $passengersAmountInExcursion
     * @param Excursion $excursion
     * @return bool
     */
    private function availableSeats($passengersAmountInExcursion, Excursion $excursion)
    {
        try {
            $order = Order::findOrFail($this->input('order_id'));
            $capacity = !empty($passengersAmountInExcursion) ? $passengersAmountInExcursion->getAmount() : $excursion->capacity;
            $amount_people = (int)$this->input('men') + (int)$this->input('women') + (int)$this->input('kids');

            if ($excursion->date != $order->date || $excursion->time != $order->time) {
                $availableSeats = $capacity - $excursion->people;
                if ($amount_people <= $availableSeats === false) {
                    return true;
                }
            } else {
                $old_amount_people = HelperController::getOldAmountPeople($excursion, $order);

                $availableSeats = $capacity - $excursion->people + $old_amount_people;
                if ($amount_people <= $availableSeats === false) {
                    return true;
                }
            }

            return false;
        } catch (\Exception $exception) {
            return false;
        }
    }

    /**
     * @return bool
     */
    private function badStatus()
    {
        $order = Order::findOrFail($this->input('order_id'));

        if ($order->status_id != 1 && $order->status_id != 3 && $order->status_id != 5 && $order->status_id != 8) {
            return true;
        }

        return false;
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
