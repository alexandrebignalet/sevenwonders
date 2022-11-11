<?php

namespace App\Domain\PlayCard;

use App\Domain\Age;
use App\Domain\Card\CardType;
use App\Domain\GameException;
use App\Domain\GameExceptionType;
use App\Domain\Player;
use App\Domain\Wonder\Neighbourhood;
use JetBrains\PhpStorm\Pure;

class BabylonPowerStrategy extends Strategy
{
    private Player $babylonPlayer;


    /**
     * @param Player $babylonPlayer
     * @param Age $age
     * @param Player[] $players
     * @param CardType[] $discard
     */
    #[Pure] public function __construct(Player $babylonPlayer, Age $age, array $players, array $discard)
    {
        parent::__construct($age, $players, $discard);
        $this->babylonPlayer = $babylonPlayer;
    }


    /**
     * @throws GameException
     * @throws \Exception
     */
    public function play(Player $player, string $cardName, string $action, ?string $tradeId): Strategy
    {
        if ($player->id !== $this->babylonPlayer->id) {
            throw GameExceptionType::CARD_PLAY_NOT_ALLOWED->exception();
        }

        $babylon = $this->babylonPlayer
            ->play($this->neighbourhood(), $cardName, $action, $tradeId)
            ->commit();

        $nextAge = $this->age->next();
        $players = array_map(fn(Player $player) => $player->id === $babylon->id ? $babylon : $player, $this->players);
        return new PlayCardStrategy($nextAge, $players, $this->discard);
    }

    public function neighbourhood(): Neighbourhood
    {
        return $this->neighboursOf($this->babylonPlayer->id);
    }
}
