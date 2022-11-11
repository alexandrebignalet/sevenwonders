<?php

namespace App\CochonRomain;

use JetBrains\PhpStorm\Pure;

class Vache extends Animal
{

    #[Pure] public function __construct(string $name)
    {
        parent::__construct($name);
    }

    public function cri(): string
    {
        return 'meuh';
    }

    public function renommage(string $name)
    {
        $this->name = $name;
    }
}
