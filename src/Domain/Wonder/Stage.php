<?php

namespace App\Domain\Wonder;

use App\Domain\Resource\Resource;
use JetBrains\PhpStorm\Pure;

class Stage
{

    private Resource $resourceCost;
    private int $points;
    private int $warPoints;
    private ?WonderPower $power;
    private bool $isBuilt;
    private int $coins;

    public function __construct(Resource    $resourceCost,
                                int         $points = 0,
                                int         $warPoints = 0,
                                int         $coins = 0,
                                WonderPower $power = null,
                                bool        $isBuilt = false)
    {
        $this->resourceCost = $resourceCost;
        $this->points = $points;
        $this->warPoints = $warPoints;
        $this->power = $power;
        $this->isBuilt = $isBuilt;
        $this->coins = $coins;
    }

    /**
     * @return Resource
     */
    public function getResourceCost(): Resource
    {
        return $this->resourceCost;
    }

    /**
     * @return int
     */
    public function getPoints(): int
    {
        return $this->points;
    }

    /**
     * @return int
     */
    public function getWarPoints(): int
    {
        return $this->warPoints;
    }

    /**
     * @return int
     */
    public function getCoins(): int
    {
        return $this->coins;
    }

    /**
     * @return mixed
     */
    public function getPower()
    {
        return $this->power;
    }

    /**
     * @return bool
     */
    public function isBuilt(): bool
    {
        return $this->isBuilt;
    }

    public function build(): Stage
    {
        return new Stage(
            $this->resourceCost,
            $this->points,
            $this->warPoints,
            $this->coins,
            $this->power,
            true
        );
    }

    public function toString()
    {
        $isBuilt = $this->isBuilt ? 'true' : 'false';
        return <<<EOD
            Stage {
                resourceCost = {$this->resourceCost->toString()}
                points = {$this->points}
                warPoints = {$this->warPoints}
                power = {$this->power}
                isBuilt = {$isBuilt}
                coins = {$this->coins}
            }
        EOD;

    }

    #[Pure] public function powerRequiresAction(): bool|WonderPowerType
    {
        if ($this->isBuilt() && $this->power !== null) {
            return $this->power->requiresAction();
        }
        return false;
    }

}
