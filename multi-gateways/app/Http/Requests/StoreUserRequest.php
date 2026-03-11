<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreUserRequest extends FormRequest
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
            'email' => 'required|string|email:rfc,dns|unique:users',
            'password' => 'required|string|min:8'
        ];
    }

    public function messages(): array
    {
        return [
            'email.required' => 'An email is required.',
            'email.email' => 'Please provide a valid email address.',
            'email.unique' => 'This email is already registred.',
            'password.required' => 'A password is required.',
            'password.min' => 'The password must be at least  8 characters.'
        ];
    }
}
