<?php

namespace App\Domain\Card;

class TradeCard extends Card
{
    /**
     * @param CardType $type
     * @param Cost $cost
     */
    public function __construct(CardType $type, Cost $cost)
    {
        parent::__construct($type, $cost);
    }
}
