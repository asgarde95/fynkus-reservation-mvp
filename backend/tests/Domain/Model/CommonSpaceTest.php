<?php

namespace App\Tests\Domain\Model;

use App\Domain\Model\CommonSpace;
use PHPUnit\Framework\TestCase;

class CommonSpaceTest extends TestCase
{
    public function testCanCreateWithNameOnly()
    {
        $space = new CommonSpace('Padel Court');
        $this->assertSame('Padel Court', $space->getName());
        $this->assertNull($space->getDescription());
    }

    public function testCanCreateWithNameAndDescription()
    {
        $space = new CommonSpace('Swimming Pool', 'Outdoor heated pool');
        $this->assertSame('Swimming Pool', $space->getName());
        $this->assertSame('Outdoor heated pool', $space->getDescription());
    }

    public function testEmptyNameIsAllowed()
    {
        $space = new CommonSpace('');
        $this->assertSame('', $space->getName());
        $this->assertNull($space->getDescription());
    }

    public function testDescriptionCanBeNull()
    {
        $space = new CommonSpace('Gym', null);
        $this->assertSame('Gym', $space->getName());
        $this->assertNull($space->getDescription());
    }
}
