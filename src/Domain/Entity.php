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
        if ($entity instanceof $class) {
            if (is_scalar($this->id)) {
                return $this->id === $entity->id;
            } else if (method_exists($this->id, 'equalsTo')) {
                return $this->id->equalsTo($entity->id);
            } else {
                return $this->id == $entity->id;
            }
        }

        return false;
    }
}
