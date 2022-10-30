<?php

namespace App\Domain\Card;

use JetBrains\PhpStorm\Pure;

enum CardType: string
{
    case SCIERIE = "SCIERIE";
    case CARRIERE = "CARRIERE";
    case BASSIN_ARGILEUX = "BASSIN_ARGILEUX";
    case FILON = "FILON";
    case FRICHE = "FRICHE";
    case EXCAVATION = "EXCAVATION";
    case FOSSE_ARGILEUSE = "FOSSE_ARGILEUSE";
    case EXPLOITATION_FORESTIERE = "EXPLOITATION_FORESTIERE";
    case GISEMENT = "GISEMENT";
    case MINE = "MINE";
    case VERRERIE = "VERRERIE";
    case PRESSE = "PRESSE";
    case METIER_A_TISSER = "METIER_A_TISSER";
    case PRETEUR_SUR_GAGE = "PRETEUR_SUR_GAGE";
    case BAINS = "BAINS";
    case AUTEL = "AUTEL";
    case THEATRE = "THEATRE";
    case TAVERNE = "TAVERNE";
    case MARCHE = "MARCHE";
    case COMPTOIR_OUEST = "COMPTOIR_OUEST";
    case COMPTOIR_EST = "COMPTOIR_EST";
    case PALISSADE = "PALISSAGE";
    case CASERNE = "CASERNE";
    case TOUR_DE_GARDE = "TOUR_DE_GARDE";
    case OFFICINE = "OFFICINE";
    case ATELIER = "ATELIER";
    case SCRIPTORIUM = "SCRIPTORIUM";


    /**
     * @param int $age
     * @return Card[]
     */
    #[Pure] function cards(int $playersCount, int $age): array {
        switch ($this) {
            case self::SCIERIE:
            case self::FILON:
            case self::TOUR_DE_GARDE:
            case self::SCRIPTORIUM:
                return $playersCount === 3 ? [new Card($this)] : [new Card($this), new Card($this)];
            case self::CARRIERE:
            case self::BASSIN_ARGILEUX:
            case self::VERRERIE:
            case self::PRESSE:
            case self::METIER_A_TISSER:
            case self::AUTEL:
            case self::CASERNE:
            case self::OFFICINE:
                return $playersCount < 5 ? [new Card($this)] : [new Card($this), new Card($this)];
            case self::FRICHE:
            case self::MINE:
            case self::THEATRE:
                return $playersCount > 5 ? [new Card($this)] : [];
            case self::EXCAVATION:
            case self::PRETEUR_SUR_GAGE:
            case self::ATELIER:
                return $playersCount < 7 ? [new Card($this)] : [new Card($this), new Card($this)];
            case self::FOSSE_ARGILEUSE:
            case self::EXPLOITATION_FORESTIERE:
                return [new Card($this)];
            case self::GISEMENT:
                return $playersCount < 5 ? [] : [new Card($this)];
            case self::BAINS:
            case self::COMPTOIR_OUEST:
            case self::COMPTOIR_EST:
            case self::PALISSADE:
                return $playersCount > 6 ? [new Card($this)] : [new Card($this), new Card($this)];
            case self::TAVERNE:
                if ($playersCount === 3) return [];
                if ($playersCount === 4) return [new Card($this)];
                if ($playersCount < 7) return [new Card($this), new Card($this)];
                return [new Card($this), new Card($this), new Card($this)];
            case self::MARCHE:
                return $playersCount < 6 ? [new Card($this)] : [new Card($this), new Card($this)];
        }
    }
}
