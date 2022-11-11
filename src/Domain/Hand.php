<?php

namespace App\Domain;

use App\Domain\Card\Card;
use App\Domain\Card\CardAction;
use App\Domain\Card\CardType;
use App\Domain\Wonder\Neighbourhood;
use App\Domain\Wonder\Wonder;

class Hand
{
    /**
     * @var CardType[]
     */
    private array $cards;


    /**
     * @param CardType[] $cardTypes
     */
    public function __construct(array $cardTypes)
    {
        $this->cards = $cardTypes;
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

    /**
     * @throws GameException
     */
    public function validate(Wonder $wonder, Neighbourhood $neighbourhood, string $cardName, string $action, ?string $tradeId): CardAction
    {
        $cardActions = $this->availableActions($wonder, $neighbourhood);
        foreach ($cardActions as $cardAction) {
            if ($cardAction->match($cardName, $action, $tradeId)) {
                return $cardAction;
            }
        }

        throw GameExceptionType::CARD_PLAY_NOT_ALLOWED->exception();
    }

    /**
     * TODO move to player
     * @return CardAction[]
     */
    public function availableActions(Wonder $wonder, Neighbourhood $neighbourhood): array
    {
        return array_reduce($this->cards, function (array $acc, CardType $cardType) use ($neighbourhood, $wonder): array {
            $availableActions = $wonder->availableActionsFor($cardType->card(), $neighbourhood);

            foreach ($availableActions as $action) {
                $acc[] = $action;
            }

            return $acc;
        }, []);
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
        return array_map(fn(CardType $type) => $type->card(), $this->cards);
    }

    /**
     * @param CardType $type
     * @return Hand
     */
    private function removeCardPlayed(CardType $type): Hand
    {
        $found = false;
        $filtered = [];
        foreach ($this->cards as $cardType) {
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
    public function takeLast(): array
    {
        $card = array_pop($this->cards);
        return ['hand' => new Hand($this->cards), 'card' => $card];
    }

    /**
     * @return int
     */
    public function size(): int
    {
        return count($this->cards);
    }
}
