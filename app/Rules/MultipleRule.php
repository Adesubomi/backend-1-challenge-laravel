<?php

namespace App\Rules;

use Illuminate\Contracts\Validation\Rule;

class MultipleRule implements Rule
{
    private float $factor;

    public function __construct(float $factor)
    {
        $this->factor = $factor;
    }

    public function passes($attribute, $value): bool
    {
        return (float)$value % $this->factor == 0;
    }

    public function message(): string
    {
        return 'The :attribute must be a multiple of '. $this->factor;
    }
}
