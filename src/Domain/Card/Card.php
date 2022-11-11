<?php

namespace App\Domain\Card;

use App\Domain\Resource\Resources;

class Card
{
    public readonly CardType $type;
    public readonly Cost $cost;
    public readonly Resources $resources;

    public function __construct(CardType $type, ?Cost $cost = null, ?Resources $resources = null)
    {
        $this->type = $type;
        $this->cost = $cost === null ? Cost::free() : $cost;
        $this->resources = $resources === null ? Resources::empty() : $resources;
    }
}
