<?php

namespace App\Traits;

use JetBrains\PhpStorm\ArrayShape;

trait EnumTraits
{
    /**
     * TODO :: Update method to support non-backed enums
     */
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

    /**
     * TODO :: Update method to support non-backed enums
     */
    public static function values(): array
    {
        return array_map( fn($v) => $v->value, self::cases() );
    }

    /**
     * TODO :: Update method to support non-backed enums
     */
    public static function names(): array
    {
        return array_map( fn($v) => $v->name, self::cases() );
    }
}
