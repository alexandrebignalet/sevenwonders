<?php

namespace App\Domain;

class GameException extends \Exception
{

    public function __construct(GameExceptionType $type)
    {
        parent::__construct($type->value);
        $this->code = $type;
    }
}
