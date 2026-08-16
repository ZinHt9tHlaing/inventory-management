<?php

namespace App\Http\Requests\Admin\User;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\Exceptions\HttpResponseException;

class StoreRequest extends FormRequest
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
            "name" => "required|string|max:255",
            "email" => "required|string|unique:users,email",
            "password" => "required|string|min:8",
            "role" => "required|in:admin,manager,user",
        ];
    }

    public function messages()
    {
        return parent::messages() + [
            "name.required" => "Name is required",
            "name.string" => "Name must be a string",
            "name.max" => "Name must be less than 255 characters",
            "email.required" => "Email is required",
            "email.unique" => "Email already exists",
            "password.min" => "Password must be at least 8 characters",
            "password.required" => "Password is required",
            "role.required" => "Role is required",
            "role.in" => "Role must be admin, manager, or user",
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
