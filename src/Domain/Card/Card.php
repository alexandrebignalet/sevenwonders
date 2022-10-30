<?php

namespace App\Domain\Card;

class Card {
    private int $points;
    private int $warPoints;
    private CardType $type;
//    private int $resource;
//    private Cost $cost;
//    private ScientificSymbol $symbol;

    public function __construct(CardType $type)
    {
        $this->type = $type;
    }


    function type(): CardType
    {
        return $this->type;
    }
}
//
//enum ScientificSymbol {
//    case ROUE;
//    case COMPAS;
//    case TABLETTE;
//}
//
//class Cost {
//    private int $coins;
//    private int $clay;
//    private int $terre;
//    private int $iron;
//    private int $wood;
//    private int $papyrus;
//    private int $flotte;
//    private int $papier;
//    private array $chain;
//
//}
//
//class CardResources {
//
//    function canAfford() {
//
//    }
//}
