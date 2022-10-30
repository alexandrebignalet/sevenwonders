<?php

namespace App\Domain\Wonder;

use JetBrains\PhpStorm\Pure;

class Wonder {
    private WonderType $type;
    private array $structures;
    private array $stages;


    #[Pure] public static function initialize(WonderType $type): Wonder
    {
        return new Wonder($type, [], []);
    }

    /**
     * @param WonderType $type
     */
    private function __construct(WonderType $type, array $structures, array $stages)
    {
        $this->type = $type;
        $this->structures = $structures;
        $this->stages = $stages;
    }

    public function structures(): array
    {
        return $this->structures;
    }

    public function type(): WonderType
    {
        return $this->type;
    }

    public function stages(): array
    {
        return $this->stages;
    }
}
