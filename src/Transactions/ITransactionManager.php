<?php

namespace LuKun\Workflow\Transactions;

interface ITransactionManager
{
    function beginTransaction(): void;
    function commitTransaction(): void;
    function rollbackTransaction(): void;
}
