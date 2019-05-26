<?php

namespace LuKun\Workflow\Domain;

abstract class Entity
{
    /** @var mixed */
    private $id;

    protected function __construct($id)
    {
        $this->id = $id;
    }

    /** @return mixed */
    public function getId()
    {
        return $this->id;
    }

    public function equalsTo(Entity $entity): bool
    {
        $class = get_class($this);
        return $entity instanceof $class
            && $this->id == $entity->id;
    }
}
