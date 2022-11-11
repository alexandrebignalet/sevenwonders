<?php

namespace App\Tests\Domain;

use App\Domain\Action;
use App\Domain\Card\CardAction;
use App\Domain\Card\CardType;
use App\Domain\GameException;
use App\Domain\Player;
use App\Domain\SevenWonders;
use App\Domain\Wonder\WonderFace;
use App\Domain\Wonder\WonderPowerType;
use App\Domain\Wonder\WonderType;
use PHPUnit\Framework\TestCase;

class HalikarnassosPowerTest extends TestCase
{
    private SevenWonders $game;
    private array $userIds = [1, 2, 3];
    private Player $hali;

    public function setUp(): void
    {
        $this->game = SevenWonders::start(1, $this->userIds, false, [WonderType::HALIKARNASSOS, WonderType::OLYMPIA, WonderType::GIZAH], [WonderFace::NIGHT]);
        $this->hali = $this->game->players()[0];
    }

    // DURING AGE


    // END OF AGE - POWER APPLY
    /**
     * @throws GameException
     */
    public function test_when_hali_is_played_at_end_of_age_should_not_end_the_age_before_hali_power_play()
    {
        $this->setupHaliUnburyCardToPlayAtEndOfAge();

        $this->assertEquals($this->game->age()->id, 1);
    }

    /**
     * @throws GameException
     */
    public function test_when_hali_is_played_at_end_of_age_should_block_other_players_plays()
    {
        $this->setupHaliUnburyCardToPlayAtEndOfAge();

        foreach ($this->game->players() as $player) {
            $this->assertEquals(0, $player->hand->size());
        }
    }

    /**
     * @throws GameException
     */
    public function test_when_hali_is_played_at_end_of_age_should_go_to_next_age_after_play()
    {
        $this->setupHaliUnburyCardToPlayAtEndOfAge();

        $pickedCard = $this->game->discard()[0];
        $game = $this->game->playCard($this->hali->id, $pickedCard->name, Action::BUILD_STRUCTURE->name);

        $this->assertEquals(2, $game->age()->id);
    }

    /**
     * @return void
     * @throws GameException
     */
    private function setupHaliUnburyCardToPlayAtEndOfAge(): void
    {
        $this->game = $this->game
            // first round
            ->playCard(1, CardType::BASSIN_ARGILEUX->value, Action::BUILD_STRUCTURE->value)
            ->playCard(2, CardType::PRESSE_1->value, Action::BUILD_STRUCTURE->value)
            ->playCard(3, CardType::COMPTOIR_EST->value, Action::BUILD_STRUCTURE->value);

        $userIds = [1, 2, 3];
        for ($i = 1; $i < 5; $i++) {
            foreach ($userIds as $userId) {
                $this->game = $this->autoPlayRandomCard($userId);
            }
        }

        $this->game = $this->game->playCard(1, CardType::METIER_A_TISSER_1->value, Action::BUILD_STAGE->value, "CLAY_1/");

        $this->assertTrue($this->game->players()[0]->wonder->stages->values[0]->isBuilt());
        $this->assertEquals($this->game->players()[0]->wonder->stages->powerRequiresAction(), WonderPowerType::UNBURY_CARD);

        $this->game = $this->autoPlayRandomCard(2);
        $this->game = $this->autoPlayRandomCard(3);

        $this->hali = $this->game->players()[0];
    }

    /**
     * @param int $userId
     * @return SevenWonders
     * @throws GameException
     */
    private function autoPlayRandomCard(int $userId): SevenWonders
    {
        $player = $this->game->findPlayer($userId);
        $availableActions = $player->hand->availableActions($player->wonder, $this->game->state->neighboursOf($player->id));
        $action = array_reduce($availableActions, function ($acc, CardAction $cardAction) {
            if ($cardAction->action() !== Action::BUILD_STAGE) {
                return $cardAction;
            }
            return $acc;
        });
        return $this->game->playCard($userId, $action->cardType()->value, $action->action()->value, $action->trade()?->id());
    }

}
