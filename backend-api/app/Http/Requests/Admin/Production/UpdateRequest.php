<?php

namespace App\Http\Requests\Admin\Production;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\Exceptions\HttpResponseException;

class UpdateRequest extends FormRequest
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
            "product_id" => ["sometimes", "exists:products,id"],
            "warehouse_id" => ["sometimes", "exists:warehouses,id"],
            "quantity_produced" => ["sometimes", "integer", "min:1"],
        ];
    }

    public function messages()
    {
        return [
            "product_id.exists" => "Product does not exist",
            "warehouse_id.exists" => "Warehouse does not exist",
            "quantity_produced.integer" => "Quantity produced must be an integer",
            "quantity_produced.min" => "Quantity produced must be greater than or equal to 1",
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
