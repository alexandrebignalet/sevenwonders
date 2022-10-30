<?php

namespace App\Domain;

use App\Domain\Card\Card;

class Hand
{
    /**
     * @var Card[]
     */
    private array $cards;


    /**
     * @param Card[] $cards
     */
    public function __construct(array $cards)
    {
        $this->cards = $cards;
    }

    public function findCard(string $cardName)
    {
        foreach ($this->cards as $card) {
            if ($card->type()->value === $cardName) {
                return $card;
            }
        }
        throw GameExceptionType::CARD_PLAY_NOT_ALLOWED->exception();
    }

    public function size()
    {
        return count($this->cards);
    }
}
