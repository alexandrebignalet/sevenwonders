<?php

namespace App\Tests\Domain\Resource;

use App\Domain\Resource\Resource;
use App\Domain\Resource\Resources;
use JetBrains\PhpStorm\Pure;
use PHPUnit\Framework\TestCase;

class ResourcesTest extends TestCase
{
    ## Resource::plus
    public function test_should_add_simple_and_double_correctly()
    {
        $resources = new Resources(new Resource(stone: 1));

        $result = $resources->plus(new Resources(new Resource(stone: 1), new Resource(clay: 1)));

        $this->assertEquals($result, new Resources(new Resource(stone: 2), new Resource(clay: 1, stone: 1)));
    }

    public function test_should_add_double_and_double_correctly()
    {
        $resources = new Resources(new Resource(wood: 2), new Resource(clay: 2));

        $result = $resources->plus(new Resources(new Resource(stone: 1), new Resource(clay: 1)));

        $expectedResult = new Resources(
            new Resource(stone: 1, wood: 2),
            new Resource(clay: 1, wood: 2),
            new Resource(clay: 2, stone: 1),
            new Resource(clay: 3)
        );
        $this->assertEquals($expectedResult, $result);
    }

    public function test_should_add_double_and_simple_correctly()
    {
        $resources = new Resources(new Resource(wood: 1), new Resource(clay: 1));

        $result = $resources->plus(new Resources(new Resource(stone: 1)));

        $expectedResult = new Resources(new Resource(stone: 1, wood: 1), new Resource(clay: 1, stone: 1));
        $this->assertEquals($expectedResult, $result);
    }

    public function test_case()
    {
        $resources = new Resources(new Resource(wood: 1), new Resource(stone: 1));

        $result = $resources->plus(new Resources(new Resource(stone: 1)));

        $expectedResult = new Resources(new Resource(stone: 1, wood: 1), new Resource(stone: 2));
        $this->assertEquals($expectedResult, $result);
    }

    public function test_case_2()
    {
        $resources = new Resources(
            new Resource(wood: 1, papyrus: 1, glass: 1),
            new Resource(stone: 1, papyrus: 1, glass: 1),
        );

        $result = $resources->plus(new Resources(new Resource(stone: 1)));

        $expectedResult = new Resources(
            new Resource(stone: 1, wood: 1, papyrus: 1, glass: 1),
            new Resource(stone: 2, papyrus: 1, glass: 1)
        );
        $this->assertEquals($expectedResult, $result);
    }

    ## Resource::plus

    /**
     * @dataProvider providePlus
     */
    public function test_should_correctly_plus(Resources $owned, Resources $plus, Resources $expectedResult)
    {
        $result = $owned->plus($plus);

        $this->assertEquals($expectedResult, $result);
    }

    #[Pure] function providePlus(): array
    {
        return array(
            array(
                new Resources(),
                Resources::single(stone: 1),
                Resources::single(stone: 1)
            ),
            array(
                Resources::single(stone: 1),
                new Resources(),
                Resources::single(stone: 1)
            ),
        );
    }

    ## Resource::missingToEqual

    /**
     * @dataProvider provideMinus
     */
    public function test_should_correctly_minus(Resources $owned, Resource $targeted, Resources $expectedResult)
    {
        $result = $owned->missingToEqual($targeted);

        $this->assertEquals($expectedResult, $result);
    }

    #[Pure] function provideMinus(): array
    {
        return array(
            array(
                new Resources(),
                new Resource(stone: 1),
                Resources::single(stone: 1)
            ),
            array(
                new Resources(
                    new Resource(wood: 1, papyrus: 1, glass: 1),
                    new Resource(stone: 1, papyrus: 1, glass: 1),
                ),
                new Resource(stone: 1),
                Resources::empty()
            ),
            array(
                new Resources(),
                new Resource(stone: 1),
                Resources::single(stone: 1)
            ),
            array(
                new Resources(
                    new Resource(wood: 1, papyrus: 1, glass: 1),
                    new Resource(stone: 1, papyrus: 1, glass: 1),
                ),
                new Resource(stone: 3, wood: 3, papyrus: 2),
                new Resources(
                    new Resource(stone: 3, wood: 2, papyrus: 1),
                    new Resource(stone: 2, wood: 3, papyrus: 1)
                )
            ),
        );
    }
}
