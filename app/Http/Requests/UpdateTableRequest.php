<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateTableRequest extends FormRequest
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
            'table_name' => 'required|string|max:255',
            'capacity' => 'required|integer|min:1|max:20',
            'position_x' => 'required|numeric|min:0|max:1000',
            'position_y' => 'required|numeric|min:0|max:1000',
            'status' => 'nullable|in:available,occupied,reserved,cleaning,billed',
        ];
    }

    /**
     * Get custom error messages for validator errors.
     *
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'table_name.required' => 'Table name is required.',
            'capacity.required' => 'Table capacity is required.',
            'capacity.min' => 'Table must seat at least 1 person.',
            'capacity.max' => 'Table capacity cannot exceed 20 people.',
            'position_x.required' => 'X position is required.',
            'position_y.required' => 'Y position is required.',
        ];
    }
}