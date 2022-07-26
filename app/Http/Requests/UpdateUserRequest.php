<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;

class UpdateUserRequest extends FormRequest
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
            'name'        => 'required|string|max:25',
            'phone'       => 'required|max:25', //alpha_num
            'role_id'     => 'required|integer|max:3|exists:roles,id',
            'balance'     => 'numeric|max:25',
            'region'      => 'nullable|string|max:25',
            'address'     => 'nullable|string|max:50',
            'password'    => 'nullable|confirmed|string|min:6|max:25',
            'category_id' => 'integer|max:4|exists:categories,id',
            'company_id'  => 'required_if:category_id,3'
        ];
    }
}
