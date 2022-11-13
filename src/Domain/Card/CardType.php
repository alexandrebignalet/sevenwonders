<?php

namespace App\Domain\Card;

use App\Domain\Resource\Resource;
use App\Domain\Resource\Resources;
use App\Domain\Wonder\ResourceCard;
use App\Domain\Wonder\WarCard;
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
                return new ResourceCard($this, Cost::free(), Resources::single(wood: 1));
            case self::FILON:
                return new ResourceCard($this, Cost::free(), Resources::single(ore: 1));
            case self::CARRIERE_1:
                return new ResourceCard($this, Cost::free(), Resources::single(stone: 1));
            case self::BASSIN_ARGILEUX:
                return new ResourceCard($this, new Cost(coins: 1), Resources::single(clay: 1));
            case self::VERRERIE_2:
            case self::VERRERIE_1:
                return new ResourceCard($this, Cost::free(), Resources::single(glass: 1));
            case self::METIER_A_TISSER_2:
            case self::METIER_A_TISSER_1:
                return new ResourceCard($this, Cost::free(), Resources::single(cloth: 1));
            case self::FRICHE:
                return new ResourceCard($this, new Cost(coins: 1), new Resources(new Resource(wood: 1), new Resource(clay: 1)));
            case self::MINE:
                return new ResourceCard($this, new Cost(coins: 1), new Resources(new Resource(ore: 1), new Resource(stone: 1)));
            case self::EXCAVATION:
                return new ResourceCard($this, new Cost(coins: 1), new Resources(new Resource(clay: 1), new Resource(stone: 1)));
            case self::FOSSE_ARGILEUSE:
                return new ResourceCard($this, new Cost(coins: 1), new Resources(new Resource(clay: 1), new Resource(ore: 1)));
            case self::EXPLOITATION_FORESTIERE:
                return new ResourceCard($this, new Cost(coins: 1), new Resources(new Resource(wood: 1), new Resource(stone: 1)));
            case self::GISEMENT:
                return new ResourceCard($this, new Cost(coins: 1), new Resources(new Resource(wood: 1), new Resource(ore: 1)));
            case self::PRESSE_2:
            case self::PRESSE_1:
                return new ResourceCard($this, Cost::free(), Resources::single(papyrus: 1));
            case self::TOUR_DE_GARDE:
                return new WarCard($this, new Cost(resource: new Resource(clay: 1)), 1);
            case self::CASERNE:
                return new WarCard($this, new Cost(resource: new Resource(ore: 1)), 1);
            case self::PALISSADE:
                return new WarCard($this, new Cost(resource: new Resource(wood: 1)), 1);
            case self::COMPTOIR_OUEST:
            case self::COMPTOIR_EST:
            case self::BAZAR:
            case self::VIGNOBLE:
            case self::TAVERNE:
            case self::MARCHE:
                return new TradeCard($this, Cost::free());
            case self::OFFICINE:
                return new ScienceCard($this, new Cost(resource: new Resource(cloth: 1)), ScienceSymbol::COMPASS);
            case self::THEATRE:
            case self::AUTEL:
            case self::PRETEUR_SUR_GAGE:
                return new CivilianCard($this, Cost::free(), 3);
            case self::SCRIPTORIUM:
                return new ScienceCard($this, new Cost(resource: new Resource(papyrus: 1)), ScienceSymbol::TABLE);
            case self::ATELIER:
                return new ScienceCard($this, new Cost(resource: new Resource(glass: 1)), ScienceSymbol::WHEEL);
            case self::BAINS:
                return new CivilianCard($this, new Cost(resource: new Resource(stone: 1)), 3);

            // AGE 2
            case self::SCIERIE_2:
                return new ResourceCard($this, new Cost(coins: 1), Resources::single(wood: 2));
            case self::CARRIERE_2:
                return new ResourceCard($this, new Cost(coins: 1), Resources::single(stone: 2));
            case self::BRIQUETERIE:
                return new ResourceCard($this, new Cost(coins: 1), Resources::single(clay: 2));
            case self::FONDERIE:
                return new ResourceCard($this, new Cost(coins: 1), Resources::single(ore: 2));
            case self::PLACE_D_ARMES:
                return new WarCard($this, new Cost(resource: new Resource(ore: 2, wood: 1)), 2);
            case self::STATUE:
                return new CivilianCard($this, new Cost(resource: new Resource(ore: 2, wood: 1), chain: [CardType::PRETEUR_SUR_GAGE]), 4);
            case self::MURAILLE:
                return new WarCard($this, new Cost(resource: new Resource(stone: 3)), 2);
            case self::AQUEDUC:
                return new CivilianCard($this, new Cost(resource: new Resource(stone: 3), chain: [CardType::BAINS]), 5);
            case self::TEMPLE:
                return new CivilianCard($this, new Cost(resource: new Resource(clay: 1, wood: 1, glass: 1)), 4);
            case self::TRIBUNAL:
                return new CivilianCard($this, new Cost(resource: new Resource(clay: 2, cloth: 1), chain: [CardType::SCRIPTORIUM]), 4);
            case self::CARAVANSERAIL:
                return new TradeCard($this, new Cost(resource: new Resource(wood: 2), chain: [CardType::MARCHE]));
            case self::FORUM:
                return new TradeCard($this, new Cost(resource: new Resource(clay: 2), chain: [CardType::COMPTOIR_OUEST, CardType::COMPTOIR_EST]));
            case self::ECURIES:
                return new WarCard($this, new Cost(resource: new Resource(clay: 1, ore: 1, wood: 1), chain: [CardType::OFFICINE]), 2);
            case self::CHAMPS_DE_TIR:
                return new WarCard($this, new Cost(resource: new Resource(ore: 1, wood: 2), chain: [CardType::ATELIER]), 2);
            case self::DISPENSAIRE:
                return new ScienceCard($this, new Cost(resource: new Resource(ore: 2, glass: 1), chain: [CardType::OFFICINE]), ScienceSymbol::COMPASS);
            case self::LABORATOIRE:
                return new ScienceCard($this, new Cost(resource: new Resource(clay: 2, papyrus: 1), chain: [CardType::ATELIER]), ScienceSymbol::WHEEL);
            case self::BIBLIOTHEQUE:
                return new ScienceCard($this, new Cost(resource: new Resource(stone: 2, cloth: 1), chain: [CardType::SCRIPTORIUM]), ScienceSymbol::TABLE);
            case self::ECOLE:
                return new ScienceCard($this, new Cost(resource: new Resource(wood: 1, papyrus: 1)), ScienceSymbol::TABLE);
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
