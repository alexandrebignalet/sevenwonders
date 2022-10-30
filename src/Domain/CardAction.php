<?php

namespace App\Domain;

use App\Domain\Card\Card;

class CardAction
{

    private Card $card;
    private Action $action;

    public static function of(Card $card, string $action) {
        return new CardAction($card, Action::of($action));
    }

    public function __construct(Card $card, Action $action)
    {
        $this->card = $card;
        $this->action = $action;
    }
}
