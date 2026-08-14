<?php

namespace App\Http\Requests\Admin\Auth;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Validation\Rule;

class RegisterRequest extends FormRequest
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
            'name' => 'required',
            "email" => ['required', Rule::unique("users", "email")],
            "password" => ["required", "string", "min:6"],
            "role" => ["required", Rule::in(["admin", "manager"])],
        ];
    }

    public function messages(): array
    {
        // same as attribute name in attributes method, then the :attribute will replace with attribute name
        return [
            "name.required" => "Please enter your :attribute.",
            "email.required" => "Please enter your :attribute.",
            "email.email" => "Please enter a valid :attribute.",
            "password.required" => "Please enter your :attribute.",
            "password.min" => ":attribute must be at least 6 characters",
            "role.required" => "Please enter your role.",
            "role.in" => ":attribute must be admin or manager",
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
