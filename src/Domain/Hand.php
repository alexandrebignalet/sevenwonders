<?php

namespace App\Domain;

use App\Domain\Card\Card;
use App\Domain\Card\CardAction;
use App\Domain\Card\CardType;
use App\Domain\Wonder\Wonder;
use JetBrains\PhpStorm\ArrayShape;
use JetBrains\PhpStorm\Pure;

class Hand
{
    /**
     * @var CardType[]
     */
    public readonly array $cardTypes;


    /**
     * @param CardType[] $cardTypes
     */
    public function __construct(array $cardTypes)
    {
        $this->cardTypes = $cardTypes;
    }

    /**
     * @param Wonder $wonder
     * @param Card[] $cards
     * @return Hand
     */
    public static function build(array $cards): Hand
    {
        $cardTypes = array_map(fn(Card $card) => $card->type, $cards);
        return new Hand($cardTypes);
    }

    public function take(CardAction $selectedAction): Hand
    {
        return $this->removeCardPlayed($selectedAction->cardType());
    }

    /**
     * @return Card[]
     */
    public function cards(): array
    {
        return array_map(fn(CardType $type) => $type->card(), $this->cardTypes);
    }

    /**
     * @param CardType $type
     * @return Hand
     */
    private function removeCardPlayed(CardType $type): Hand
    {
        $found = false;
        $filtered = [];
        foreach ($this->cardTypes as $cardType) {
            if ($type === $cardType && $found === false) {
                $found = true;
            } else {
                $filtered[] = $cardType;
            }
        }
        return new Hand($filtered);
    }

    /**
     * @return array{ hand: Hand, card: CardType | null }
     */
    #[Pure] #[ArrayShape(['hand' => "\App\Domain\Hand", 'cardType' => "\App\Domain\Card\CardType|null"])] public function takeLast(): array
    {
        $cardType = $this->size() > 0 ? $this->cardTypes[0] : null;
        return ['hand' => new Hand([]), 'cardType' => $cardType];
    }

    /**
     * @return int
     */
    public function size(): int
    {
        return count($this->cardTypes);
    }
}
