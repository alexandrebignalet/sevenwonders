<?php

namespace App\Domain\Wonder;

use App\Domain\Card\Offer;
use App\Domain\Card\Trade;
use App\Domain\Resource\Resource;
use App\Domain\Resource\Resources;
use JetBrains\PhpStorm\Pure;

class Neighbourhood
{
    public readonly Wonder $left;
    public readonly Wonder $right;

    public function __construct(Wonder $left, Wonder $right)
    {
        $this->left = $left;
        $this->right = $right;
    }

    /**
     * @param Resources $missingResources
     * @return Trade[]
     */
    public function availableTrades(Resources $missingResources): array
    {
        return array_reduce($missingResources->resources(), function (array $acc, Resource $resource): array {
            $result = $this->availableTradesFor($resource);
            return array_merge($acc, $this->filterDuplicates($acc, $result));
        }, []);
    }

    /**
     * @param Trade[] $trades
     * @param Trade[] $needles
     * @return Trade[]
     */
    private function filterDuplicates(array $trades, array $needles): array
    {
        $tradeIds = [];
        $filtered = [];

        $tradeIds = array_merge($tradeIds, array_map(fn(Trade $trade) => $trade->id(), $trades));
        $tradeIds = array_merge($tradeIds, array_map(fn(Trade $trade) => $trade->id(), $needles));
        $tradeIds = array_unique($tradeIds);

        foreach ($needles as $needle) {
            if (in_array($needle->id(), $tradeIds)) {
                $filtered[] = $needle;
                $tradeIds = array_filter($tradeIds, fn($id) => $id !== $needle->id());
            }
        }
        return $filtered;
    }

    /**
     * @param Resource $neededResource
     * @return Trade[]
     */
    #[Pure] private function availableTradesFor(Resource $neededResource): array
    {
        $trades = [];
        $leftResources = $this->left->resources();
        $rightResources = $this->right->resources();

        /** @var Resource $leftResource */
        foreach ($leftResources->resources() as $leftResource) {
            /** @var Resource $rightResource */
            foreach ($rightResources->resources() as $rightResource) {
                $trades = array_merge($trades, $this->generateTrades($neededResource, $leftResource, $rightResource));
            }

            if ($rightResources->count() === 0) {
                $trades = array_merge($trades, $this->generateTrades($neededResource, $leftResource, new Resource()));
            }
        }

        if ($leftResources->count() === 0) {
            foreach ($rightResources->resources() as $rightResource) {
                $trades = array_merge($trades, $this->generateTrades($neededResource, new Resource(), $rightResource));
            }
        }

        return $trades;
    }

    /**
     * @param Resource $neededResource
     * @param Resource $leftResource
     * @param Resource $rightResource
     * @return Trade[]
     */
    private function generateTrades(Resource $neededResource, Resource $leftResource, Resource $rightResource): array
    {
        $trades = [];
        $availableShares = $neededResource->availableShares($leftResource, $rightResource);

        foreach ($availableShares as $share) {
            $trades[] = new Trade(
                new Offer($this->left, $share->getLeft()),
                new Offer($this->right, $share->getRight())
            );
        }
        return $trades;
    }
}
