<?php

namespace App\Tests\CochonRomain;


use App\CochonRomain\Animal;
use App\CochonRomain\Cochon;
use App\CochonRomain\Romain;
use App\CochonRomain\SacDeNourriture;
use App\CochonRomain\Vache;
use Monolog\Test\TestCase;

class CochonRomainTest extends TestCase
{

    public function test_objet()
    {
        $animals = array(
            new Vache('gertrude'),
            new Cochon('germain'),
            new Romain('benest')
        );
        $nourriture = new SacDeNourriture();
        $this->assertEquals($nourriture->quantite(), 10);


        foreach ($animals as $animal) {
            $animal->feed($nourriture);
            $this->assertTrue($animal->estFeed());
        }
        $this->assertEquals($nourriture->quantite(), 7);
    }

    /**
     * @param Animal[] $animals
     * @return void
     */
    function faitCrierLaBassecour(array $animals)
    {
        foreach ($animals as $animal) {
            var_dump($animal->cri());
        }
    }
}
