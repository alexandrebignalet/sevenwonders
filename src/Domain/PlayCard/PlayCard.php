<?php

namespace App\Domain\PlayCard;

use App\Domain\Action;
use App\Domain\GameException;
use App\Domain\Player;
use App\Domain\Wonder\WonderPowerType;

class PlayCard extends Strategy
{

    /**
     * @throws GameException
     */
    public function play(Player $player, string $cardName, string $action, ?string $tradeId): Strategy
    {
        $playerPlayed = $player->play($this->neighboursOf($player->id), $cardName, $action, $tradeId);

        $players = array_map(fn(Player $player) => $player->id === $playerPlayed->id ? $playerPlayed : $player, $this->players);
        if (!$this->hasEveryonePlayed($players)) {
            return new PlayCard($this->age, $players, $this->discard);
        }


        $committedPlayers = array_map(function (Player $player) {
            list('cardAction' => $cardAction, 'player' => $player) = $player->commit();

            if ($cardAction->action() === Action::DISCARD) {
                $this->discard[] = $cardAction->cardType();
            }

            return $player;
        }, $players);


        $haliPlayer = $this->hasActionRequiredByWonderPower($committedPlayers, WonderPowerType::UNBURY_CARD);

        if (!$this->isAgeOver($committedPlayers)) {
            if (isset($haliPlayer) && count($this->discard) > 0) {
                return new HalikarnassosPowerStrategy($haliPlayer, $this->age, $committedPlayers, $this->discard);
            }

            return $this->setupNextRound($committedPlayers);
        }

        $babylonPlayer = $this->hasActionRequiredByWonderPower($committedPlayers, WonderPowerType::BUILD_LAST_CARD);
        if (isset($babylonPlayer)) {
            return new BabylonPowerStrategy($babylonPlayer, $this->age, $committedPlayers, $this->discard);
        }

        list('players' => $endOfAgePlayers, 'discard' => $endOfAgeDiscard) = $this->discardEachPlayerLastCard($committedPlayers, $this->discard);

        $halikarnassosPlay = $this->halikarnassosPowerOrNull($endOfAgePlayers, $endOfAgeDiscard);
        if (isset($halikarnassosPlay)) {
            return $halikarnassosPlay;
        };

        return $this->setupNextAge($endOfAgePlayers, $endOfAgeDiscard);

    }

    /**
     * @param Player[] $players
     * @return bool
     */
    public function hasEveryonePlayed(array $players): bool
    {
        return array_reduce($players, fn(bool $acc, Player $player): bool => $acc && ($player->selectedAction !== null), true);
    }
}
