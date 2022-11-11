<?php

namespace App\CochonRomain;

use JetBrains\PhpStorm\Pure;

class Romain extends Animal
{

    #[Pure] public function __construct(string $name)
    {
        parent::__construct($name);
    }

    public function cri(): string
    {
        return 'gniieee';
    }

    public function renommage(string $name)
    {
        $this->name = $name;
    }
}
