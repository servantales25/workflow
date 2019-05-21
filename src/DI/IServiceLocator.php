<?php

namespace LuKun\Workflow\DI;

interface IServiceLocator
{
    function findByType(string $className): ?object;
}
