<?php

namespace App\Domain\Card;

class ScienceCard extends Card
{

    /**
     * @param CardType $type
     * @param Cost $cost
     * @param ScienceSymbol $symbol
     */
    public function __construct(CardType $type, Cost $cost, ScienceSymbol $symbol)
    {
        parent::__construct($type, $cost);
        $this->symbol = $symbol;
    }
}
