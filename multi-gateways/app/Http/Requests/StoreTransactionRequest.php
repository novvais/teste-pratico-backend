<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreTransactionRequest extends FormRequest
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
            'client_id' => 'required|uuid|exists:clients,id',
            'amount' => 'required|integer|min:0',
            'status' => 'required|string',
            'card' => 'required|array:last_four,expiration_month,expiration_year',
            'card.last_four' => 'required|string|max:4',
            'card.expiration_month' => 'required|string|digits:2',
            'card.expiration_year' => 'required|string|digits:4',
            'products' => 'required|array:id,quantity',
            'products.*.id' => 'required|integer|exists:products,id',
            'products.*.quantity' => 'required|integer|min:1'
        ];
    }

    public function messages(): array 
    {
        return [
            'client_id.required' => 'Please provide a client_id.',
            'amount.required' => 'The price is required.',
            'amount.min' => 'The price must be at least 0.',
            'status.required' => 'The status field is required.',
        ];
    }
}
