<?php

namespace App\Domain;

use App\Domain\Card\Card;
use App\Domain\Card\CardType;

class Age {

    /**
     * @return Card[]
     */
    public static function firstAgeCards(int $playersCount): array {
        $ageOneCardsType = [
            CardType::CARRIERE,
            CardType::SCIERIE,
            CardType::BASSIN_ARGILEUX,
            CardType::FILON,
            CardType::FRICHE,
            CardType::EXCAVATION,
            CardType::FOSSE_ARGILEUSE,
            CardType::EXPLOITATION_FORESTIERE,
            CardType::GISEMENT,
            CardType::MINE,
            CardType::VERRERIE,
            CardType::PRESSE,
            CardType::METIER_A_TISSER,
            CardType::PRETEUR_SUR_GAGE,
            CardType::BAINS,
            CardType::AUTEL,
            CardType::THEATRE,
            CardType::TAVERNE,
            CardType::MARCHE,
            CardType::COMPTOIR_OUEST,
            CardType::COMPTOIR_EST,
            CardType::PALISSADE,
            CardType::CASERNE,
            CardType::TOUR_DE_GARDE,
            CardType::OFFICINE,
            CardType::ATELIER,
            CardType::SCRIPTORIUM,
        ];
        return array_reduce($ageOneCardsType, fn(array $acc, CardType $type) => array_merge($acc, $type->cards($playersCount, 1)), []);
    }

    public static function first(int $playersCount, bool $shuffle = true): Age {
        $cards = Age::firstAgeCards($playersCount);

        if ($shuffle) {
            shuffle($cards);
        }

        return new Age(1, $cards);
    }

    public static function second(): Age {
        return new Age(2, $cards = []);
    }

    public static function third(): Age {
        return new Age(3, $cards= []);
    }

    private int $id;
    /**
     * @var Card[]
     */
    private array $cards;

    /**
     * @param int $id
     * @param Card[] $cards
     */
    public function __construct(int $id, array $cards)
    {
        $this->id = $id;
        $this->cards = $cards;
    }

    public function distributeHand(): Hand
    {
        return new Hand(array_chunk($this->cards, 7)[0]);
    }

    /**
     * @return Card[]
     */
    public function cards(): array
    {
        return $this->cards;
    }

}
