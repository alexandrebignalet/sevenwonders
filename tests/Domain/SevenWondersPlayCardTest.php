<?php

namespace App\Tests\Domain;

use App\Domain\Action;
use App\Domain\Card\CardAction;
use App\Domain\Card\CardType;
use App\Domain\GameException;
use App\Domain\GameExceptionType;
use App\Domain\SevenWonders;
use PHPUnit\Framework\TestCase;

class SevenWondersPlayCardTest extends TestCase
{
    private SevenWonders $game;
    private array $userIds = [1, 2, 3];

    public function setUp(): void
    {
        $this->game = SevenWonders::start(1, $this->userIds, false);
    }

    /**
     * @throws GameException
     */
    public function test_should_not_allow_an_unknown_player_to_play()
    {
        $this->expectExceptionMessage(GameExceptionType::PLAYER_NOT_FOUND->exception()->getMessage());

        $this->game->playCard(10, 'Panthéon', 'build_structure');
    }

    /**
     * @throws GameException
     */
    public function test_should_not_allow_to_play_a_card_not_in_player_hand()
    {
        $this->expectExceptionMessage('"Panthéon" is not a valid backing value for enum "App\Domain\Card\CardType"');

        $this->game->playCard(1, 'Panthéon', 'build_structure');
    }

    /**
     * @throws GameException
     */
    public function test_should_not_allow_unknown_action()
    {
        $this->expectExceptionMessage('"dance" is not a valid backing value for enum "App\Domain\Action"');

        $cardName = CardType::CARRIERE_1->value;
        $this->game->playCard(1, $cardName, 'dance');
    }

    /**
     * @throws GameException
     */
    public function test_should_set_a_selection_action_when_card_play_allowed()
    {
        $cardName = CardType::CARRIERE_1->value;
        $game = $this->game->playCard(1, $cardName, Action::BUILD_STRUCTURE->value);

        $this->assertEquals(
            new CardAction(CardType::CARRIERE_1, Action::BUILD_STRUCTURE),
            $game->players()[0]->selectedAction
        );
    }

    /**
     * @throws GameException
     */
    public function test_should_throw_when_a_build_structure_play_not_allowed()
    {
        $this->expectExceptionMessage(GameExceptionType::CARD_PLAY_NOT_ALLOWED->exception()->getMessage());

        $cardName = CardType::BAINS->value;
        $this->game->playCard(2, $cardName, Action::BUILD_STRUCTURE->value);
    }

    /**
     * @throws GameException
     */
    public function test_should_throw_when_build_stage_play_not_allowed()
    {
        $this->expectExceptionMessage(GameExceptionType::CARD_PLAY_NOT_ALLOWED->exception()->getMessage());

        $cardName = CardType::BAINS->value;
        $this->game->playCard(1, $cardName, Action::BUILD_STAGE->value);
    }

    /**
     * @throws GameException
     */
    public function test_should_commit_the_plays_when_every_player_played()
    {

        $initialPlayerOneHand = $this->game->players()[0]->hand->cards();
        $initialPlayerTwoHand = $this->game->players()[1]->hand->cards();
        $initialPlayerThreeHand = $this->game->players()[2]->hand->cards();

        $cardName = CardType::CARRIERE_1->value;
        $game = $this->game->playCard(1, $cardName, Action::BUILD_STRUCTURE->value);

        $cardName = CardType::PRESSE_1->value;
        $game = $game->playCard(2, $cardName, Action::BUILD_STRUCTURE->value);

        $cardName = CardType::COMPTOIR_EST->value;
        $game = $game->playCard(3, $cardName, Action::BUILD_STRUCTURE->value);


        $newPlayerOneHand = $game->players()[0]->hand->cards();
        $newPlayerTwoHand = $game->players()[1]->hand->cards();
        $newPlayerThreeHand = $game->players()[2]->hand->cards();

        $this->assertCount(6, $newPlayerOneHand);
        $this->assertCount(6, $newPlayerTwoHand);
        $this->assertCount(6, $newPlayerThreeHand);

        $expectedNewHandOne = array_values(array_filter($initialPlayerThreeHand, fn($card) => $card->type !== CardType::COMPTOIR_EST));
        $expectedNewHandTwo = array_values(array_filter($initialPlayerOneHand, fn($card) => $card->type !== CardType::CARRIERE_1));
        $expectedNewHandThree = array_values(array_filter($initialPlayerTwoHand, fn($card) => $card->type !== CardType::PRESSE_1));

        $this->assertEquals($newPlayerOneHand, $expectedNewHandOne);
        $this->assertEquals($newPlayerTwoHand, $expectedNewHandTwo);
        $this->assertEquals($newPlayerThreeHand, $expectedNewHandThree);
    }

    /**
     * @throws GameException
     */
    public function test_should_allow_stage_building_when_resources_available()
    {
        // GIVEN
        $game = $this->game
            // first round
            ->playCard(1, CardType::EXPLOITATION_FORESTIERE->value, Action::BUILD_STRUCTURE->value)
            ->playCard(2, CardType::PRESSE_1->value, Action::BUILD_STRUCTURE->value)
            ->playCard(3, CardType::COMPTOIR_EST->value, Action::BUILD_STRUCTURE->value)
            // second round
            ->playCard(1, CardType::CASERNE->value, Action::BUILD_STRUCTURE->value)
            ->playCard(2, CardType::SCIERIE_1->value, Action::BUILD_STRUCTURE->value)
            ->playCard(3, CardType::AUTEL->value, Action::BUILD_STRUCTURE->value)
            // third round
            ->playCard(1, CardType::BAINS->value, Action::BUILD_STAGE->value, "WOOD_1/")
            ->playCard(2, CardType::TOUR_DE_GARDE->value, Action::DISCARD->value)
            ->playCard(3, CardType::BASSIN_ARGILEUX->value, Action::BUILD_STRUCTURE->value);

        $this->assertTrue($game->players()[0]->wonder->stages->values[0]->isBuilt());
    }

}
