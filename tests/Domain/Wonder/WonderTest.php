<?php

namespace App\Tests\Domain\Wonder;

use App\Domain\Action;
use App\Domain\Age;
use App\Domain\Card\CardAction;
use App\Domain\Card\CardType;
use App\Domain\Card\Offer;
use App\Domain\Card\Trade;
use App\Domain\Player;
use App\Domain\Resource\Resource;
use App\Domain\Resource\Resources;
use App\Domain\Wonder\Neighbourhood;
use App\Domain\Wonder\Structures;
use App\Domain\Wonder\Wonder;
use App\Domain\Wonder\WonderFace;
use App\Domain\Wonder\WonderType;
use JetBrains\PhpStorm\Pure;
use PHPUnit\Framework\TestCase;

class WonderTest extends TestCase
{


    public function test_available_choices_for_stages()
    {
        $leftNeighbourWonderType = WonderType::GIZAH;
        $leftNeighbourWonderFace = WonderFace::DAY;
        $leftNeighbour = new Wonder(
            type: $leftNeighbourWonderType,
            face: $leftNeighbourWonderFace,
            coins: 3,
            structures: new Structures(CardType::BASSIN_ARGILEUX, CardType::FOSSE_ARGILEUSE),
            stages: $leftNeighbourWonderType->stages($leftNeighbourWonderFace),
        );

        $rightNeighbourWonderType = WonderType::ALEXANDRIA;
        $rightNeighbourWonderFace = WonderFace::DAY;
        $rightNeighbour = new Wonder(
            type: $rightNeighbourWonderType,
            face: $rightNeighbourWonderFace,
            coins: 3,
            structures: new Structures(CardType::SCIERIE_1),
            stages: $rightNeighbourWonderType->stages($rightNeighbourWonderFace),
        );

        $wonderType = WonderType::RHODOS;
        $wonderFace = WonderFace::DAY;
        $wonder = new Wonder(
            type: $wonderType,
            face: $wonderFace,
            coins: 3,
            structures: new Structures(CardType::EXPLOITATION_FORESTIERE),
            stages: $wonderType->stages($wonderFace),
        );

        $neighbourhood = new Neighbourhood($leftNeighbour, $rightNeighbour);

        $player = Player::initialize(1, Age::first(3), $wonder);

        $actions = $player->availableActionsFor(CardType::SCIERIE_1->card(), $neighbourhood);

        $this->assertEquals($actions, [
            new CardAction(CardType::SCIERIE_1, Action::BUILD_STAGE, new Trade(leftNeighbourOffer: new Offer($leftNeighbour, new Resource()), rightNeighbourOffer: new Offer($rightNeighbour, new Resource(wood: 1)))),
            new CardAction(CardType::SCIERIE_1, Action::BUILD_STRUCTURE),
            new CardAction(CardType::SCIERIE_1, Action::DISCARD),
        ]);
    }

    /**
     * @dataProvider provideWonderDefaultResource
     */
    public function test_wonders_default_resource(WonderType $wonderType, Resources $expected)
    {
        $day = $wonderType->wonder(WonderFace::DAY);
        $night = $wonderType->wonder(WonderFace::NIGHT);

        $this->assertEquals($day->resources(), $expected);
        $this->assertEquals($night->resources(), $expected);
    }

    #[Pure] public function provideWonderDefaultResource(): array
    {
        return array(
            array(WonderType::RHODOS, Resources::single(ore: 1)),
            array(WonderType::GIZAH, Resources::single(stone: 1)),
            array(WonderType::BABYLON, Resources::single(wood: 1)),
            array(WonderType::OLYMPIA, Resources::single(clay: 1)),
            array(WonderType::EPHESOS, Resources::single(papyrus: 1)),
            array(WonderType::ALEXANDRIA, Resources::single(glass: 1)),
            array(WonderType::HALIKARNASSOS, Resources::single(cloth: 1)),
        );
    }
}
