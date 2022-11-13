<?php

namespace App\Tests\Domain\Builder;


use App\Domain\Card\CardType;
use App\Domain\Wonder\Stages;
use App\Domain\Wonder\Structures;
use App\Domain\Wonder\Wonder;
use App\Domain\Wonder\WonderFace;
use App\Domain\Wonder\WonderType;
use JetBrains\PhpStorm\Pure;

class WonderBuilder
{

    private WonderType $type;
    private WonderFace $face;
    private int $coins;
    private Structures $structures;
    private Stages $stages;

    /**
     * @param WonderType $type
     */
    #[Pure] private function __construct(WonderType $type, WonderFace $face)
    {
        $this->type = $type;
        $this->face = $face;
        $this->coins = 3;
        $this->structures = new Structures();
        $this->stages = $type->stages($this->face);
    }

    #[Pure] public static function of(WonderType $type, WonderFace $face = WonderFace::NIGHT): WonderBuilder
    {
        return new WonderBuilder($type, $face);
    }


    #[Pure] function build(): Wonder
    {
        return new Wonder(
            $this->type,
            $this->face,
            $this->coins,
            $this->structures,
            $this->stages
        );
    }

    public function withStageBuild(int $stageBuild): WonderBuilder
    {
        $this->stages = $this->type->stages($this->face, $stageBuild);
        return $this;
    }

    public function withCards(CardType ...$cardTypes): WonderBuilder
    {
        $this->structures = new Structures(...$cardTypes);
        return $this;
    }
}
