<?php

namespace App\Http\Requests;

use App\Excursion;
use App\Http\Controllers\API\HelperController;
use Carbon\Carbon;
use Illuminate\Foundation\Http\FormRequest;

class ChangeAmountRequest extends FormRequest
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
            'route_id' => 'required|integer|exists:routes,id',
            'DateTime' => 'required|date',
            'amount_men' => 'required|integer',
            'amount_women' => 'required|integer',
            'amount_kids' => 'required|integer'
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

            if ($excursion = $this->getExcursion()) {
                $amount = HelperController::amountPeoplesInExcursion($excursion);

                if ($this->emptyAmountMen($amount)) {
                    $validator->errors()->add('men', 'Недопустимое значение для мужчин!');
                }
                if ($this->emptyAmountWomen($amount)) {
                    $validator->errors()->add('women', 'Недопустимое значение для женщин!');
                }
                if ($this->emptyAmountKids($amount)) {
                    $validator->errors()->add('kids', 'Недопустимое значение для детей!');
                }
            }
        });
    }

    /**
     * @return Excursion|\Illuminate\Database\Eloquent\Model|object|null
     */
    private function getExcursion()
    {
        return Excursion::where('route_id', $this->input('route_id'))
            ->where('car_id', $this->user()->cars()->first()->id)
            ->where('date', Carbon::parse($this->input('DateTime'))->format('Y-m-d'))
            ->where('time', Carbon::parse($this->input('DateTime'))->format('H:i:s'))
            ->first();
    }

    /**
     * @param $amount
     * @return bool
     */
    private function emptyAmountMen($amount)
    {
        if ($this->input('amount_men') and $this->input('amount_men') > $amount['men']) {
            return false;
        }

        return true;
    }

    /**
     * @param $amount
     * @return bool
     */
    private function emptyAmountWomen($amount)
    {
        if ($this->input('amount_women') and $this->input('amount_women') > $amount['women']) {
            return false;
        }

        return true;
    }

    /**
     * @param $amount
     * @return bool
     */
    private function emptyAmountKids($amount)
    {
        if ($this->input('amount_kids') and $this->input('amount_kids') > $amount['kids']) {
            return false;
        }

        return true;
    }
}

