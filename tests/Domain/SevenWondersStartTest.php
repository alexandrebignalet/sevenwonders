<?php

namespace App\Tests\Domain;

use App\Domain\Player;
use App\Domain\SevenWonders;
use App\Domain\Wonder\Neighbourhood;
use App\Domain\Wonder\WonderFace;
use App\Domain\Wonder\WonderType;
use PHPUnit\Framework\TestCase;

class SevenWondersStartTest extends TestCase
{
    private int $id = 1;
    private SevenWonders $game;
    private array $userIds = [1, 2, 3];
    private Player $playerOne;
    private Player $playerTwo;
    private Player $playerThree;


    public function setUp(): void
    {
        $this->game = SevenWonders::start($this->id, $this->userIds, false);
        $this->playerOne = $this->game->players()[0];
        $this->playerTwo = $this->game->players()[1];
        $this->playerThree = $this->game->players()[2];
    }

    public function test_should_start_at_age_one()
    {
        $this->assertEquals($this->game->id, $this->id);
        $this->assertEquals(1, $this->game->age()->id);
    }

    public function test_should_initialize_players_using_user_ids()
    {
        $this->assertEquals(array_map(fn(Player $player): string => $player->id, $this->game->players()), $this->userIds);
    }

    public function test_should_have_setup_neighbourhood()
    {
        $this->assertEquals($this->game->state->neighboursOf(1), new Neighbourhood($this->playerTwo->wonder, $this->playerThree->wonder));
        $this->assertEquals($this->game->state->neighboursOf(2), new Neighbourhood($this->playerThree->wonder, $this->playerOne->wonder));
        $this->assertEquals($this->game->state->neighboursOf(3), new Neighbourhood($this->playerOne->wonder, $this->playerTwo->wonder));
    }

    public function test_setup_wonders()
    {
        $this->assertEquals(WonderType::RHODOS->wonder(WonderFace::DAY), $this->playerOne->wonder);
        $this->assertEquals(WonderType::ALEXANDRIA->wonder(WonderFace::DAY), $this->playerTwo->wonder);
        $this->assertEquals(WonderType::GIZAH->wonder(WonderFace::DAY), $this->playerThree->wonder);
    }
}
