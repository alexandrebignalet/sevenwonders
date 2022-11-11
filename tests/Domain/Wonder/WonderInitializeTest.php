<?php

namespace App\Tests\Domain\Wonder;

use App\Domain\Wonder\Wonder;
use App\Domain\Wonder\WonderFace;
use App\Domain\Wonder\WonderType;
use PHPUnit\Framework\TestCase;

class WonderInitializeTest extends TestCase
{
    private Wonder $wonder;
    private WonderType $wonderType;
    private WonderFace $wonderFace;

    protected function setUp(): void
    {
        $this->wonderType = WonderType::RHODOS;
        $this->wonderFace = WonderFace::DAY;
        $this->wonder = Wonder::initialize($this->wonderType, $this->wonderFace);
    }

    public function test_should_have_a_type()
    {
        $this->assertEquals($this->wonderType, $this->wonder->type);
    }

    public function test_should_have_a_face()
    {
        $this->assertEquals($this->wonderFace, $this->wonder->face);
    }

    public function test_should_have_no_structures_built()
    {
        $this->assertEquals(0, $this->wonder->structuresCount());
    }

    public function test_should_have_no_stage_built()
    {
        foreach ($this->wonder->stages->values as $stage) {
            $this->assertFalse($stage->isBuilt());
        }
    }
}
