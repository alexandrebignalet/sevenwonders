<?php

namespace App\Domain\PlayCard;

use App\Domain\Action;
use App\Domain\GameException;
use App\Domain\Player;
use App\Domain\RotationDirectionFlow;
use App\Domain\Wonder\WonderPowerType;

class PlayCardStrategy extends Strategy
{

    /**
     * @throws GameException
     */
    public function play(Player $player, string $cardName, string $action, ?string $tradeId): Strategy
    {
        $playerPlayed = $player->play($this->neighboursOf($player->id), $cardName, $action, $tradeId);

        $players = array_map(fn(Player $player) => $player->id === $playerPlayed->id ? $playerPlayed : $player, $this->players);
        if (!$this->hasEveryonePlayed($players)) {
            return new PlayCardStrategy($this->age, $players, $this->discard);
        }


        $committedPlayers = array_map(function (Player $player) {
            list('cardAction' => $cardAction, 'player' => $player) = $player->commit();

            if ($cardAction->action() === Action::DISCARD) {
                $this->discard[] = $cardAction->cardType();
            }

            return $player;
        }, $players);


        $haliPlayer = $this->hasPowerToPlay($committedPlayers, WonderPowerType::UNBURY_CARD);

        if (!$this->isAgeOver($committedPlayers)) {
            if (isset($haliPlayer) && count($this->discard) > 0) {
                return new HalikarnassosPowerStrategy($haliPlayer, $this->age, $committedPlayers, $this->discard);
            }

            $rotatedHandPlayers = $this->rotatePlayersHand($committedPlayers);
            return new PlayCardStrategy($this->age, $rotatedHandPlayers, $this->discard);
        }

        $babylonPlayer = $this->hasPowerToPlay($committedPlayers, WonderPowerType::BUILD_LAST_CARD);
        if (isset($babylonPlayer)) {
            return new BabylonPowerStrategy($babylonPlayer, $this->age, $committedPlayers, $this->discard);
        }

        $endOfAgeDiscard = array_map(fn(Player $player) => $player->discardLastCard()['card'], $committedPlayers);

        if (isset($haliPlayer)) {
            return new HalikarnassosPowerStrategy($haliPlayer, $this->age, $committedPlayers, $endOfAgeDiscard);
        }

        $age = $this->age->next();
        $players = array_map(fn(Player $player) => $player->receives($age->distributeHand()), $committedPlayers);
        return new PlayCardStrategy($age, $players, array_merge($this->discard, $endOfAgeDiscard));

    }

    /**
     * @param Player[] $players
     * @return bool
     */
    public function hasEveryonePlayed(array $players): bool
    {
        return array_reduce($players, fn(bool $acc, Player $player): bool => $acc && ($player->selectedAction !== null), true);
    }

    /**
     * @param Player[] $players
     * @return bool
     */
    public function isAgeOver(array $players): bool
    {
        return array_reduce($players, function (bool $isAgeOver, Player $player) {
            return $isAgeOver && $player->hand->size() === 1;
        }, true);
    }

    /**
     * @param Player[] $players
     * @return Player[]
     */
    private function rotatePlayersHand(array $players): array
    {
        $rotated = $players;
        $rotationDirectionFlow = $this->age->rotationDirectionFlow();
        $from = $rotationDirectionFlow === RotationDirectionFlow::CLOCKWISE ? 0 : count($players) - 1;
        $to = $rotationDirectionFlow === RotationDirectionFlow::CLOCKWISE ? count($players) : 0;
        $flow = $rotationDirectionFlow === RotationDirectionFlow::CLOCKWISE ? 1 : -1;

        for ($i = $from; $rotationDirectionFlow === RotationDirectionFlow::CLOCKWISE ? $i < $to : $i > $to; $i = $i + $flow) {

            $prevPlayerIndex = $i === $from ? $to === 0 ? $to : $to - 1 : $i - $flow;
            $prevPlayer = $players[$prevPlayerIndex];

            $currentPlayer = $players[$i];

            $rotated[$i] = $currentPlayer->receives($prevPlayer->hand->cards());
        }

        return $rotated;
    }
}
