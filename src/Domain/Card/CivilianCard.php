<?php

namespace App\Domain\Card;

class CivilianCard extends Card
{
    private int $points;

    /**
     * @param CardType $type
     * @param Cost $cost
     * @param int $points
     */
    public function __construct(CardType $type, Cost $cost, int $points)
    {
        parent::__construct($type, $cost);
        $this->points = $points;
    }
}
