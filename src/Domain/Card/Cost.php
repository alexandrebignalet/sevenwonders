<?php

namespace App\Domain\Card;

use App\Domain\Resource\Resource;
use JetBrains\PhpStorm\Pure;

class Cost
{
    public readonly int $coins;
    public readonly Resource $resource;
    public readonly ?CardType $chain;

    #[Pure] public static function free(): Cost
    {
        return new Cost(0, new Resource(), null);
    }

    #[Pure] public static function stoneOnly(int $stone): Cost
    {
        return new Cost(0, new Resource(stone: $stone), null);
    }

    public function __construct(int $coins = 0, Resource $resource = new Resource(), ?CardType $chain = null)
    {
        $this->coins = $coins;
        $this->resource = $resource;
        $this->chain = $chain;
    }
}
