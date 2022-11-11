<?php

namespace App\Domain\Wonder;

use App\Domain\Resource\Resource;
use JetBrains\PhpStorm\Pure;

enum WonderType
{
    case RHODOS;
    case ALEXANDRIA;
    case GIZAH;
    case BABYLON;
    case OLYMPIA;
    case EPHESOS;
    case HALIKARNASSOS;

    #[Pure] public function wonder(WonderFace $face): Wonder
    {
        return Wonder::initialize($this, $face);
    }

    #[Pure] public function stages(WonderFace $face, int $stagesBuilt = 0): Stages
    {
        $firstBuilt = $stagesBuilt === 1;
        $secondBuilt = $stagesBuilt === 2;
        $thirdBuilt = $stagesBuilt === 2;
        $forthBuilt = $stagesBuilt === 2;

        switch ($this) {
            case self::RHODOS:
                if ($face === WonderFace::DAY) {
                    return new Stages(
                        new Stage(resourceCost: new Resource(wood: 2), points: 3, isBuilt: $firstBuilt),
                        new Stage(resourceCost: new Resource(clay: 3), warPoints: 2, isBuilt: $secondBuilt),
                        new Stage(resourceCost: new Resource(ore: 4), points: 7, isBuilt: $thirdBuilt),
                    );
                } else {
                    return new Stages(
                        new Stage(resourceCost: new Resource(stone: 3), points: 3, warPoints: 1, coins: 3, isBuilt: $firstBuilt),
                        new Stage(resourceCost: new Resource(ore: 4), points: 4, warPoints: 1, coins: 4, isBuilt: $secondBuilt),
                    );
                }
            case self::ALEXANDRIA:
                if ($face === WonderFace::DAY) {
                    return new Stages(
                        new Stage(resourceCost: new Resource(stone: 2), points: 3, isBuilt: $firstBuilt),
                        new Stage(resourceCost: new Resource(ore: 2), power: WonderPowerType::CARAVANSARAIL->wonderPower(), isBuilt: $secondBuilt),
                        new Stage(resourceCost: new Resource(papyrus: 1, cloth: 1), points: 7, isBuilt: $thirdBuilt),
                    );
                } else {
                    return new Stages(
                        new Stage(resourceCost: new Resource(clay: 2), power: WonderPowerType::CARAVANSARAIL->wonderPower(), isBuilt: $firstBuilt),
                        new Stage(resourceCost: new Resource(ore: 3), power: WonderPowerType::MARCHE->wonderPower(), isBuilt: $secondBuilt),
                        new Stage(resourceCost: new Resource(wood: 4), points: 7, isBuilt: $thirdBuilt),
                    );
                }
            case self::GIZAH:
                if ($face === WonderFace::DAY) {
                    return new Stages(
                        new Stage(resourceCost: new Resource(wood: 2), points: 3, isBuilt: $firstBuilt),
                        new Stage(resourceCost: new Resource(clay: 2, cloth: 1), points: 5, isBuilt: $secondBuilt),
                        new Stage(resourceCost: new Resource(stone: 4), points: 7, isBuilt: $thirdBuilt),
                    );
                } else {
                    return new Stages(
                        new Stage(resourceCost: new Resource(wood: 2), points: 3, isBuilt: $firstBuilt),
                        new Stage(resourceCost: new Resource(stone: 3), points: 5, isBuilt: $secondBuilt),
                        new Stage(resourceCost: new Resource(clay: 3), points: 5, isBuilt: $thirdBuilt),
                        new Stage(resourceCost: new Resource(stone: 4, papyrus: 1), points: 7, isBuilt: $forthBuilt),
                    );
                }
            case self::BABYLON:
                if ($face === WonderFace::DAY) {
                    return new Stages(
                        new Stage(resourceCost: new Resource(clay: 2), points: 3, isBuilt: $firstBuilt),
                        new Stage(resourceCost: new Resource(ore: 2, cloth: 1), power: WonderPowerType::SCIENCE->wonderPower(), isBuilt: $secondBuilt),
                        new Stage(resourceCost: new Resource(wood: 4), points: 7, isBuilt: $thirdBuilt),
                    );
                } else {
                    return new Stages(
                        new Stage(resourceCost: new Resource(stone: 2), power: WonderPowerType::BUILD_LAST_CARD->wonderPower(), isBuilt: $firstBuilt),
                        new Stage(resourceCost: new Resource(clay: 3, glass: 1), power: WonderPowerType::SCIENCE->wonderPower(), isBuilt: $secondBuilt),
                    );
                }
            case self::OLYMPIA:
                if ($face === WonderFace::DAY) {
                    return new Stages(
                        new Stage(resourceCost: new Resource(stone: 2), points: 3, isBuilt: $firstBuilt),
                        new Stage(resourceCost: new Resource(wood: 2), power: WonderPowerType::BUILD_FREE_FIRST_COLOR->wonderPower(), isBuilt: $secondBuilt),
                        new Stage(resourceCost: new Resource(clay: 3), points: 7, isBuilt: $thirdBuilt),
                    );
                } else {
                    return new Stages(
                        new Stage(resourceCost: new Resource(ore: 2), power: WonderPowerType::BUILD_FIRST_CARD_FREE->wonderPower(), isBuilt: $firstBuilt),
                        new Stage(resourceCost: new Resource(clay: 3), power: WonderPowerType::BUILD_LAST_CARD_FREE->wonderPower(), isBuilt: $secondBuilt),
                        new Stage(resourceCost: new Resource(papyrus: 1, glass: 1, cloth: 1), points: 5, isBuilt: $thirdBuilt),
                    );
                }
            case self::EPHESOS:
                if ($face === WonderFace::DAY) {
                    return new Stages(
                        new Stage(resourceCost: new Resource(clay: 2), points: 3, isBuilt: $firstBuilt),
                        new Stage(resourceCost: new Resource(wood: 2), coins: 9, isBuilt: $secondBuilt),
                        new Stage(resourceCost: new Resource(ore: 2, glass: 1), points: 7, isBuilt: $thirdBuilt),
                    );
                } else {
                    return new Stages(
                        new Stage(resourceCost: new Resource(stone: 2), points: 2, coins: 4, isBuilt: $firstBuilt),
                        new Stage(resourceCost: new Resource(wood: 2), points: 3, coins: 4, isBuilt: $secondBuilt),
                        new Stage(resourceCost: new Resource(ore: 2, cloth: 1), points: 5, coins: 4, isBuilt: $thirdBuilt),
                    );
                }
            case self::HALIKARNASSOS:
                if ($face === WonderFace::DAY) {
                    return new Stages(
                        new Stage(resourceCost: new Resource(ore: 2), points: 3, isBuilt: $firstBuilt),
                        new Stage(resourceCost: new Resource(papyrus: 1, glass: 1), power: WonderPowerType::UNBURY_CARD->wonderPower(), isBuilt: $secondBuilt),
                        new Stage(resourceCost: new Resource(stone: 3), points: 7, isBuilt: $thirdBuilt),
                    );
                } else {
                    return new Stages(
                        new Stage(resourceCost: new Resource(clay: 2), power: WonderPowerType::UNBURY_CARD->wonderPower(), isBuilt: $firstBuilt),
                        new Stage(resourceCost: new Resource(papyrus: 1, glass: 1), power: WonderPowerType::UNBURY_CARD->wonderPower(), isBuilt: $secondBuilt),
                        new Stage(resourceCost: new Resource(wood: 3), power: WonderPowerType::UNBURY_CARD->wonderPower(), isBuilt: $thirdBuilt),
                    );
                }
        }
    }

    #[Pure] public function resource(): Resource
    {
        switch ($this) {
            case self::RHODOS:
                return new Resource(ore: 1);
            case self::ALEXANDRIA:
                return new Resource(glass: 1);
            case self::GIZAH:
                return new Resource(stone: 1);
            case self::BABYLON:
                return new Resource(wood: 1);
            case self::OLYMPIA:
                return new Resource(clay: 1);
            case self::EPHESOS:
                return new Resource(papyrus: 1);
            case self::HALIKARNASSOS:
                return new Resource(cloth: 1);
        }
    }
}
