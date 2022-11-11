<?php

namespace App\Tests\Domain;

use App\Domain\GameException;
use App\Domain\SevenWonders;
use PHPUnit\Framework\TestCase;

class SevenWondersAgeRotationTest extends TestCase
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
    public function test_should_go_to_next_age_when_each_players_played_6_cards_and_no_power_to_apply()
    {
        $game = $this->game;
        for ($i = 0; $i < 6; $i++) {
            foreach ($this->userIds as $userId) {
                $player = $game->findPlayer($userId);
                $neighbourhood = $game->state->neighboursOf($player->id);
                $availableActions = $player->hand->availableActions($player->wonder, $neighbourhood)[0];
                $game = $game->playCard($userId, $availableActions->cardType()->value, $availableActions->action()->value, $availableActions->trade()?->id());
            }
        }

        $this->assertCount(3, $game->discard());
        $this->assertEquals(2, $game->age()->id);

        foreach ($this->userIds as $userId) {
            $player = $game->findPlayer($userId);
            $this->assertCount(7, $player->hand->cards());
        }
    }
}
