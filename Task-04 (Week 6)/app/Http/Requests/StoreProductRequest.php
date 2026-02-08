<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreProductRequest extends FormRequest
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
     */
    public function rules(): array
    {
        return [
            'name' => 'required|string|max:255|unique:products,name',
            'price' => 'required|numeric|gt:0',
            'category_id' => 'required|exists:categories,id',
            'image' => 'nullable|image|mimes:jpg,jpeg,png,webp,gif|max:2048',

            // Suppliers validation
            'suppliers' => ['required', 'array', function ($attribute, $value, $fail) {
                // Check that at least one supplier is selected
                $hasSelected = collect($value)->contains(fn($s) => !empty($s['selected']));
                if (!$hasSelected) {
                    $fail('At least one supplier must be selected.');
                }
            }],
            'suppliers.*.selected' => 'nullable',
            'suppliers.*.cost_price' => 'required_if:suppliers.*.selected,1|nullable|numeric|min:0',
            'suppliers.*.lead_time_days' => 'required_if:suppliers.*.selected,1|nullable|integer|min:0',
        ];
    }

    /**
     * Get custom error messages for validation rules.
     */
    public function messages(): array
    {
        return [
            'name.required' => 'Product name is required.',
            'name.unique' => 'A product with this name already exists.',
            'price.required' => 'Product price is required.',
            'price.numeric' => 'Price must be a valid number.',
            'price.gt' => 'Price must be greater than 0.',
            'category_id.required' => 'Please select a category.',
            'category_id.exists' => 'The selected category is invalid.',
            'image.image' => 'The file must be an image.',
            'image.mimes' => 'Allowed image types: JPG, PNG, WebP, GIF.',
            'image.max' => 'Image must not exceed 2 MB.',
            'suppliers.required' => 'Please select at least one supplier.',
            'suppliers.*.cost_price.required_if' => 'Cost price is required for selected suppliers.',
            'suppliers.*.cost_price.numeric' => 'Cost price must be a valid number.',
            'suppliers.*.cost_price.min' => 'Cost price must be at least 0.',
            'suppliers.*.lead_time_days.required_if' => 'Lead time is required for selected suppliers.',
            'suppliers.*.lead_time_days.integer' => 'Lead time must be a whole number.',
            'suppliers.*.lead_time_days.min' => 'Lead time must be at least 0 days.',
        ];
    }

    /**
     * Prepare the data for validation.
     */
    protected function prepareForValidation(): void
    {
        // Ensure suppliers array exists
        if (!$this->has('suppliers')) {
            $this->merge(['suppliers' => []]);
        }
    }
}
