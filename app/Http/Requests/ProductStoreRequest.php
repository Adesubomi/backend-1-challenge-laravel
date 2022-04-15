<?php

namespace App\Http\Requests;

use App\Rules\MultipleRule;
use Illuminate\Foundation\Http\FormRequest;

class ProductStoreRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'amount_available' => ['required', 'numeric'],
            'cost' => ['required', 'numeric', new MultipleRule(5)],
            'product_name' => 'required',
        ];
    }
}
