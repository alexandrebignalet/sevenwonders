<?php

namespace App\Tests\Domain\Resource;

use App\Domain\Resource\Resource;
use App\Domain\Resource\Share;
use PHPUnit\Framework\TestCase;

class ResourceTest extends TestCase
{

    public function test_should_resolves_empty_when_sharing_a_resource_is_missing()
    {
        $neededResource = new Resource(clay: 2, stone: 1, glass: 1);
        $left = new Resource(clay: 1, stone: 1);
        $right = new Resource(clay: 1, stone: 1);

        $shares = $neededResource->availableShares($left, $right);

        $this->assertCount(0, $shares);
    }

    /**
     * @dataProvider providerAvailableSharesData
     */
    public function test_should_resolves_all_possible_shares(Resource $neededResource, Resource $left, Resource $right, Share ...$expectedShares)
    {
        $shares = $neededResource->availableShares($left, $right);

        for ($i = 0; $i < count($expectedShares); $i++) {
            $this->assertObjectEquals($shares[$i], $expectedShares[$i]);
        }
        $this->assertEquals(count($expectedShares), count($shares));
    }

    public function providerAvailableSharesData()
    {
        return array(
            array(
                new Resource(clay: 2, stone: 1),
                new Resource(clay: 1, stone: 1),
                new Resource(clay: 1, stone: 1),
                new Share(new Resource(clay: 1), new Resource(clay: 1, stone: 1)),
                new Share(new Resource(clay: 1, stone: 1), new Resource(clay: 1))
            ),
            array(
                new Resource(ore: 1),
                new Resource(ore: 3, wood: 2, cloth: 1),
                new Resource(),
                new Share(new Resource(ore: 1), new Resource()),
            ),
            array(
                new Resource(clay: 2, stone: 1, ore: 3, glass: 1),
                new Resource(clay: 1, stone: 1, ore: 2, glass: 1),
                new Resource(clay: 1, stone: 1, ore: 1),
                new Share(new Resource(clay: 1, ore: 2, glass: 1), new Resource(clay: 1, stone: 1, ore: 1)),
                new Share(new Resource(clay: 1, stone: 1, ore: 2, glass: 1), new Resource(clay: 1, ore: 1))
            ),
            array(
                new Resource(clay: 2, stone: 1, ore: 2, glass: 1),
                new Resource(clay: 1, stone: 1, ore: 2, glass: 1),
                new Resource(clay: 1, stone: 1, ore: 1, glass: 1),
                new Share(new Resource(clay: 1, ore: 1), new Resource(clay: 1, stone: 1, ore: 1, glass: 1)),
                new Share(new Resource(clay: 1, ore: 1, glass: 1), new Resource(clay: 1, stone: 1, ore: 1)),
                new Share(new Resource(clay: 1, ore: 2), new Resource(clay: 1, stone: 1, glass: 1)),
                new Share(new Resource(clay: 1, ore: 2, glass: 1), new Resource(clay: 1, stone: 1)),
                new Share(new Resource(clay: 1, stone: 1, ore: 1), new Resource(clay: 1, ore: 1, glass: 1)),
                new Share(new Resource(clay: 1, stone: 1, ore: 1, glass: 1), new Resource(clay: 1, ore: 1)),
                new Share(new Resource(clay: 1, stone: 1, ore: 2), new Resource(clay: 1, glass: 1)),
                new Share(new Resource(clay: 1, stone: 1, ore: 2, glass: 1), new Resource(clay: 1)),
            ),
        );
    }
}
