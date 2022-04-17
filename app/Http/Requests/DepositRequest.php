<?php

namespace App\Http\Requests;

use App\Enums\Coin;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use JetBrains\PhpStorm\ArrayShape;

class DepositRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'coin' => ['required', Rule::in(Coin::values())]
        ];
    }
}
