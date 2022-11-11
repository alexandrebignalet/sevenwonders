<?php

namespace App\CochonRomain;

class SacDeNourriture implements Consommable
{

    protected int $quantite = 10;



    public function quantite() : int {

        return $this->quantite;
    }

    public function decremente() : void
    {
        $this->quantite = $this->quantite -1;
    }


    public function smokeEat()
    {
        // TODO: Implement smokeEat() method.
        $this->decremente();
    }
}
