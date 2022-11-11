<?php

namespace App\Domain\Wonder;

use App\Domain\Action;
use App\Domain\Card\Card;
use App\Domain\Card\CardAction;
use App\Domain\Card\Trade;
use App\Domain\Resource\Resource;
use App\Domain\Resource\Resources;
use JetBrains\PhpStorm\Pure;

class Wonder
{
    public readonly WonderType $type;
    public readonly WonderFace $face;
    public readonly int $coins;
    public readonly Resource $resource;
    public readonly Structures $structures;
    public readonly Stages $stages;


    #[Pure] public static function initialize(WonderType $type, WonderFace $face): Wonder
    {
        return new Wonder($type, $face, $type->resource(), 3, Structures::initialize(), $type->stages($face));
    }

    /**
     * @param WonderType $type
     */
    public function __construct(WonderType $type, WonderFace $face, Resource $resource, int $coins, Structures $structures, Stages $stages)
    {
        $this->coins = $coins;
        $this->type = $type;
        $this->structures = $structures;
        $this->stages = $stages;
        $this->face = $face;
        $this->resource = $resource;
    }

    public function build(CardAction $selectedAction): Wonder
    {
        switch ($selectedAction->action()) {
            case Action::BUILD_STRUCTURE:
                $structures = $this->structures->add($selectedAction->cardType());
                return new Wonder($this->type, $this->face, $this->resource, $this->coins, $structures, $this->stages);
            case Action::BUILD_STAGE:
                $stages = $this->stages->build();
                return new Wonder($this->type, $this->face, $this->resource, $this->coins, $this->structures, $stages);
            case Action::DISCARD:
                return new Wonder($this->type, $this->face, $this->resource, $this->coins + 3, $this->structures, $this->stages);
        }
    }

    public function availableActionsFor(Card $card, Neighbourhood $neighbourhood): array
    {
        $stageChoices = $this->availableBuildStageActionsFor($card, $neighbourhood);
        $structureChoices = $this->availableBuildStructureActionsFor($card, $neighbourhood);
        $discard = [new CardAction($card->type, Action::DISCARD)];
        return array_merge($stageChoices, $structureChoices, $discard);
    }

    /**
     * @param Card $card
     * @return CardAction[]
     */
    private function availableBuildStageActionsFor(Card $card, Neighbourhood $neighbourhood): array
    {
        $stageToBuild = $this->stages->stageToBuild();

        if ($stageToBuild === null) {
            return [];
        }

        return $this->buildCardActions($card, $stageToBuild->getResourceCost(), $neighbourhood, fn(Card $card, ?Trade $trade = null) => CardAction::stage($card, $trade));
    }

    /**
     * @param Card $card
     * @return CardAction[]
     */
    private function availableBuildStructureActionsFor(Card $card, Neighbourhood $neighbourhood): array
    {
        if ($card->cost->coins > $this->coins) {
            return [];
        }

        return $this->buildCardActions($card, $card->cost->resource, $neighbourhood, fn(Card $card, ?Trade $trade = null) => CardAction::structure($card, $trade));
    }

    /**
     * @param Card $card
     * @param callable $cardActionBuilder
     * @return CardAction[]
     */
    private function buildCardActions(Card $card, Resource $resourceCost, Neighbourhood $neighbourhood, callable $cardActionBuilder): array
    {
        $missingResources = $this->structures->missingResourcesToPay($resourceCost);

        if ($missingResources->count() === 0) {
            return [$cardActionBuilder($card)];
        } else {
            $availableTrades = $neighbourhood->availableTrades($missingResources);
            return array_map(fn(Trade $trade): CardAction => $cardActionBuilder($card, $trade), $availableTrades);
        }
    }

    public function resources(): Resources
    {
        return (new Resources($this->resource))->plus($this->structures->resources());
    }

    #[Pure] public function structuresCount(): int
    {
        return $this->structures->count();
    }

    #[Pure] public function hasPowerToPlay(WonderPowerType $powerType): bool
    {
        $power = $this->stages->powerRequiresAction();
        return $power === $powerType;
    }
}
