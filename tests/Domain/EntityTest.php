<?php

namespace LuKun\Workflow\Tests\Domain;

use PHPUnit\Framework\TestCase;
use LuKun\Workflow\Tests\Domain\Fakes\FakeEntity;
use LuKun\Workflow\Tests\Domain\Fakes\FakeIdentity;

class EntityTest extends TestCase
{
    public function test_equalsTo_SameIdentity()
    {
        $identity1 = new FakeIdentity(20);
        $identity2 = new FakeIdentity(20);
        $entity1 = new FakeEntity($identity1);
        $entity2 = new FakeEntity($identity2);

        $areEqual = $entity1->equalsTo($entity2);

        $this->assertTrue($areEqual);
    }

    public function test_equalsTo_DifferentIdentity()
    {
        $identity1 = new FakeIdentity(20);
        $identity2 = new FakeIdentity(21);
        $entity1 = new FakeEntity($identity1);
        $entity2 = new FakeEntity($identity2);

        $areEqual = $entity1->equalsTo($entity2);

        $this->assertFalse($areEqual);
    }

    public function test_getId()
    {
        $identity = new FakeIdentity(20);
        $entity = new FakeEntity($identity);

        $returnedIdentity = $entity->getId();

        $this->assertSame($identity, $returnedIdentity);
    }
}
