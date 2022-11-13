<?php

namespace App\Tests\Domain;

use App\Domain\Action;
use App\Domain\Card\CardAction;
use App\Domain\Card\CardType;
use App\Domain\GameException;
use App\Domain\GameExceptionType;
use App\Domain\Player;
use App\Domain\SevenWonders;
use App\Domain\Wonder\WonderFace;
use App\Domain\Wonder\WonderPowerType;
use App\Domain\Wonder\WonderType;
use PHPUnit\Framework\TestCase;

class HalikarnassosPowerTest extends TestCase
{
    private SevenWonders $game;
    private int $babylon = 3;
    private array $userIds = [1, 2, 3, 4];
    private Player $hali;

    public function setUp(): void
    {
        $this->game = SevenWonders::start(1, $this->userIds, false, [WonderType::HALIKARNASSOS, WonderType::OLYMPIA, WonderType::BABYLON, WonderType::GIZAH], [WonderFace::NIGHT]);
        $this->hali = $this->game->players()[0];
    }

    // DURING AGE

    /**
     * @throws GameException
     */
    public function test_should_not_end_round_before_hali_played()
    {
        $this->expectExceptionMessage(GameExceptionType::CARD_PLAY_NOT_ALLOWED->exception()->getMessage());
        $this->setupHaliUnburyCardToPlay();

        $this->autoPlayPreferringDiscardAvoidingBuildStage(2);
    }

    /**
     * @throws GameException
     */
    public function test_should_end_round_after_hali_played()
    {
        $this->setupHaliUnburyCardToPlay();

        if (count($this->game->discard()) > 0) {
            $this->game = $this->game->playCard($this->hali->id, $this->game->discard()[0]->name, Action::BUILD_STRUCTURE->name);
        }


        $this->autoPlayPreferringDiscardAvoidingBuildStage(1);
        $this->autoPlayPreferringDiscardAvoidingBuildStage(2);
        $this->autoPlayPreferringDiscardAvoidingBuildStage(3);
        $this->autoPlayPreferringDiscardAvoidingBuildStage(4);
        $this->assertTrue(true);
    }

    // END OF AGE - POWER APPLY

    /**
     * @throws GameException
     */
    public function test_when_hali_is_played_at_end_of_age_should_not_end_the_age_before_hali_power_play()
    {
        $this->setupHaliUnburyCardToPlayAtEndOfAge();

        $this->assertEquals(1, $this->game->age()->id);
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
     * @throws GameException
     */
    public function test_when_hali_is_played_at_end_of_age_should_be_triggered_after_babylon_power()
    {
        $this->setupHaliUnburyCardToPlayAtEndOfAge(true);

        // BABYLON
        $this->game = $this->autoPlayPreferringDiscardAvoidingBuildStage($this->babylon);

        // HALI
        $pickedCard = $this->game->discard()[0];
        $game = $this->game->playCard($this->hali->id, $pickedCard->name, Action::BUILD_STRUCTURE->name);

        $this->assertEquals(2, $game->age()->id);
    }

    /**
     * @param bool $withBabylon
     * @return void
     * @throws GameException
     */
    private function setupHaliUnburyCardToPlayAtEndOfAge(bool $withBabylon = false): void
    {
        $this->game = $this->game
            // first round
            ->playCard(1, CardType::BASSIN_ARGILEUX->value, Action::BUILD_STRUCTURE->value)
            ->playCard(2, CardType::PRESSE_1->value, Action::BUILD_STRUCTURE->value)
            ->playCard($this->babylon, CardType::COMPTOIR_EST->value, Action::BUILD_STRUCTURE->value)
            ->playCard(4, CardType::OFFICINE->value, Action::DISCARD->value);

        $userIds = [1, 2, $this->babylon, 4];
        for ($i = 1; $i < 5; $i++) {
            foreach ($userIds as $userId) {
                if ($withBabylon && $userId === $this->babylon) {
                    $this->game = $this->babylonAutoPlayTargetingStage($userId);

                } else {
                    $this->game = $this->autoPlayPreferringDiscardAvoidingBuildStage($userId);
                }
            }
        }

        $this->game = $this->game->playCard(1, CardType::TOUR_DE_GARDE->value, Action::BUILD_STAGE->value, "CLAY_1/");
        $this->game = $this->autoPlayPreferringDiscardAvoidingBuildStage(2);
        $this->game = $this->babylonAutoPlayTargetingStage($this->babylon);
        $this->game = $this->autoPlayPreferringDiscardAvoidingBuildStage(4);

        $this->assertTrue($this->game->players()[0]->wonder->stages->values[0]->isBuilt());
        $this->assertEquals(WonderPowerType::UNBURY_CARD, $this->game->players()[0]->wonder->stages->powerRequiresAction());

        $this->hali = $this->game->players()[0];
    }

    /**
     * @return void
     * @throws GameException
     */
    private function setupHaliUnburyCardToPlay(): void
    {
        $this->game = $this->game->playCard(1, CardType::BASSIN_ARGILEUX->value, Action::BUILD_STRUCTURE->value);
        $this->game = $this->autoPlayPreferringDiscardAvoidingBuildStage(2);
        $this->game = $this->autoPlayPreferringDiscardAvoidingBuildStage(3);
        $this->game = $this->autoPlayPreferringDiscardAvoidingBuildStage(4);

        $this->game = $this->game->playCard(1, CardType::CASERNE->value, Action::BUILD_STAGE->value, "CLAY_1/");
        $this->game = $this->autoPlayPreferringDiscardAvoidingBuildStage(2);
        $this->game = $this->autoPlayPreferringDiscardAvoidingBuildStage(3);
        $this->game = $this->autoPlayPreferringDiscardAvoidingBuildStage(4);

        $this->assertTrue($this->game->players()[0]->wonder->stages->values[0]->isBuilt());
        $this->assertEquals(WonderPowerType::UNBURY_CARD, $this->game->players()[0]->wonder->stages->powerRequiresAction());

        $this->hali = $this->game->players()[0];
    }

    /**
     * @param int $userId
     * @return SevenWonders
     * @throws GameException
     */
    private function autoPlayPreferringDiscardAvoidingBuildStage(int $userId): SevenWonders
    {
        $player = $this->game->findPlayer($userId);
        $availableActions = $player->availableActions($this->game->state->neighboursOf($player->id));
        $action = array_reduce($availableActions, function ($acc, CardAction $cardAction) {

            if ($cardAction->action() !== Action::BUILD_STAGE) {
                return $cardAction;
            }

            if ($cardAction->action() === Action::DISCARD) {
                return $cardAction;
            }
            return $acc;
        });
        return $this->game->playCard($userId, $action->cardType()->value, $action->action()->value, $action->trade()?->id());
    }

    /**
     * @param int $userId
     * @return SevenWonders
     * @throws GameException
     */
    private function babylonAutoPlayTargetingStage(int $userId): SevenWonders
    {
        $player = $this->game->findPlayer($userId);
        $availableActions = $player->availableActions($this->game->state->neighboursOf($player->id));
        $cardAction = array_reduce($availableActions, function ($acc, CardAction $cardAction) {
            $action = $cardAction->action();
            $cardType = $cardAction->cardType();

            if ($action === Action::BUILD_STAGE) {
                return $cardAction;
            }

            $stoneCardTypes = [CardType::CARRIERE_1, CardType::EXPLOITATION_FORESTIERE, CardType::EXCAVATION];
            if ($action === Action::BUILD_STRUCTURE && array_search($cardType, $stoneCardTypes)) {
                return $cardAction;
            }

            return $acc;
        });

        $finalAction = $cardAction === null ? $availableActions[0] : $cardAction;

        return $this->game->playCard($userId, $finalAction->cardType()->value, $finalAction->action()->value, $finalAction->trade()?->id());
    }
}
