<?php

namespace App\Tests;

use App\Domain\GameException;
use App\Domain\SevenWonders;
use JetBrains\PhpStorm\Pure;

class SevenTestOrchestrator
{
    private SevenWonders $game;

    public function __construct(SevenWonders $game)
    {
        $this->game = $game;
    }

    #[Pure] public static function of(SevenWonders $game): SevenTestOrchestrator
    {
        return new SevenTestOrchestrator($game);
    }

    /**
     * @throws GameException
     */
    public function autoPlay(int $untilAge): SevenWonders
    {

        while ($this->game->age()->id < $untilAge) {
            for ($round = 0; $round < 6; $round++) {
                $players = $this->game->state->players();
                foreach ($players as $player) {
                    $availableAction = $player->availableActions($this->game->state->neighboursOf($player->id))[0];
                    $this->game = $this->game->playCard($player->id, $availableAction->cardType()->name, $availableAction->action()->name, $availableAction->trade()?->id());
                }
            }
        }

        return $this->game;
    }


}
