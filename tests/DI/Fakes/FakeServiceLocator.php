<?php

namespace LuKun\Workflow\Tests\DI\Fakes;

use LuKun\Workflow\DI\IServiceLocator;

class FakeServiceLocator implements IServiceLocator
{
    /** @var callable - (string $className): ?object */
    public $onFindByType;

    public function __construct()
    {
        $this->onFindByType = function ($className) { };
    }

    public function findByType(string $className): ?object
    {
        return call_user_func($this->onFindByType, $className);
    }
}
