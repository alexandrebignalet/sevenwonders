<?php

namespace App\Domain\Card;

use JetBrains\PhpStorm\Pure;

class Card
{
    public readonly CardType $type;
    public readonly Cost $cost;

    #[Pure] public function __construct(CardType $type, ?Cost $cost = null)
    {
        $this->type = $type;
        $this->cost = $cost === null ? Cost::free() : $cost;
    }
}
