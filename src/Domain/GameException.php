<?php

namespace App\Domain;

use JetBrains\PhpStorm\Pure;

class GameException extends \Exception
{

    private GameExceptionType $customCode;

    #[Pure] public function __construct(GameExceptionType $type)
    {
        parent::__construct($type->value);
        $this->customCode = $type;
    }

    /**
     * @return GameExceptionType
     */
    public function getCustomCode(): GameExceptionType
    {
        return $this->customCode;
    }


}
