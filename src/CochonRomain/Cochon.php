<?php

namespace App\CochonRomain;

use JetBrains\PhpStorm\Pure;

class Cochon extends Animal
{

    #[Pure] public function __construct(string $name)
    {
        parent::__construct($name);
    }

    public function cri(): string
    {
        return "{$this->name} is telling: gruik";
    }

    public function renommage(string $name)
    {
        $this->name = $name;
    }
}
