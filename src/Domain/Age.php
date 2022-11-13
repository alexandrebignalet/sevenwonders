<?php

namespace App\Domain;

use App\Domain\Card\Card;
use App\Domain\Card\CardType;

class Age
{

    public readonly int $id;
    /**
     * @var Card[]
     */
    public array $cards;
    public readonly int $playersCount;
    public readonly bool $shuffle;

    /**
     * @param int $id
     * @param Card[] $cards
     */
    public function __construct(int $id, array $cards, int $playersCount, bool $shuffle = false)
    {
        $this->id = $id;
        $this->cards = $cards;
        $this->playersCount = $playersCount;
        $this->shuffle = $shuffle;
    }

    /**
     * @return Card[]
     */
    public static function firstAgeCards(int $playersCount): array
    {
        $ageOneCardsType = [
            CardType::CARRIERE_1,
            CardType::SCIERIE_1,
            CardType::BASSIN_ARGILEUX,
            CardType::FILON,
            CardType::FRICHE,
            CardType::EXCAVATION,
            CardType::FOSSE_ARGILEUSE,
            CardType::EXPLOITATION_FORESTIERE,
            CardType::GISEMENT,
            CardType::MINE,
            CardType::VERRERIE_1,
            CardType::PRESSE_1,
            CardType::METIER_A_TISSER_1,
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
        return array_reduce($ageOneCardsType, fn(array $acc, CardType $type) => array_merge($acc, $type->cards($playersCount)), []);
    }

    public static function first(int $playersCount, bool $shuffle = true): Age
    {
        $cards = Age::firstAgeCards($playersCount);

        if ($shuffle) {
            shuffle($cards);
        }

        return new Age(1, $cards, $playersCount, $shuffle);
    }

    /**
     * @return Card[]
     */
    public static function secondAgeCards(int $playersCount): array
    {
        $ageOneCardsType = [
            CardType::CARRIERE_2,
            CardType::SCIERIE_2,
            CardType::BRIQUETERIE,
            CardType::FONDERIE,
            CardType::FRICHE,
            CardType::VERRERIE_2,
            CardType::PRESSE_2,
            CardType::METIER_A_TISSER_2,
            CardType::STATUE,
            CardType::AQUEDUC,
            CardType::TEMPLE,
            CardType::TRIBUNAL,
            CardType::CARAVANSERAIL,
            CardType::FORUM,
            CardType::VIGNOBLE,
            CardType::BAZAR,
            CardType::ECURIES,
            CardType::CHAMPS_DE_TIR,
            CardType::MURAILLE,
            CardType::PLACE_D_ARMES,
            CardType::DISPENSAIRE,
            CardType::LABORATOIRE,
            CardType::BIBLIOTHEQUE,
            CardType::ECOLE,
        ];
        return array_reduce($ageOneCardsType, fn(array $acc, CardType $type) => array_merge($acc, $type->cards($playersCount)), []);
    }

    public static function second(int $playersCount, bool $shuffle = true): Age
    {
        $cards = Age::secondAgeCards($playersCount);

        if ($shuffle) {
            shuffle($cards);
        }

        return new Age(2, $cards, $playersCount, $shuffle);
    }

    /**
     * @return Card[]
     */
    public function distributeHand(): array
    {
        return array_splice($this->cards, 0, 7);
    }

    public function rotationDirectionFlow(): RotationDirectionFlow
    {
        if ($this->id === 1 || $this->id === 3) {
            return RotationDirectionFlow::CLOCKWISE;
        }

        return RotationDirectionFlow::ANTICLOCKWISE;
    }

    public function next(): Age
    {
        if ($this->id === 1) {
            return Age::second($this->playersCount, $this->shuffle);
        }

        if ($this->id === 2) {
            return new Age(3, [], $this->playersCount, $this->shuffle);
        }

        throw GameExceptionType::NO_MORE_AGES->exception();
    }

}
