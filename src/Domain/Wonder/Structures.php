<?php

namespace App\Domain\Wonder;

use App\Domain\Card\CardKind;
use App\Domain\Card\CardType;
use App\Domain\Resource\Resource;
use App\Domain\Resource\Resources;
use JetBrains\PhpStorm\Pure;

class Structures
{
    /**
     * @var CardType[] $cardTypes
     */
    private array $cardTypes;

    public function __construct(CardType ...$cardTypes)
    {
        $this->cardTypes = $cardTypes;
    }

    #[Pure] public static function initialize(): Structures
    {
        return new Structures();
    }

    public function add(CardType $cardType): Structures
    {
        $this->cardTypes[] = $cardType;
        return new Structures(...$this->cardTypes);
    }

    public function missingResourcesToPay(Resource $resourceCost): Resources
    {
        return $this->resources()->missingToEqual($resourceCost);
    }

    public function resources(): Resources
    {
        return array_reduce($this->cardTypes, function (Resources $acc, CardType $cardType) {
            $card = $cardType->card();
            return $card instanceof ResourceCard ? $acc->plus($card->resources) : $acc;
        }, new Resources());
    }

    public function warPoints(): int
    {
        return array_reduce($this->cardTypes, function (int $acc, CardType $cardType) {
            $card = $cardType->card();
            return $card instanceof WarCard ? $acc + $card->points : $acc;
        }, 0);
    }

    #[Pure] public function count(): int
    {
        return count($this->cardTypes);
    }
}
