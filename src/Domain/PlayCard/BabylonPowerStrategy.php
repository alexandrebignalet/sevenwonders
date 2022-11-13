<?php

namespace App\Domain\PlayCard;

use App\Domain\Action;
use App\Domain\Age;
use App\Domain\Card\CardAction;
use App\Domain\Card\CardType;
use App\Domain\GameException;
use App\Domain\GameExceptionType;
use App\Domain\Player;
use App\Domain\Wonder\Neighbourhood;
use App\Domain\Wonder\WonderPowerType;
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

        /**
         * @var CardAction $cardAction
         * @var Player $player
         */
        list('cardAction' => $cardAction, 'player' => $babylon) = $this->babylonPlayer
            ->play($this->neighbourhood(), $cardName, $action, $tradeId)
            ->commit();

        if ($cardAction->action() === Action::DISCARD) {
            $this->discard[] = $cardAction->cardType();
        }

        $nextAge = $this->age->next();
        $players = array_map(fn(Player $player) => $player->id === $babylon->id ? $babylon : $player, $this->players);
        $playersLastCard = array_filter(array_map(fn(Player $player) => $player->discardLastCard()['card'], $players), fn(?CardType $cardType) => $cardType !== null);


        $haliPlayer = $this->hasPowerToPlay($players, WonderPowerType::UNBURY_CARD);
        $endOfAgeDiscard = array_merge($this->discard, $playersLastCard);

        if (isset($haliPlayer)) {
            return new HalikarnassosPowerStrategy($haliPlayer, $this->age, $players, $endOfAgeDiscard);
        }

        return new PlayCardStrategy($nextAge, $players, $endOfAgeDiscard);
    }

    public function neighbourhood(): Neighbourhood
    {
        return $this->neighboursOf($this->babylonPlayer->id);
    }
}
