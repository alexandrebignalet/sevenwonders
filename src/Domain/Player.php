<?php

namespace App\Domain;

use App\Domain\Card\Card;
use App\Domain\Wonder\Wonder;
use App\Domain\Wonder\WonderType;
use JetBrains\PhpStorm\Pure;

class Player {


    private int $id;
    private int $coins;
    private Wonder $wonder;
    private Hand $hand;
    private ?string $selectionAction;
    /**
     * @var Player[] $neighbours
     */
    private array $neighbours;

    /**
     * @param int $userId
     * @param Age $age
     * @param Player[] $neighbours
     * @return Player
     */
    #[Pure] static function initialize(int $userId, Age $age, Wonder $wonder): Player {
        return new Player($userId, 3, $wonder, $age->distributeHand(), null);
    }

    /**
     * @param int $id
     * @param int $coins
     * @param Wonder $wonder
     * @param Card[] $hand
     * @param string|null $selectionAction
     * @param Player[] $neighbours
     */
    private function __construct(int $id, int $coins, Wonder $wonder, Hand $hand, ?string $selectionAction)
    {
        $this->id = $id;
        $this->coins = $coins;
        $this->wonder = $wonder;
        $this->hand = $hand;
        $this->selectionAction = $selectionAction;
    }

    /**
     * @throws GameException
     */
    public function play(string $cardName, string $action)
    {
        $card = $this->hand->findCard($cardName);
        $cardAction = CardAction::of($card, $action);
    }

    public function id(): int
    {
        return $this->id;
    }

    public function coins(): int
    {
        return $this->coins;
    }

    public function wonder(): Wonder
    {
        return $this->wonder;
    }

    /**
     * @return Hand
     */
    public function hand(): Hand
    {
        return $this->hand;
    }

    public function selectedAction(): ?string
    {
        return $this->selectionAction;
    }

    /**
     * @return Player[]
     */
    public function neighbours(): array
    {
        return $this->neighbours;
    }

    /**
     * @param Player[] $neighbours
     * @return void
     */
    public function setNeighbours(array $neighbours)
    {
        $this->neighbours = $neighbours;
    }

    public function structures(): array
    {
        return $this->wonder->structures();
    }
}
