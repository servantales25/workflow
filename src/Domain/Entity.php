<?php

namespace LuKun\Workflow\Domain;

abstract class Entity
{
    /** @var IIdentity */
    private $identity;

    protected function __construct(IIdentity $identity)
    {
        $this->identity = $identity;
    }

    /** @return IIdentity */
    public function getId()
    {
        return $this->identity;
    }

    public function equalsTo(Entity $entity): bool
    {
        $class = get_class($this);
        return $entity instanceof $class
            && $this->identity->equalsTo($entity->identity);
    }
}
