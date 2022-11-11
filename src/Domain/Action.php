<?php

namespace App\Domain;

enum Action: string
{
    case BUILD_STRUCTURE = "BUILD_STRUCTURE";
    case BUILD_STAGE = "BUILD_STAGE";
    case DISCARD = "DISCARD";

    /**
     * @throws GameException
     */
    public static function of(string $action): Action
    {
        foreach (Action::cases() as $case) {
            if ($case->value === $action) {
                return $case;
            }
        }

        throw GameExceptionType::UNKNOWN_ACTION->exception();
    }
}
