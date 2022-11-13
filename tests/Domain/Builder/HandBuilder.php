<?php

namespace App\Tests\Domain\Builder;


use App\Domain\Age;
use App\Domain\Card\Card;
use App\Domain\Card\CardType;
use App\Domain\Hand;
use JetBrains\PhpStorm\Pure;

class HandBuilder
{

    /**
     * @var CardType[] $cardTypes
     */
    private array $cardTypes;

    /**
     * @param CardType[] $cardTypes
     */
    #[Pure] private function __construct(array $cardTypes)
    {
        $this->cardTypes = $cardTypes;
    }

    #[Pure] public static function of(Age $age, int $count = 7): HandBuilder
    {
        $cardTypes = array_map(fn(Card $card) => $card->type, $age->distributeHand());
        return new HandBuilder(array_splice($cardTypes, 0, $count));
    }

    /**
     * @param CardType[] $cardTypes
     */
    #[Pure] public static function with(CardType ...$cardTypes): HandBuilder
    {
        return new HandBuilder($cardTypes);
    }


    #[Pure] function build(): Hand
    {
        return new Hand($this->cardTypes);
    }
}
