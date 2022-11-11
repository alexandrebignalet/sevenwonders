<?php

namespace App\Domain;

use JetBrains\PhpStorm\Pure;

enum GameExceptionType: string
{
    case CARD_PLAY_NOT_ALLOWED = 'you are not allowed to play this card';
    case PLAYER_NOT_FOUND = 'you are lost';
    case UNKNOWN_ACTION = 'you are a pirate';
    case NO_MORE_AGES = 'no age 4 sorry';


    #[Pure] public function exception(): GameException
    {
        return new GameException($this);
    }
}
