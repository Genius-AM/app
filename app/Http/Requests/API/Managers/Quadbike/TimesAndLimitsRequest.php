<?php

namespace App\Http\Requests\API\Managers\Quadbike;

use App\Models\Company;
use Illuminate\Foundation\Http\FormRequest;

class TimesAndLimitsRequest extends FormRequest
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
            'company_id' => 'nullable|exists:companies,id',
            'DateTime' => 'required|date',
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
            if ($this->emptyCar()) {
                $validator->errors()->add('car', 'У водителя нет машины!');
            }
        });
    }

    private function emptyCar()
    {
        try {
            if ($this->filled('company_id')) {
                $company = Company::findOrFail($this->input('company_id'));
                if (!$company->driver->cars) {
                    return true;
                }

                return false;
            }
        } catch (\Exception $exception) {
            return true;
        }
    }
}
