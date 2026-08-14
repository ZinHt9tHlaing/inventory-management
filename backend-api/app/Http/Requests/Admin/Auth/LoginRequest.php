<?php

namespace App\Http\Requests\Admin\Auth;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\Exceptions\HttpResponseException;

class LoginRequest extends FormRequest
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
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            "email" => ["required", "email"],
            "password" => ["required", "string", "min:6"],
        ];
    }

    public function messages(): array
    {
        // same as attribute name in attributes method, then the :attribute will replace with attribute name
        return [
            "email.required" => "Please enter your :attribute.",
            "email.email" => "Please enter a valid :attribute.",
            "password.required" => "Please enter your :attribute.",
            "password.min" => ":attribute must be at least 6 characters",
        ];
    }

    protected function failedValidation(Validator $validator)
    {
        throw new HttpResponseException(response()->json([
            "message" => "Validation failed",
            "errors" => $validator->errors(),
        ], 422));
    }
}
