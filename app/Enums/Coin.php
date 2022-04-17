<?php

namespace App\Enums;

use App\Traits\EnumTraits;

enum Coin: int
{
    use EnumTraits;

    case Five = 5;
    case Ten = 10;
    case Twenty = 20;
    case Fifty = 50;
    case Hundred = 100;
}
