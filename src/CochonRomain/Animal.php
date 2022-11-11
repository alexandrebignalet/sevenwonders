<?php

namespace App\CochonRomain;

abstract class Animal
{
    protected string $name;
    protected bool $estfeed;

    public function __construct(string $name, bool $estfeed=false)
    {
        $this->name = $name;
        $this->estfeed = $estfeed;
    }

    abstract public function cri(): string;

    public function renommage(string $name)
    {
        $this->name = $name;
    }

    public function feed(Consommable $conso) : void{

        $conso->smokeEat();
        $this->estfeed = true;

    }

    public function estfeed() : bool {

        return $this->estfeed;
    }
}
