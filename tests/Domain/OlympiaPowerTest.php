<?php

namespace App\Tests\Domain;

use App\Domain\Action;
use App\Domain\Age;
use App\Domain\Card\CardAction;
use App\Domain\Card\CardType;
use App\Domain\GameException;
use App\Domain\Hand;
use App\Domain\PlayCard\PlayCard;
use App\Domain\Player;
use App\Domain\SevenWonders;
use App\Domain\Wonder\WonderType;
use App\Tests\Domain\Builder\HandBuilder;
use App\Tests\Domain\Builder\WonderBuilder;
use PHPUnit\Framework\TestCase;

class OlympiaPowerTest extends TestCase
{
    /**
     * @throws GameException
     */
    public function test_olympia_night_should_build_structure_of_last_card_for_free_when_second_stage_build()
    {
        // GIVEN
        $age = Age::first(3);
        $game = $this->givenOlympiaWithTwoStagesBuilt($age, HandBuilder::with(CardType::BAINS, CardType::CASERNE)->build());

        // WHEN
        $olympiaPlayer = $game->findPlayer(1);
        $availableActions = $olympiaPlayer->availableActions($game->state->neighboursOf(1));

        // THEN
        $this->assertContainsEquals(new CardAction(CardType::CASERNE, Action::BUILD_STRUCTURE), $availableActions);
        $this->assertContainsEquals(new CardAction(CardType::BAINS, Action::BUILD_STRUCTURE), $availableActions);
    }

    /**
     * @throws GameException
     */
    public function test_olympia_night_should_build_structure_of_first_card_for_free_when_first_stage_built()
    {
        // GIVEN
        $age = Age::second(3);
        $game = $this->givenOlympiaWithTwoStagesBuilt($age);

        // WHEN
        $olympiaPlayer = $game->findPlayer(1);
        $availableActions = $olympiaPlayer->availableActions($game->state->neighboursOf(1));

        // THEN
        $buildStructureActions = array_values(array_filter($availableActions, fn($cardAction) => $cardAction->action() === Action::BUILD_STRUCTURE));
        $buildStructureActionsTrades = array_map(fn(CardAction $cardAction) => $cardAction->trade(), $buildStructureActions);
        $this->assertCount(7, $buildStructureActions);
        $this->assertEquals([null, null, null, null, null, null, null], $buildStructureActionsTrades);
    }

    /**
     * @param Age $age
     * @return SevenWonders
     */
    private function givenOlympiaWithTwoStagesBuilt(Age $age, ?Hand $withHand = null): SevenWonders
    {
        return new SevenWonders(1, new PlayCard(
            $age,
            array(
                new Player(
                    1,
                    WonderBuilder::of(WonderType::OLYMPIA)->withStageBuild(2)->withCards(CardType::VERRERIE_1, CardType::PRESSE_1, CardType::METIER_A_TISSER_1)->build(),
                    $withHand === null ? HandBuilder::of($age)->build() : $withHand,
                    null
                ),
                new Player(
                    2,
                    WonderBuilder::of(WonderType::EPHESOS)->build(),
                    HandBuilder::of($age, 2)->build(),
                    null
                ),
                new Player(
                    3,
                    WonderBuilder::of(WonderType::BABYLON)->build(),
                    HandBuilder::of($age, 2)->build(),
                    null
                ),
            ),
            []
        ));
    }

}
