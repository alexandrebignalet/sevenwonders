<?php

namespace App\Tests\Domain\Card;

use App\Domain\Card\Offer;
use App\Domain\Card\Trade;
use App\Domain\Resource\Resource;
use App\Domain\Wonder\WonderFace;
use App\Domain\Wonder\WonderType;
use PHPUnit\Framework\TestCase;

class TradeTest extends TestCase
{

    /**
     * @dataProvider provideTrades
     */
    public function test_should_generate_correctly(Offer $left, Offer $right, string $expected)
    {
        $trade = new Trade($left, $right);

        $id = $trade->id();

        $this->assertEquals($expected, $id);
    }

    public function provideTrades(): array
    {
        return array(
            array(
                new Offer(WonderType::BABYLON->wonder(WonderFace::DAY), new Resource(clay: 1)),
                new Offer(WonderType::BABYLON->wonder(WonderFace::DAY), new Resource()),
                "CLAY_1/"
            ),
            array(
                new Offer(WonderType::BABYLON->wonder(WonderFace::DAY), new Resource(clay: 1, stone: 1)),
                new Offer(WonderType::BABYLON->wonder(WonderFace::DAY), new Resource(clay: 1)),
                "CLAY_1STONE_1/CLAY_1"
            ),
            array(
                new Offer(WonderType::BABYLON->wonder(WonderFace::DAY), new Resource(clay: 3, stone: 2)),
                new Offer(WonderType::BABYLON->wonder(WonderFace::DAY), new Resource(clay: 1, glass: 4, cloth: 3)),
                "CLAY_3STONE_2/CLAY_1GLASS_4CLOTH_3"
            ),
        );
    }
}
