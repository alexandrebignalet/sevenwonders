<?php

namespace App\Domain;

enum RotationDirectionFlow: string
{
    case CLOCKWISE = "CLOCKWISE";
    case ANTICLOCKWISE = "ANTICLOCKWISE";
}
