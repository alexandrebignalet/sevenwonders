<?php

namespace App\CochonRomain;

abstract class Animal
{
    protected string $name;

    public function __construct(string $name)
    {
        $this->name = $name;
    }

    abstract public function cri(): string;

    public function renommage(string $name)
    {
        $this->name = $name;
    }
}
