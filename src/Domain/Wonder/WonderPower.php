<?php

namespace App\Domain\Wonder;

class WonderPower
{

    private WonderPowerType $type;

    /**
     * @param WonderPowerType $type
     */
    public function __construct(WonderPowerType $type)
    {
        $this->type = $type;
    }


    public function requiresAction(): bool|WonderPowerType
    {
        switch ($this->type) {
            case WonderPowerType::CARAVANSARAIL:
            case WonderPowerType::MARCHE:
            case WonderPowerType::SCIENCE:
            case WonderPowerType::BUILD_FREE_FIRST_COLOR:
            case WonderPowerType::BUILD_FIRST_CARD_FREE:
            case WonderPowerType::BUILD_LAST_PLAYABLE_CARD_FREE:
                return false;
            case WonderPowerType::BUILD_LAST_CARD:
            case WonderPowerType::UNBURY_CARD:
                return $this->type;
        }
    }
}
