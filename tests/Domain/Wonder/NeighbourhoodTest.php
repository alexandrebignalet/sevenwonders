<?php

namespace App\Tests\Domain\Wonder;

use App\Domain\Card\Offer;
use App\Domain\Resource\Resource;
use App\Domain\Resource\Resources;
use App\Domain\Wonder\Neighbourhood;
use App\Domain\Wonder\Structures;
use App\Domain\Wonder\Wonder;
use App\Domain\Wonder\WonderFace;
use App\Domain\Wonder\WonderType;
use JetBrains\PhpStorm\Pure;
use PHPUnit\Framework\TestCase;

class NeighbourhoodTest extends TestCase
{

    public function test_no_trades_when_nothing_available()
    {
        $missingResources = new Resources(new Resource(clay: 1));
        $neighbourhood = $this->createNeighbourhood(
            new Resources(),
            new Resources()
        );
        $trades = $neighbourhood->availableTrades($missingResources);
        $this->assertCount(0, $trades);
    }

    public function test_no_trades_when_no_matching_resource_available()
    {
        $missingResources = new Resources(new Resource(clay: 1));
        $neighbourhood = $this->createNeighbourhood(
            Resources::single(glass: 1, cloth: 1),
            Resources::single(stone: 1)
        );
        $trades = $neighbourhood->availableTrades($missingResources);
        $this->assertCount(0, $trades);
    }

    public function test_single_to_single_with_one_neighbour_trade()
    {
        $missingResources = new Resources(new Resource(clay: 1));
        $neighbourhood = $this->createNeighbourhood(
            new Resources(new Resource(clay: 1)),
            new Resources(new Resource(ore: 1))
        );
        $trades = $neighbourhood->availableTrades($missingResources);
        $firstTrades = $trades[0]->getLeftNeighbourOffer();
        $this->assertCount(1, $trades);
        $this->assertEquals($firstTrades, new Offer($neighbourhood->left, new Resource(clay: 1)));
    }

    public function test_single_to_single_with_two_neighbours_trade()
    {
        $missingResources = new Resources(new Resource(clay: 1, ore: 1));
        $neighbourhood = $this->createNeighbourhood(
            new Resources(new Resource(clay: 1)),
            new Resources(new Resource(ore: 1))
        );
        $trades = $neighbourhood->availableTrades($missingResources);
        $firstTrades = $trades[0]->getLeftNeighbourOffer();
        $secondTrades = $trades[0]->getRightNeighbourOffer();
        $this->assertCount(1, $trades);
        $this->assertEquals($firstTrades, new Offer($neighbourhood->left, new Resource(clay: 1)));
        $this->assertEquals($secondTrades, new Offer($neighbourhood->right, new Resource(ore: 1)));
    }

    public function test_multiple_to_single_with_two_neighbours_trade()
    {
        $missingResources = new Resources(
            new Resource(ore: 1),
            new Resource(wood: 1),
        );
        $neighbourhood = $this->createNeighbourhood(
            new Resources(new Resource(wood: 1, glass: 3)),
            new Resources(new Resource(stone: 2, ore: 1))
        );
        $trades = $neighbourhood->availableTrades($missingResources);
        $firstTradeLeftNeighbourOffer = $trades[0]->getLeftNeighbourOffer();
        $firstTradeRightNeighbourOffer = $trades[0]->getRightNeighbourOffer();
        $secondTradeLeftNeighbourOffer = $trades[1]->getLeftNeighbourOffer();
        $secondTradeRightNeighbourOffer = $trades[1]->getRightNeighbourOffer();
        $thirdTradeLeftNeighbourOffer = $trades[2]->getLeftNeighbourOffer();
        $thirdTradeRightNeighbourOffer = $trades[2]->getRightNeighbourOffer();
        $this->assertCount(3, $trades);
        $this->assertEquals($firstTradeLeftNeighbourOffer, new Offer($neighbourhood->left, new Resource()));
        $this->assertEquals($firstTradeRightNeighbourOffer, new Offer($neighbourhood->right, new Resource(ore: 1)));
        $this->assertEquals($secondTradeLeftNeighbourOffer, new Offer($neighbourhood->left, new Resource()));
        $this->assertEquals($secondTradeRightNeighbourOffer, new Offer($neighbourhood->right, new Resource(wood: 1)));
        $this->assertEquals($thirdTradeLeftNeighbourOffer, new Offer($neighbourhood->left, new Resource(wood: 1)));
        $this->assertEquals($thirdTradeRightNeighbourOffer, new Offer($neighbourhood->right, new Resource()));
    }

    public function test_multiple_to_single_with_one_neighbours_trade()
    {
        $missingResources = new Resources(
            new Resource(ore: 1),
            new Resource(wood: 1),
        );
        $neighbourhood = $this->createNeighbourhood(
            new Resources(new Resource(ore: 2, wood: 3, cloth: 1)),
            new Resources()
        );
        $trades = $neighbourhood->availableTrades($missingResources);
        $firstTrades = $trades[0]->getLeftNeighbourOffer();
        $secondTrades = $trades[1]->getRightNeighbourOffer();
        $thirdTrades = $trades[2]->getLeftNeighbourOffer();
        $this->assertCount(3, $trades);
        $this->assertEquals($firstTrades, new Offer($neighbourhood->left, new Resource(ore: 1)));
        $this->assertEquals($secondTrades, new Offer($neighbourhood->right, new Resource(wood: 1)));
        $this->assertEquals($thirdTrades, new Offer($neighbourhood->left, new Resource(wood: 1)));
    }

    public function test_multiple_to_multiple_with_one_neighbour_trade()
    {
        $missingResources = new Resources(
            new Resource(ore: 1),
            new Resource(wood: 1),
        );
        $neighbourhood = $this->createNeighbourhood(
            new Resources(new Resource(ore: 1), new Resource(wood: 1)),
            new Resources()
        );
        $trades = $neighbourhood->availableTrades($missingResources);
        $this->assertCount(3, $trades);

        $leftNeighbourOffer = $trades[0]->getLeftNeighbourOffer();
        $this->assertEquals($leftNeighbourOffer, new Offer($neighbourhood->left, new Resource(ore: 1)));

        $secondRightNeighbourOffer = $trades[1]->getRightNeighbourOffer();
        $this->assertEquals($secondRightNeighbourOffer, new Offer($neighbourhood->right, new Resource(wood: 1)));

        $thirdLeftNeighbourOffer = $trades[2]->getLeftNeighbourOffer();
        $this->assertEquals($thirdLeftNeighbourOffer, new Offer($neighbourhood->left, new Resource(wood: 1)));
    }

    public function test_multiple_to_multiple_with_two_neighbours_trade()
    {
        $missingResources = new Resources(
            new Resource(ore: 2),
            new Resource(ore: 1, wood: 2),
        );
        $neighbourhood = $this->createNeighbourhood(
            new Resources(new Resource(ore: 1), new Resource(wood: 1)),
            new Resources(new Resource(ore: 1), new Resource(wood: 2))
        );
        $trades = $neighbourhood->availableTrades($missingResources);
        $this->assertCount(3, $trades);

        $first = $trades[0]->getLeftNeighbourOffer();
        $second = $trades[0]->getRightNeighbourOffer();
        $this->assertEquals($first, new Offer($neighbourhood->left, new Resource(ore: 1)));
        $this->assertEquals($second, new Offer($neighbourhood->right, new Resource(ore: 1)));

        $first = $trades[1]->getLeftNeighbourOffer();
        $second = $trades[1]->getRightNeighbourOffer();
        $this->assertEquals($first, new Offer($neighbourhood->left, new Resource(ore: 1)));
        $this->assertEquals($second, new Offer($neighbourhood->right, new Resource(wood: 2)));

        $first = $trades[2]->getLeftNeighbourOffer();
        $second = $trades[2]->getRightNeighbourOffer();
        $this->assertEquals($first, new Offer($neighbourhood->left, new Resource(wood: 1)));
        $this->assertEquals($second, new Offer($neighbourhood->right, new Resource(ore: 1, wood: 1)));
    }

    /**
     * @return Neighbourhood
     */
    #[Pure] public function createNeighbourhood(
        Resources $leftResources,
        Resources $rightResources
    ): Neighbourhood
    {
        $left = new Wonder(
            WonderType::ALEXANDRIA,
            WonderFace::DAY,
            WonderType::ALEXANDRIA->resource(),
            3,
            new Structures($leftResources),
            WonderType::ALEXANDRIA->stages(WonderFace::DAY),
        );
        $right = new Wonder(
            WonderType::BABYLON,
            WonderFace::NIGHT,
            WonderType::BABYLON->resource(),
            3,
            new Structures($rightResources),
            WonderType::BABYLON->stages(WonderFace::NIGHT)
        );
        return new Neighbourhood($left, $right);
    }
}
