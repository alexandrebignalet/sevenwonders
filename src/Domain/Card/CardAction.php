<?php

namespace App\Domain\Card;

use App\Domain\Action;
use JetBrains\PhpStorm\Pure;

class CardAction
{

    private CardType $cardType;
    private Action $action;
    private ?Trade $trade;

    public function __construct(CardType $cardType, Action $action, ?Trade $trade = null)
    {
        $this->cardType = $cardType;
        $this->action = $action;
        $this->trade = $trade;
    }

    #[Pure] public static function structure(Card $card, ?Trade $trade = null): CardAction
    {
        return new CardAction($card->type, Action::BUILD_STRUCTURE, $trade);
    }

    #[Pure] public static function stage(Card $card, ?Trade $trade = null): CardAction
    {
        return new CardAction($card->type, Action::BUILD_STAGE, $trade);
    }

    public function cardType(): CardType
    {
        return $this->cardType;
    }

    public function action(): Action
    {
        return $this->action;
    }

    /**
     * @return Trade|null
     */
    public function trade(): ?Trade
    {
        return $this->trade;
    }


    #[Pure] public function equals(CardAction $other)
    {
        return $other->cardType() === $this->cardType
            && $other->action === $this->action
            && $other->trade === $this->trade;
    }

    public function match(string $cardName, string $action, ?string $tradeId): bool
    {
        $type = CardType::from($cardName);
        $action = Action::from($action);
        return $this->cardType === $type && $this->action === $action && $this->trade?->id() === $tradeId;
    }

    public function toString(): string
    {
        $trade = $this->trade === null ? 'null' : $this->trade->toString();
        return "({$this->cardType->name};{$this->action->name};{$trade})";
    }

    public function id(): string
    {
        return "{$this->cardType->name}_{$this->action->name}_{$this->trade?->id()}";
    }
}
