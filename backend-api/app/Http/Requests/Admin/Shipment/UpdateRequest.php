<?php

namespace App\Http\Requests\Admin\Shipment;

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
            "warehouse_id" => ["required", "exists:warehouses,id"],
            "shipment_number" => ["required", "string", "max:255", "unique:shipments,shipment_number"],
            "shipped_at" => ["required", "date"],
            "shipment_items" => ["required", "array"],
            "shipment_items.*.product_id" => ["required", "exists:products,id"],
            "shipment_items.*.quantity" => ["required", "integer", "min:1"],
        ];
    }

    public function messages(): array
    {
        return [
            "warehouse_id.required" => "The warehouse field is required.",
            "warehouse_id.exists" => "The selected warehouse is invalid.",
            "shipment_number.required" => "The shipment number field is required.",
            "shipment_number.string" => "The shipment number must be a string.",
            "shipment_number.max" => "The shipment number may not be greater than :max characters.",
            "shipment_number.unique" => "The shipment number has already been taken.",
            "shipped_at.required" => "The shipped at field is required.",
            "shipped_at.date" => "The shipped at must be a date.",
            "shipment_items.required" => "The shipment items field is required.",
            "shipment_items.array" => "The shipment items must be an array.",
            "shipment_items.*.product_id.required" => "The product field is required.",
            "shipment_items.*.product_id.exists" => "The selected product is invalid.",
            "shipment_items.*.quantity.required" => "The quantity field is required.",
            "shipment_items.*.quantity.integer" => "The quantity must be an integer.",
            "shipment_items.*.quantity.min" => "The quantity must be at least 1.",
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
