<?php

namespace App\Domain\Wonder;

use App\Domain\Action;
use App\Domain\Card\CardAction;
use App\Domain\Resource\Resources;
use JetBrains\PhpStorm\Pure;

class Wonder
{
    public readonly WonderType $type;
    public readonly WonderFace $face;
    public readonly int $coins;
    public readonly Structures $structures;
    public readonly Stages $stages;


    #[Pure] public static function initialize(WonderType $type, WonderFace $face): Wonder
    {
        return new Wonder($type, $face, 3, Structures::initialize(), $type->stages($face));
    }

    /**
     * @param WonderType $type
     */
    public function __construct(WonderType $type, WonderFace $face, int $coins, Structures $structures, Stages $stages)
    {
        $this->coins = $coins;
        $this->type = $type;
        $this->structures = $structures;
        $this->stages = $stages;
        $this->face = $face;
    }

    public function build(CardAction $selectedAction): Wonder
    {
        switch ($selectedAction->action()) {
            case Action::BUILD_STRUCTURE:
                $structures = $this->structures->add($selectedAction->cardType());
                return new Wonder($this->type, $this->face, $this->coins, $structures, $this->stages);
            case Action::BUILD_STAGE:
                $stages = $this->stages->build();
                return new Wonder($this->type, $this->face, $this->coins, $this->structures, $stages);
            case Action::DISCARD:
                return new Wonder($this->type, $this->face, $this->coins + 3, $this->structures, $this->stages);
        }
    }

    public function resources(): Resources
    {
        return (new Resources($this->type->resource()))->plus($this->structures->resources());
    }

    #[Pure] public function structuresCount(): int
    {
        return $this->structures->count();
    }

    #[Pure] public function hasActionRequiredPower(WonderPowerType $powerType): bool
    {
        $power = $this->stages->powerRequiresAction();
        return $power === $powerType;
    }

    public function warPoints(): int
    {
        return $this->structures->warPoints();
    }
}
