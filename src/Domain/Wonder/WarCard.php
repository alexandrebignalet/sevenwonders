<?php

namespace App\Domain\Wonder;

use App\Domain\Card\Card;
use App\Domain\Card\CardType;
use App\Domain\Card\Cost;
use JetBrains\PhpStorm\Pure;

class WarCard extends Card
{

    public readonly int $points;

    #[Pure] public function __construct(CardType $type, Cost $cost, int $points)
    {
        parent::__construct($type, $cost);
        $this->points = $points;
    }
}
