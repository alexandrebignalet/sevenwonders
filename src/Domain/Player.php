<?php

namespace App\Domain;

use App\Domain\Card\Card;
use App\Domain\Card\CardAction;
use App\Domain\Card\CardType;
use App\Domain\Wonder\Neighbourhood;
use App\Domain\Wonder\Wonder;
use JetBrains\PhpStorm\ArrayShape;
use JetBrains\PhpStorm\Pure;

class Player
{
    public readonly int $id;
    public readonly Wonder $wonder;
    public readonly Hand $hand;
    public readonly ?CardAction $selectedAction;

    /**
     * @param int $userId
     * @param Age $age
     * @param Wonder $wonder
     * @return Player
     */
    #[Pure] static function initialize(int $userId, Age $age, Wonder $wonder): Player
    {
        return new Player($userId, $wonder, Hand::build($age->distributeHand()), null);
    }

    /**
     * @param int $id
     * @param Wonder $wonder
     * @param Hand $hand
     * @param CardAction|null $selectionAction
     */
    private function __construct(int $id, Wonder $wonder, Hand $hand, ?CardAction $selectionAction)
    {
        $this->id = $id;
        $this->wonder = $wonder;
        $this->hand = $hand;
        $this->selectedAction = $selectionAction;
    }

    /**
     * @throws GameException
     */
    public function play(Neighbourhood $neighbourhood, string $cardName, string $action, ?string $tradeId): Player
    {
        $cardAction = $this->hand->validate($this->wonder, $neighbourhood, $cardName, $action, $tradeId);
        return new Player($this->id, $this->wonder, $this->hand, $cardAction);
    }

    /**
     * @return array{card: ?Card, player: Player}
     */
    #[ArrayShape(['cardAction' => "\App\Domain\Card\CardAction", 'player' => "\App\Domain\Player"])] public function commit(): array
    {
        $hand = $this->hand->take($this->selectedAction);
        $wonder = $this->wonder->build($this->selectedAction);

        $player = new Player($this->id, $wonder, $hand, null);
        return ['cardAction' => $this->selectedAction, 'player' => $player];
    }

    /**
     * @param Card[] $cards
     * @return Player
     */
    public function receives(array $cards): Player
    {
        return new Player(
            $this->id,
            $this->wonder,
            Hand::build($cards),
            $this->selectedAction
        );
    }

    /**
     * @return array{ player: Player, card: CardType }
     */
    public function discardLastCard(): array
    {
        $result = $this->hand->takeLast();
        $player = new Player(
            $this->id,
            $this->wonder,
            $result['hand'],
            $this->selectedAction
        );
        return ['player' => $player, 'card' => $result['card']];
    }

    public function unbury(CardType $cardType)
    {
        return new Player(
            $this->id,
            $this->wonder->build(new CardAction($cardType, Action::BUILD_STRUCTURE)),
            $this->hand,
            null
        );
    }
}
