<?php

namespace App\Domain\Wonder;

use JetBrains\PhpStorm\Pure;

enum WonderPowerType: string
{
    case BUILD_LAST_CARD = "BUILD_LAST_CARD";
    case CARAVANSARAIL = "CARAVANSARAIL";
    case MARCHE = "MARCHE";
    case SCIENCE = "SCIENCE";
    case BUILD_FREE_FIRST_COLOR = "BUILD_FREE_FIRST_COLOR";
    case BUILD_FIRST_CARD_FREE = "BUILD_FIRST_CARD_FREE";
    case BUILD_LAST_CARD_FREE = "BUILD_LAST_CARD_FREE";
    case UNBURY_CARD = "UNBURY_CARD";

    #[Pure] public function wonderPower(): WonderPower
    {
        return new WonderPower($this);
    }
}
