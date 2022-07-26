<?php

namespace App\Core\Categories\Djipping;

use App\Category;
use App\Core\Categories\ChangeAmountMethod;
use App\Excursion;
use App\PassengersAmountInExcursion;
use App\Route;
use Illuminate\Support\Carbon;

class DjippingChangeAmount implements ChangeAmountMethod
{
    /**
     * @var
     */
    private $date;

    /**
     * @var
     */
    private $time;

    /**
     * @var Route
     */
    private $route;

    /**
     * @var
     */
    private $amount;

    /**
     * SeaChangeAmount constructor.
     * @param array $array
     */
    public function __construct(array $array)
    {
        $this->setData($array);
    }

    /**
     * @param array $array
     * @return mixed|void
     */
    public function setData(array $array)
    {
        if ($array) {
            $this->date = Carbon::createFromFormat('Y-m-d\TH:i:sP', $array['DateTime'])->format('Y-m-d');
            $this->time = Carbon::createFromFormat('Y-m-d\TH:i:sP', $array['DateTime'])->format('H:i:s');
            $this->route = Route::findOrFail($array['route_id']);
            $this->amount['men'] = $array['amount_men'];
            $this->amount['women'] = $array['amount_women'];
            $this->amount['kids'] = $array['amount_kids'];
        }
    }

    /**
     * @return bool
     */
    public function changeAmount() : bool
    {
        $data = PassengersAmountInExcursion::where('route_id', $this->route->id)
            ->where('date',$this->date)
            ->where('time',$this->time)
            ->first();

        $result_changing_excursion = self::changeAmountInExcursionIfExist();
        if (!$data) {
            $data = new PassengersAmountInExcursion();
        }

        if ($result_changing_excursion) {
            $result = $this->changePassengersDataInTable($data);
        } else {
            $result = false;
        }

        return $result;
    }

    /**
     * Если экскурсия есть, то сохранем новую емкость
     *
     * @return bool
     */
    private function changeAmountInExcursionIfExist()
    {
        $excursion = Excursion::where('route_id', $this->route->id)
            ->where('date', $this->date)
            ->where('time', $this->time)
            ->first();

        if(!empty($excursion)) {
            $amount = (int) $this->amount['men'] + (int) $this->amount['women'] + (int) $this->amount['kids'];
            $people = (int) $excursion->people;
            if($amount < $people){
                return false;
            } else {
                $excursion->capacity = $amount;
                $excursion->save();

                return true;
            }
        } else {
            return true;
        }
    }

    /**
     * Изменение данных в таблице
     *
     * @param PassengersAmountInExcursion $instance
     * @param $request
     * @param $date
     * @param $time
     * @return boolean
     */
    private function changePassengersDataInTable(PassengersAmountInExcursion $instance)
    {
        $instance->category_id = Category::DJIPPING;
        $instance->subcategory_id = $this->route->subcategory_id;
        $instance->route_id = $this->route->id;
        $instance->date = $this->date;
        $instance->time = $this->time;
        $instance->amount_men = $this->amount['men'];
        $instance->amount_women = $this->amount['women'];
        $instance->amount_kids = $this->amount['kids'];
        $instance->amount = $this->amount['men'] + $this->amount['women'] + $this->amount['kids'];
        $result = $instance->save();

        return $result;
    }
}