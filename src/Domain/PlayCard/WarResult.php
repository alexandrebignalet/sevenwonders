<?php

namespace App\Domain\PlayCard;

enum WarResult: string
{
    case WON = "WON";
    case EX_AEQUO = "EX_AEQUO";
    case LOSE = "LOSE";
}
