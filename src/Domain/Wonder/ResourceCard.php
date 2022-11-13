<?php

namespace App\Domain\Wonder;

use App\Domain\Card\Card;
use App\Domain\Card\CardType;
use App\Domain\Card\Cost;
use App\Domain\Resource\Resources;
use JetBrains\PhpStorm\Pure;

class ResourceCard extends Card
{
    public readonly Resources $resources;

    #[Pure] public function __construct(CardType $type, Cost $cost, Resources $resources)
    {
        parent::__construct($type, $cost);
        $this->resources = $resources;
    }
}
