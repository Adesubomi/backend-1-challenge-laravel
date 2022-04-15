<?php

namespace App\Http\Requests;

use App\Rules\MultipleRule;
use Illuminate\Foundation\Http\FormRequest;

class ProductUpdateRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'amount_available' => ['sometimes', 'numeric'],
            'cost' => ['sometimes', 'numeric', new MultipleRule(5)],
            'product_name' => 'sometimes',
        ];
    }
}
