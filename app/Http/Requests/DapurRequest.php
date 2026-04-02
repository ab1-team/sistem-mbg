<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class DapurRequest extends FormRequest
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
        $dapurId = $this->dapur?->id;

        return [
            'code' => 'required|string|max:10|unique:dapurs,code,'.$dapurId,
            'name' => 'required|string|max:100',
            'address' => 'nullable|string',
            'city' => 'nullable|string|max:50',
            'province' => 'nullable|string|max:50',
            'capacity_portions' => 'required|integer|min:0',
            'is_active' => 'boolean',
        ];
    }
}
