<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class DepositRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'meta_login' => ['required'],
            'amount' => ['required', 'numeric', 'min:1'],
        ];
    }

    public function attributes(): array
    {
        return [
            'meta_login' => trans('public.trading_account'),
            'amount' => trans('public.amount'),
        ];
    }
}
