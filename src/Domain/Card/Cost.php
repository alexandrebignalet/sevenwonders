<?php

namespace App\Domain\Card;

use App\Domain\Resource\Resource;
use JetBrains\PhpStorm\Pure;

class Cost
{
    public readonly int $coins;
    public readonly Resource $resource;
    /**
     * @var CardType[]
     */
    public readonly array $chain;

    #[Pure] public static function free(): Cost
    {
        return new Cost(0, new Resource(), []);
    }

    /**
     * @param int $coins
     * @param Resource $resource
     * @param CardType[] $chain
     */
    #[Pure] public function __construct(int $coins = 0, Resource $resource = new Resource(), array $chain = [])
    {
        $this->coins = $coins;
        $this->resource = $resource;
        $this->chain = $chain;
    }
}
