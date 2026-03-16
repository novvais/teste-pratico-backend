<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreCheckoutRequest extends FormRequest
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
            'client_name' => 'required|string',
            'client_email' => 'required|string|email:rfc,dns',
            'card' => 'required|array',
            'card.last_four' => 'required|string|max:4',
            'card.expiration_month' => 'required|string|digits:2',
            'card.expiration_year' => 'required|string|digits:4',
            'card.cvv' => 'required|string|max:3',
            'products' => 'required|array',
            'products.*.id' => 'required|integer|exists:products,id',
            'products.*.quantity' => 'required|integer|min:1'
        ];
    }

    public function messages(): array 
    {
        return [
            'client_email.required' => 'Please provide a email.',
            'client_name.required' => 'Please provide a name.'
        ];
    }
}
