<?php

namespace App\Traits;

trait EnumTraits
{
    public function match(mixed $value, bool $strict = false): bool
    {
        if (!$strict) {
            if ($value === $this->name) {
                return true;
            }
        }

        if ($this === $value
            || $this->value === $value) {
            return true;
        }

        return false;
    }
}
