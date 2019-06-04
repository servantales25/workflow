<?php

namespace LuKun\Workflow\Tests\Domain;

use PHPUnit\Framework\TestCase;
use LuKun\Workflow\Domain\IntId;
use LuKun\Workflow\Tests\Domain\Fakes\FakeEntity;

class EntityTest extends TestCase
{
    public function test_equalsTo_SameIdentity()
    {
        $id1 = new IntId(1);
        $id2 = new IntId(1);
        $entity1 = new FakeEntity($id1);
        $entity2 = new FakeEntity($id2);

        $this->assertTrue($entity1->equalsTo($entity2));
    }

    public function test_equalsTo_DifferentIdentity()
    {
        $id1 = new IntId(1);
        $id2 = new IntId(2);
        $entity1 = new FakeEntity($id1);
        $entity2 = new FakeEntity($id2);

        $this->assertFalse($entity1->equalsTo($entity2));
    }

    public function test_getId()
    {
        $identity = new IntId(1);
        $entity = new FakeEntity($identity);

        $returnedIdentity = $entity->getId();

        $this->assertTrue($identity->equalsTo($returnedIdentity));
    }
}
