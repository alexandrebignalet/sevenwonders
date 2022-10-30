<?php

namespace App\Tests\Domain\Wonder;

use App\Domain\Wonder\Wonder;
use App\Domain\Wonder\WonderType;
use PHPUnit\Framework\TestCase;

class WonderInitializeTest extends TestCase
{
    private Wonder $wonder;
    private WonderType $wonderType;

    protected function setUp(): void
    {
        $this->wonderType = WonderType::RHODOS;
        $this->wonder = Wonder::initialize($this->wonderType);
    }

    public function test_should_have_a_type() {
        $this->assertEquals($this->wonderType, $this->wonder->type());
    }

    public function test_should_have_no_structures_built() {
        $this->assertEquals([], $this->wonder->structures());
    }

    public function test_should_have_no_stage_built() {
        $this->assertEquals([], $this->wonder->stages());
    }
}
