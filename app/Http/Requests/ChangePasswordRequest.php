<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules\Password; // Import Password rule
use Illuminate\Contracts\Validation\Validator; // Import Validator
use Illuminate\Http\Exceptions\HttpResponseException; // Import Exception

class ChangePasswordRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true; // Handled by route middleware
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array|string>
     */
    public function rules(): array
    {
        return [
            'current_password' => ['required', 'string', function ($attribute, $value, $fail) {
                if (!Hash::check($value, $this->user()->password)) {
                    $fail('The provided current password does not match your actual current password.');
                }
            }],
            'new_password' => [
                'required',
                'string',
                Password::min(8) 
                    ->mixedCase() 
                    ->numbers() 
                    ->symbols(), 
                'confirmed', 
                'different:current_password',
            ],
             'new_password_confirmation' => ['required'], 
        ];
    }

    /**
     * Add custom messages for validation errors.
     *
     * @return array
     */
    public function messages(): array
    {
        return [
            'new_password.different' => 'The new password must be different from the current password.',
        ];
    }

    /**
     * Handle a failed validation attempt.
     * Override to ensure JSON response for API.
     *
     * @param  \Illuminate\Contracts\Validation\Validator  $validator
     * @return void
     *
     * @throws \Illuminate\Http\Exceptions\HttpResponseException
     */
    protected function failedValidation(Validator $validator)
    {
        throw new HttpResponseException(response()->json([
            'message' => 'The given data was invalid.',
            'errors' => $validator->errors(),
        ], 422));
    }
}