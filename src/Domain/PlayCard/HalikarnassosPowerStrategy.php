<?php

namespace App\Domain\PlayCard;

use App\Domain\Age;
use App\Domain\Card\CardType;
use App\Domain\GameException;
use App\Domain\GameExceptionType;
use App\Domain\Player;
use App\Domain\Wonder\Neighbourhood;
use JetBrains\PhpStorm\ArrayShape;
use JetBrains\PhpStorm\Pure;

class HalikarnassosPowerStrategy extends Strategy
{
    private Player $haliPlayer;


    /**
     * @param Player $haliPlayer
     * @param Age $age
     * @param Player[] $players
     * @param CardType[] $discard
     */
    #[Pure] public function __construct(Player $haliPlayer, Age $age, array $players, array $discard)
    {
        parent::__construct($age, $players, $discard);
        $this->haliPlayer = $haliPlayer;
    }


    /**
     * @throws GameException
     * @throws \Exception
     */
    public function play(Player $player, string $cardName, string $action, ?string $tradeId): Strategy
    {
        if ($player->id !== $this->haliPlayer->id) {
            throw GameExceptionType::CARD_PLAY_NOT_ALLOWED->exception();
        }

        $result = $this->unburyInDiscard($cardName);

        if ($result['card'] === null) {
            throw GameExceptionType::CARD_PLAY_NOT_ALLOWED->exception();
        }

        $hali = $this->haliPlayer->unbury($result['card']);
        $players = array_map(fn(Player $player) => $player->id === $hali->id ? $hali : $player, $this->players);

        if ($this->isAgeOver($players)) {
            return $this->setupNextAge($players, $result['remaining']);
        } else {
            return $this->setupNextRound($players);
        }
    }

    public function neighbourhood(): Neighbourhood
    {
        return $this->neighboursOf($this->haliPlayer->id);
    }

    /**
     * @param string $cardName
     * @return array{card: CardType, remaining: array<CardType>}
     */
    #[ArrayShape(['card' => "\App\Domain\Card\CardType|null", 'remaining' => "\App\Domain\Card\CardType[]"])]
    private function unburyInDiscard(string $cardName): array
    {
        $remaining = [];
        $pickedCard = null;
        foreach ($this->discard as $card) {
            if ($card->name === $cardName) {
                $pickedCard = $card;
            } else {
                $remaining[] = $card;
            }
        }
        return ['card' => $pickedCard, 'remaining' => $remaining];
    }
}
