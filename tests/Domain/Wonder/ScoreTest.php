<?php

namespace App\Tests\Domain\Wonder;

use App\Domain\Age;
use App\Domain\GameException;
use App\Domain\PlayCard\PlayCard;
use App\Domain\PlayCard\WarResult;
use App\Domain\Player;
use App\Domain\SevenWonders;
use App\Domain\WarSymbol;
use App\Domain\Wonder\WonderType;
use App\Tests\Domain\Builder\HandBuilder;
use App\Tests\Domain\Builder\WonderBuilder;
use App\Tests\SevenTestOrchestrator;
use PHPUnit\Framework\TestCase;

class ScoreTest extends TestCase
{
    /**
     * @throws GameException
     */
    public function test_should_resolve_war_points_at_the_end_of_age()
    {
        $age = Age::first(3, false);
        $game = new SevenWonders(
            1,
            state: new PlayCard(
                $age,
                array(
                    new Player(1, WonderBuilder::of(WonderType::RHODOS)->build(), HandBuilder::of($age)->build()),
                    new Player(2, WonderBuilder::of(WonderType::GIZAH)->build(), HandBuilder::of($age)->build()),
                    new Player(3, WonderBuilder::of(WonderType::ALEXANDRIA)->build(), HandBuilder::of($age)->build())
                ),
                []
            )
        );

        $game = SevenTestOrchestrator::of($game)->autoPlay(untilAge: 2);

        $this->assertContainsEquals(new WarSymbol(1, WarResult::LOSE), $game->state->players()[0]->warSymbols);
        $this->assertContainsEquals(new WarSymbol(1, WarResult::LOSE), $game->state->players()[0]->warSymbols);
        $this->assertEquals($game->state->players()[0]->warPoints(), -2);

        $this->assertContainsEquals(new WarSymbol(1, WarResult::WON), $game->state->players()[1]->warSymbols);
        $this->assertEquals($game->state->players()[1]->warPoints(), 1);

        $this->assertContainsEquals(new WarSymbol(1, WarResult::WON), $game->state->players()[2]->warSymbols);
        $this->assertEquals($game->state->players()[2]->warPoints(), 1);
    }
}
