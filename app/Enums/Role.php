<?php

namespace App\Enums;

use App\Traits\EnumTraits;

enum Role: string
{
    use EnumTraits;

    case Buyer = "Buyer";
    case Seller = "Seller";
}
