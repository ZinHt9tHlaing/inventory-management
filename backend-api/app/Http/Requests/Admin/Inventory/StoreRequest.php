<?php

namespace App\Http\Requests\Admin\Inventory;

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
            "product_id" => ["required", "exists:products,id"],
            "warehouse_id" => ["required", "exists:warehouses,id"],
            "quantity" => ["required", "integer", "min:0"],
        ];
    }

    public function messages()
    {
        return [
            "product_id.required" => "Product is required",
            "product_id.exists" => "Product does not exist",
            "warehouse_id.required" => "Warehouse is required",
            "warehouse_id.exists" => "Warehouse does not exist",
            "quantity.required" => "Quantity is required",
            "quantity.integer" => "Quantity must be an integer",
            "quantity.min" => "Quantity must be greater than or equal to 0",
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
