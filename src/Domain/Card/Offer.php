<?php

namespace App\Domain\Card;

use App\Domain\Resource\Resource;
use App\Domain\Wonder\Wonder;
use JetBrains\PhpStorm\Pure;

class Offer
{
    private Wonder $neighbour;
    private Resource $tradedResource;

    /**
     * @param Wonder $neighbour
     * @param Resource $tradedResource
     */
    public function __construct(Wonder $neighbour, Resource $tradedResource)
    {
        $this->neighbour = $neighbour;
        $this->tradedResource = $tradedResource;
    }


    #[Pure] public function equals(Offer $other): bool
    {
        return $other->tradedResource->equals($this->tradedResource)
            && $other->neighbour->equals($this->neighbour);
    }

    public function hasTradedResource(): bool
    {
        return $this->tradedResource->hasAtLeastOnePositive();
    }

    public function tradeId(): string
    {
        return $this->tradedResource->tradeId();
    }

    public function toString()
    {
        return <<<EOD
            Offer {
                neighbour = {$this->neighbour->type->name}
                tradedResource = {$this->tradedResource->toString()}
            }
        EOD;

    }
}
