<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class InternalTransferRequest extends FormRequest
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
        $transferMode = $this->input('transferMode');
        
        $rules = [
            'transferMode' => 'required',
            'from_meta_login' => $transferMode === '1' || $transferMode === '2' ? 'required' : 'nullable',
            'to_meta_login' => $transferMode === '0' || $transferMode === '2' ? 'required' : 'nullable',
            'amount' => 'required|numeric|min:1',
        ];

        if($transferMode === '2') {
            $rules['to_meta_login'] .= '|different:from_meta_login';
        }

        return $rules;
    }

    public function attributes(): array
    {
        return [
            'transferMode' => trans('public.transfer_mode'),
            'from_meta_login' => trans('public.from_trading_acc'),
            'to_meta_login' => trans('public.to_trading_acc'),
            'amount' => trans('public.amount'),
        ];
    }
}
