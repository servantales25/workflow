<?php

namespace LuKun\Workflow\Tests\Domain;

use PHPUnit\Framework\TestCase;
use LuKun\Workflow\Tests\Domain\Fakes\FakeEntity;

class EntityTest extends TestCase
{
    public function test_equalsTo_SameIdentity()
    {
        $id1 = 1;
        $id2 = 1;
        $entity1 = new FakeEntity($id1);
        $entity2 = new FakeEntity($id2);

        $areEqual = $entity1->equalsTo($entity2);

        $this->assertTrue($areEqual);
    }

    public function test_equalsTo_DifferentIdentity()
    {
        $id1 = 1;
        $id2 = 2;
        $entity1 = new FakeEntity($id1);
        $entity2 = new FakeEntity($id2);

        $areEqual = $entity1->equalsTo($entity2);

        $this->assertFalse($areEqual);
    }

    public function test_getId()
    {
        $identity = 1;
        $entity = new FakeEntity($identity);

        $returnedIdentity = $entity->getId();

        $this->assertSame($identity, $returnedIdentity);
    }
}
