<?php

namespace App\Tests\Domain;

use App\Domain\Age;
use App\Domain\Player;
use App\Domain\SevenWonders;
use PHPUnit\Framework\TestCase;

class SevenWondersStartTest extends TestCase
{
    private int $id = 1;
    private SevenWonders $game;
    private array $userIds = [1, 2, 3];

    public function setUp(): void
    {
        $this->game = SevenWonders::start($this->id, $this->userIds, false);
    }

    public function test_should_start_at_age_one() {
        $this->assertEquals($this->game->id(), $this->id);
        $this->assertEquals($this->game->age(), Age::first(3, false));
    }

    public function test_should_initialize_players_using_user_ids() {
        $this->assertEquals(array_map(fn(Player $player): string => $player->id(), $this->game->players()), $this->userIds);
    }

    public function test_should_have_setup_neighbourhood() {
        $this->assertEquals($this->game->players()[0]->neighbours(), [$this->game->players()[2], $this->game->players()[1]]);
        $this->assertEquals($this->game->players()[1]->neighbours(), [$this->game->players()[0], $this->game->players()[2]]);
        $this->assertEquals($this->game->players()[2]->neighbours(), [$this->game->players()[1], $this->game->players()[0]]);
    }
}
