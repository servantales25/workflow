<?php

namespace LuKun\Workflow\Domain;

abstract class Entity
{
    /** @var IIdentity */
    private $id;

    protected function __construct(IIdentity $id)
    {
        $this->id = $id;
    }

    public function getId(): IIdentity
    {
        return $this->id;
    }

    public function equalsTo(Entity $entity): bool
    {
        $class = get_class($this);

        return ($entity instanceof $class) && $this->id->equalsTo($entity->id);
    }
}
