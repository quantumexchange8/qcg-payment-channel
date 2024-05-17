<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

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
        return [
            'transferMode' => ['required'],
            'from_meta_login' => ['exclude_unless:transferMode,2','required'],
            'to_meta_login' => ['exclude_unless:transferMode,2','required','different:from_meta_login'],
            'amount' => ['required', 'numeric', 'min:1'],
        ];
    }

    public function attributes(): array
    {
        return [
            'transferMode' => 'Transfer Mode',
            'from_meta_login' => 'From Trading Account',
            'to_meta_login' => 'To Trading Account',
            'amount' => 'Amount',
        ];
    }
}
