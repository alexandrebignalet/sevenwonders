<?php

namespace App\Domain\Wonder;

use App\Domain\Card\CardType;
use App\Domain\Resource\Resource;
use App\Domain\Resource\Resources;
use JetBrains\PhpStorm\Pure;

class Structures
{
    private Resources $resources;

    /**
     * @param Resources $resources
     */
    public function __construct(Resources $resources)
    {
        $this->resources = $resources;
    }

    #[Pure] public static function initialize(): Structures
    {
        return new Structures(
            resources: Resources::empty()
        );
    }

    public function add(CardType $cardType): Structures
    {
        $card = $cardType->card();
        return new Structures($this->resources->plus($card->resources));
    }

    public function missingResourcesToPay(Resource $resourceCost): Resources
    {
        return $this->resources->missingToEqual($resourceCost);
    }

    public function resources(): Resources
    {
        return $this->resources;
    }

    #[Pure] public function count(): int
    {
        return $this->resources->count();
    }

    public function toString(): string
    {
        return <<<EOD
        Structures {
                    resources = ({$this->resources->toString()})
                }
        EOD;

    }

}
