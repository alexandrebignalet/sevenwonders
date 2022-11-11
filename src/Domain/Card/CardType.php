<?php

namespace App\Domain\Card;

use App\Domain\Resource\Resource;
use App\Domain\Resource\Resources;
use JetBrains\PhpStorm\Pure;

enum CardType: string
{
    case SCIERIE_1 = "SCIERIE_1";
    case CARRIERE_1 = "CARRIERE_1";
    case BASSIN_ARGILEUX = "BASSIN_ARGILEUX";
    case FILON = "FILON";
    case FRICHE = "FRICHE";
    case EXCAVATION = "EXCAVATION";
    case FOSSE_ARGILEUSE = "FOSSE_ARGILEUSE";
    case EXPLOITATION_FORESTIERE = "EXPLOITATION_FORESTIERE";
    case GISEMENT = "GISEMENT";
    case MINE = "MINE";
    case VERRERIE_1 = "VERRERIE_1";
    case PRESSE_1 = "PRESSE_1";
    case METIER_A_TISSER_1 = "METIER_A_TISSER_1";
    case PRETEUR_SUR_GAGE = "PRETEUR_SUR_GAGE";
    case BAINS = "BAINS";
    case AUTEL = "AUTEL";
    case THEATRE = "THEATRE";
    case TAVERNE = "TAVERNE";
    case MARCHE = "MARCHE";
    case COMPTOIR_OUEST = "COMPTOIR_OUEST";
    case COMPTOIR_EST = "COMPTOIR_EST";
    case PALISSADE = "PALISSADE";
    case CASERNE = "CASERNE";
    case TOUR_DE_GARDE = "TOUR_DE_GARDE";
    case OFFICINE = "OFFICINE";
    case ATELIER = "ATELIER";
    case SCRIPTORIUM = "SCRIPTORIUM";

    case SCIERIE_2 = "SCIERIE_2";
    case CARRIERE_2 = "CARRIERE_2";
    case BRIQUETERIE = "BRIQUETERIE";
    case FONDERIE = "FONDERIE";
    case VERRERIE_2 = "VERRERIE_2";
    case PRESSE_2 = "PRESSE_2";
    case METIER_A_TISSER_2 = "METIER_A_TISSER_2";
    case STATUE = "STATUE";
    case AQUEDUC = "AQUEDUC";
    case TEMPLE = "TEMPLE";
    case TRIBUNAL = "TRIBUNAL";
    case CARAVANSERAIL = "CARAVANSERAIL";
    case FORUM = "FORUM";
    case VIGNOBLE = "VIGNOBLE";
    case BAZAR = "BAZAR";
    case ECURIES = "ECURIES";
    case CHAMPS_DE_TIR = "CHAMPS_DE_TIR";
    case MURAILLE = "MURAILLE";
    case PLACE_D_ARMES = "PLACE_D_ARMES";
    case DISPENSAIRE = "DISPENSAIRE";
    case LABORATOIRE = "LABORATOIRE";
    case BIBLIOTHEQUE = "BIBLIOTHEQUE";
    case ECOLE = "ECOLE";

    /**
     * @return Card
     */
    #[Pure] function card(): Card
    {
        switch ($this) {
            // AGE 2
            case self::SCIERIE_1:
                return new Card($this, Cost::free(), new Resources(new Resource(wood: 1)));
            case self::FILON:
                return new Card($this, Cost::free(), new Resources(new Resource(ore: 1)));
            case self::CARRIERE_1:
                return new Card($this, Cost::free(), new Resources(new Resource(stone: 1)));
            case self::BASSIN_ARGILEUX:
            case self::VERRERIE_1:
            case self::METIER_A_TISSER_1:
            case self::FRICHE:
            case self::MINE:
            case self::EXCAVATION:
                return new Card($this, Cost::free(), Resources::single(clay: 1, stone: 1));
            case self::FOSSE_ARGILEUSE:
            case self::EXPLOITATION_FORESTIERE:
                return new Card($this, new Cost(coins: 1), new Resources(new Resource(wood: 1), new Resource(stone: 1)));
            case self::GISEMENT:
                return new Card($this, Cost::free(), Resources::empty());
            case self::PRESSE_2:
            case self::PRESSE_1:
                return new Card($this, Cost::free(), Resources::single(papyrus: 1));
            case self::TOUR_DE_GARDE:
            case self::CASERNE:
            case self::PALISSADE:
            case self::COMPTOIR_OUEST:
            case self::COMPTOIR_EST:
            case self::TAVERNE:
            case self::MARCHE:
            case self::OFFICINE:
            case self::SCRIPTORIUM:
            case self::ATELIER:
            case self::THEATRE:
            case self::AUTEL:
            case self::VIGNOBLE:
            case self::BAZAR:
            case self::PRETEUR_SUR_GAGE:
                return new Card($this, Cost::free());
            case self::BAINS:
                return new Card($this, Cost::stoneOnly(1));

            // AGE 2
            case self::SCIERIE_2:
                return new Card($this, new Cost(coins: 1), Resources::single(wood: 2));
            case self::CARRIERE_2:
                return new Card($this, new Cost(coins: 1), Resources::single(stone: 2));
            case self::BRIQUETERIE:
                return new Card($this, new Cost(coins: 1), Resources::single(clay: 2));
            case self::FONDERIE:
                return new Card($this, new Cost(coins: 1), Resources::single(ore: 2));
            case self::VERRERIE_2:
                return new Card($this, Cost::free(), Resources::single(glass: 1));
            case self::METIER_A_TISSER_2:
                return new Card($this, Cost::free(), Resources::single(cloth: 1));
            case self::PLACE_D_ARMES:
            case self::STATUE:
                return new Card($this, new Cost(resource: new Resource(ore: 2, wood: 1)));
            case self::MURAILLE:
            case self::AQUEDUC:
                return new Card($this, new Cost(resource: new Resource(stone: 3)));
            case self::TEMPLE:
                return new Card($this, new Cost(resource: new Resource(clay: 1, wood: 1, glass: 1)));
            case self::TRIBUNAL:
                return new Card($this, new Cost(resource: new Resource(clay: 2, cloth: 1)));
            case self::CARAVANSERAIL:
                return new Card($this, new Cost(resource: new Resource(wood: 2)));
            case self::FORUM:
                return new Card($this, new Cost(resource: new Resource(clay: 2)));
            case self::ECURIES:
                return new Card($this, new Cost(resource: new Resource(clay: 1, ore: 1, wood: 1)));
            case self::CHAMPS_DE_TIR:
                return new Card($this, new Cost(resource: new Resource(ore: 1, wood: 2)));
            case self::DISPENSAIRE:
                return new Card($this, new Cost(resource: new Resource(ore: 2, glass: 1)));
            case self::LABORATOIRE:
                return new Card($this, new Cost(resource: new Resource(clay: 2, papyrus: 1)));
            case self::BIBLIOTHEQUE:
                return new Card($this, new Cost(resource: new Resource(stone: 2, cloth: 1)));
            case self::ECOLE:
                return new Card($this, new Cost(resource: new Resource(wood: 1, papyrus: 1)));
        }
    }

    /**
     * @param int $playersCount
     * @return Card[]
     */
    #[Pure] function cards(int $playersCount): array
    {
        $card = $this->card();
        switch ($this) {
            // AGE 1
            case self::SCIERIE_1:
            case self::FILON:
            case self::TOUR_DE_GARDE:
            case self::FONDERIE:
            case self::BRIQUETERIE:
            case self::CARRIERE_2:
            case self::SCIERIE_2:
            case self::DISPENSAIRE:
            case self::SCRIPTORIUM:
                return $playersCount === 3 ? [$card] : [$card, $card];
            case self::CARRIERE_1:
            case self::BASSIN_ARGILEUX:
            case self::VERRERIE_1:
            case self::PRESSE_1:
            case self::METIER_A_TISSER_1:
            case self::AUTEL:
            case self::CASERNE:
            case self::ECURIES:
            case self::LABORATOIRE:
            case self::OFFICINE:
                return $playersCount < 5 ? [$card] : [$card, $card];
            case self::TEMPLE:
            case self::THEATRE:
                return $playersCount > 5 ? [$card, $card] : [$card];
            case self::PRETEUR_SUR_GAGE:
                return $playersCount === 3 ? [] : ($playersCount < 7 ? [$card] : [$card, $card]);
            case self::MURAILLE:
            case self::ECOLE:
            case self::ATELIER:
                return $playersCount < 7 ? [$card] : [$card, $card];
            case self::FOSSE_ARGILEUSE:
            case self::EXPLOITATION_FORESTIERE:
                return [$card];
            case self::GISEMENT:
                return $playersCount < 5 ? [] : [$card];
            case self::FRICHE:
            case self::MINE:
                return $playersCount < 6 ? [] : [$card];
            case self::BAINS:
            case self::COMPTOIR_OUEST:
            case self::COMPTOIR_EST:
            case self::AQUEDUC:
            case self::STATUE:
            case self::PALISSADE:
                return $playersCount > 6 ? [$card, $card] : [$card];
            case self::EXCAVATION:
                return $playersCount === 3 ? [] : [$card];
            case self::TAVERNE:
                if ($playersCount === 3) return [];
                if ($playersCount === 4) return [$card];
                if ($playersCount < 7) return [$card, $card];
                return [$card, $card, $card];
            case self::CHAMPS_DE_TIR:
            case self::BIBLIOTHEQUE:
            case self::MARCHE:
                return $playersCount < 6 ? [$card] : [$card, $card];
            // AGE 2
            case self::VERRERIE_2:
            case self::PRESSE_2:
            case self::TRIBUNAL:
            case self::METIER_A_TISSER_2:
                return $playersCount > 4 ? [$card, $card] : [$card];
            case self::CARAVANSERAIL:
                if ($playersCount < 5) {
                    return [$card];
                }
                return $playersCount === 5
                    ? [$card, $card]
                    : [$card, $card, $card];
            case self::FORUM:
                if ($playersCount < 6) {
                    return [$card];
                }
                return $playersCount === 6
                    ? [$card, $card]
                    : [$card, $card, $card];
            case self::VIGNOBLE:
                if ($playersCount < 6) {
                    return [$card];
                } else {
                    return [$card, $card];
                }
            case self::BAZAR:
                if ($playersCount === 3) {
                    return [];
                }
                return $playersCount <= 6
                    ? [$card]
                    : [$card, $card];
            case self::PLACE_D_ARMES:
                if ($playersCount === 3) {
                    return [];
                }
                if ($playersCount < 6) {
                    return [$card];
                }
                return $playersCount === 6 ? [$card, $card] : [$card, $card, $card];
        }
    }
}
