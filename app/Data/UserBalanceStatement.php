<?php

namespace App\Data;

class UserBalanceStatement
{
    public readonly int $balance = 0;

    public readonly array $coins = [];

    public function __construct(array $coins)
    {

    }
}
