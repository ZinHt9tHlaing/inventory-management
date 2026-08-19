<?php

namespace App\Http\Requests\Admin\Product;

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
            "price" => "required|numeric|min:0",
            "sku" => "required|string|max:255|unique:products,sku",
            "is_deleted" => "nullable|boolean|default:false",
            "supplier_id" => "required|exists:suppliers,id",
        ];
    }

    public function messages()
    {
        return [
            'name.required' => 'Name is required',
            'name.string' => 'Name must be a string',
            'name.max' => 'Name must be less than 255 characters',
            'price.required' => 'Price is required',
            'price.numeric' => 'Price must be a numeric',
            'price.min' => 'Price must be greater than or equal to 0',
            'sku.required' => 'Sku is required',
            'sku.string' => 'Sku must be a string',
            'sku.max' => 'Sku must be less than 255 characters',
            'sku.unique' => 'Sku already exists',
            'is_deleted.boolean' => 'Is deleted must be a boolean',
            'supplier_id.required' => 'Supplier is required',
            'supplier_id.exists' => 'Supplier does not exist',
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