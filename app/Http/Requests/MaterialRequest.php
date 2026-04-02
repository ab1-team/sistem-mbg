<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class MaterialRequest extends FormRequest
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
        $materialId = $this->material?->id;

        return [
            'code' => 'required|string|max:30|unique:materials,code,'.$materialId,
            'name' => 'required|string|max:150',
            'category' => 'required|in:sayuran,daging,ikan,bumbu,sembako,minuman,lainnya',
            'unit' => 'required|string|max:20',
            'calories' => 'nullable|numeric|min:0',
            'protein' => 'nullable|numeric|min:0',
            'carbs' => 'nullable|numeric|min:0',
            'fat' => 'nullable|numeric|min:0',
            'fiber' => 'nullable|numeric|min:0',
            'price_estimate' => 'required|numeric|min:0',
            'min_stock_threshold' => 'required|numeric|min:0',
            'is_active' => 'boolean',
            'dapur_id' => 'nullable|exists:dapurs,id',
        ];
    }
}
