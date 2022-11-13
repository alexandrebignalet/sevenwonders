<?php

namespace App\Domain;

use App\Domain\Card\Card;
use App\Domain\Card\CardAction;
use App\Domain\Card\CardType;
use App\Domain\Card\Trade;
use App\Domain\PlayCard\WarResult;
use App\Domain\Resource\Resource;
use App\Domain\Wonder\Neighbourhood;
use App\Domain\Wonder\Wonder;
use App\Domain\Wonder\WonderType;
use JetBrains\PhpStorm\ArrayShape;
use JetBrains\PhpStorm\Pure;

class Player
{
    public readonly int $id;
    public readonly Wonder $wonder;
    public readonly Hand $hand;
    public readonly ?CardAction $selectedAction;
    /**
     * @var WarSymbol[] $warSymbols
     */
    public readonly array $warSymbols;

    /**
     * @param int $userId
     * @param Age $age
     * @param Wonder $wonder
     * @return Player
     */
    #[Pure] static function initialize(int $userId, Age $age, Wonder $wonder): Player
    {
        return new Player($userId, $wonder, Hand::build($age->distributeHand()), null, []);
    }

    /**
     * @param int $id
     * @param Wonder $wonder
     * @param Hand $hand
     * @param CardAction|null $selectionAction
     */
    public function __construct(int $id, Wonder $wonder, Hand $hand, ?CardAction $selectionAction = null, array $warSymbols = [])
    {
        $this->id = $id;
        $this->wonder = $wonder;
        $this->hand = $hand;
        $this->selectedAction = $selectionAction;
        $this->warSymbols = $warSymbols;
    }

    /**
     * @throws GameException
     */
    public function play(Neighbourhood $neighbourhood, string $cardName, string $action, ?string $tradeId): Player
    {
        $cardAction = $this->validate($neighbourhood, $cardName, $action, $tradeId);
        return new Player($this->id, $this->wonder, $this->hand, $cardAction, $this->warSymbols);
    }

    /**
     * @return array{card: ?Card, player: Player}
     */
    #[ArrayShape(['cardAction' => "\App\Domain\Card\CardAction", 'player' => "\App\Domain\Player"])] public function commit(): array
    {
        $hand = $this->hand->take($this->selectedAction);
        $wonder = $this->wonder->build($this->selectedAction);

        $player = new Player($this->id, $wonder, $hand, null, $this->warSymbols);
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
            $this->selectedAction,
            $this->warSymbols
        );
    }

    /**
     * @return array{ player: Player, cardType: CardType | null }
     */
    #[Pure] #[ArrayShape(['player' => "\App\Domain\Player", 'cardType' => "\App\Domain\Card\Card|null"])] public function discardLastCard(): array
    {
        $result = $this->hand->takeLast();
        $player = new Player(
            $this->id,
            $this->wonder,
            $result['hand'],
            $this->selectedAction,
            $this->warSymbols
        );
        return ['player' => $player, 'cardType' => $result['cardType']];
    }

    public function unbury(CardType $cardType)
    {
        return new Player(
            $this->id,
            $this->wonder->build(new CardAction($cardType, Action::BUILD_STRUCTURE)),
            $this->hand,
            null,
            $this->warSymbols
        );
    }

    /**
     * @throws GameException
     */
    public function validate(Neighbourhood $neighbourhood, string $cardName, string $action, ?string $tradeId): CardAction
    {
        $cardActions = $this->availableActions($neighbourhood);
        foreach ($cardActions as $cardAction) {
            if ($cardAction->match($cardName, $action, $tradeId)) {
                return $cardAction;
            }
        }

        throw GameExceptionType::CARD_PLAY_NOT_ALLOWED->exception();
    }

    /**
     * @return CardAction[]
     */
    public function availableActions(Neighbourhood $neighbourhood): array
    {
        $availableActions = array_reduce($this->hand->cardTypes, function ($acc, CardType $cardType) use ($neighbourhood) {
            $availableActions = $this->availableActionsFor($cardType->card(), $neighbourhood);

            foreach ($availableActions as $action) {
                $acc[$action->id()] = $action;
            }

            return $acc;
        }, []);

        return array_values($availableActions);
    }

    /**
     * @param Card $card
     * @param Neighbourhood $neighbourhood
     * @return CardAction[]
     */
    public function availableActionsFor(Card $card, Neighbourhood $neighbourhood): array
    {
        $stageChoices = $this->availableBuildStageActionsFor($card, $neighbourhood);
        $structureChoices = $this->availableBuildStructureActionsFor($card, $neighbourhood);
        $discard = [new CardAction($card->type, Action::DISCARD)];
        return array_merge($stageChoices, $structureChoices, $discard);
    }

    /**
     * @param Card $card
     * @param Neighbourhood $neighbourhood
     * @return CardAction[]
     */
    private function availableBuildStageActionsFor(Card $card, Neighbourhood $neighbourhood): array
    {
        $stageToBuild = $this->wonder->stages->stageToBuild();

        if ($stageToBuild === null) {
            return [];
        }

        return $this->buildCardActions($stageToBuild->getResourceCost(), $neighbourhood, fn(?Trade $trade = null) => CardAction::stage($card, $trade));
    }

    /**
     * @param Card $card
     * @return CardAction[]
     */
    private function availableBuildStructureActionsFor(Card $card, Neighbourhood $neighbourhood): array
    {
        $availableActions = $card->cost->coins > $this->wonder->coins
            ? []
            : $this->buildCardActions($card->cost->resource, $neighbourhood, fn(?Trade $trade = null) => CardAction::structure($card, $trade));

        if ($this->isOlympiaPowerApplicable()) {
            $availableActions = array_values(array_filter($availableActions, fn(CardAction $action) => !($action->cardType() === $card->type && $action->action() === Action::BUILD_STRUCTURE)));
            $availableActions[] = new CardAction($card->type, Action::BUILD_STRUCTURE);
        }

        return $availableActions;
    }

    /**
     * @param Resource $resourceCost
     * @param Neighbourhood $neighbourhood
     * @param callable $cardActionBuilder
     * @return CardAction[]
     */
    private function buildCardActions(Resource $resourceCost, Neighbourhood $neighbourhood, callable $cardActionBuilder): array
    {
        $missingResources = $this->wonder->structures->missingResourcesToPay($resourceCost);

        if ($missingResources->count() === 0) {
            return [$cardActionBuilder()];
        } else {
            $availableTrades = $neighbourhood->availableTrades($missingResources);
            return array_map(fn(Trade $trade): CardAction => $cardActionBuilder($trade), $availableTrades);
        }
    }

    private function isOlympiaPowerApplicable(): bool
    {
        $isOlympia = $this->wonder->type === WonderType::OLYMPIA;
        $isLastPlayableCard = $this->hand->size() === 2;

        return ($isOlympia && $isLastPlayableCard) || $this->wonder->stages->isStagesBuilt(2);
    }

    /**
     * @param Age $age
     * @param WarResult[] $battles
     * @return Player
     */
    public function war(Age $age, array $battles): Player
    {
        $warSymbols = array_reduce($battles, function ($acc, WarResult $result) use ($age) {
            switch ($result) {
                case WarResult::WON:
                case WarResult::LOSE:
                    $acc[] = new WarSymbol($age->id, $result);
            };
            return $acc;
        }, []);

        return new Player(
            $this->id,
            $this->wonder,
            $this->hand,
            $this->selectedAction,
            array_merge($this->warSymbols, $warSymbols)
        );
    }

    public function warPoints(): int
    {
        return array_reduce($this->warSymbols, fn($acc, WarSymbol $ws) => $acc + $ws->points(), 0);
    }
}
