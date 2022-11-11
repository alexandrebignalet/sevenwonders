<?php

namespace App\Tests\Domain;

use App\Domain\Action;
use App\Domain\Card\CardType;
use App\Domain\GameException;
use App\Domain\GameExceptionType;
use App\Domain\Player;
use App\Domain\SevenWonders;
use App\Domain\Wonder\WonderFace;
use App\Domain\Wonder\WonderPowerType;
use App\Domain\Wonder\WonderType;
use PHPUnit\Framework\TestCase;

class BabylonPowerTest extends TestCase
{
    private SevenWonders $game;
    private array $userIds = [1, 2, 3];
    private Player $babylon;

    public function setUp(): void
    {
        $this->game = SevenWonders::start(1, $this->userIds, false, [WonderType::BABYLON, WonderType::RHODOS, WonderType::GIZAH], [WonderFace::NIGHT]);
        $this->babylon = $this->game->players()[0];
    }

    /**
     * @throws GameException
     */
    public function test_should_not_end_the_age_before_babylon_play()
    {
        $this->setupBabylonLastCardToPlay();

        $this->assertEquals($this->game->age()->id, 1);
    }

    /**
     * @throws GameException
     */
    public function test_should_block_other_players_plays()
    {
        $this->expectExceptionMessage(GameExceptionType::CARD_PLAY_NOT_ALLOWED->exception()->getMessage());
        $this->setupBabylonLastCardToPlay();

        $notBabylonUser = $this->game->players()[1];
        $availableAction = $notBabylonUser->hand->availableActions($notBabylonUser->wonder, $this->game->state->neighboursOf($notBabylonUser->id))[0];
        $this->game->playCard($notBabylonUser->id, $availableAction->cardType()->value, $availableAction->action()->value, $availableAction->trade()?->id());
    }

    /**
     * @throws GameException
     */
    public function test_should_go_to_next_age_after_play()
    {
        $this->setupBabylonLastCardToPlay();

        $availableAction = $this->babylon->hand->availableActions($this->babylon->wonder, $this->game->state->neighboursOf($this->babylon->id))[0];
        $game = $this->game->playCard($this->babylon->id, $availableAction->cardType()->value, $availableAction->action()->value, $availableAction->trade()?->id());

        $this->assertEquals(2, $game->age()->id);
    }

    /**
     * @return void
     * @throws GameException
     */
    private function setupBabylonLastCardToPlay(): void
    {
        $this->game = $this->game
            // first round
            ->playCard(1, CardType::CARRIERE_1->value, Action::BUILD_STRUCTURE->value)
            ->playCard(2, CardType::PRESSE_1->value, Action::BUILD_STRUCTURE->value)
            ->playCard(3, CardType::COMPTOIR_EST->value, Action::BUILD_STRUCTURE->value)
            // second round
            ->playCard(1, CardType::PALISSADE->value, Action::BUILD_STAGE->value, "/STONE_1")
            ->playCard(2, CardType::SCIERIE_1->value, Action::BUILD_STRUCTURE->value)
            ->playCard(3, CardType::AUTEL->value, Action::BUILD_STRUCTURE->value);

        $this->assertTrue($this->game->players()[0]->wonder->stages->values[0]->isBuilt());
        $this->assertEquals($this->game->players()[0]->wonder->stages->powerRequiresAction(), WonderPowerType::BUILD_LAST_CARD);

        $userIds = [1, 2, 3];
        for ($i = 2; $i < 6; $i++) {
            foreach ($userIds as $userId) {
                $player = $this->game->findPlayer($userId);
                $availableAction = $player->hand->availableActions($player->wonder, $this->game->state->neighboursOf($player->id))[0];
                $this->game = $this->game->playCard($userId, $availableAction->cardType()->value, $availableAction->action()->value, $availableAction->trade()?->id());
            }
        }
        $this->babylon = $this->game->players()[0];
    }

}
