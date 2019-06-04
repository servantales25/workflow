<?php

namespace LuKun\Workflow\Tests\Transactions;

use PHPUnit\Framework\TestCase;
use LuKun\Workflow\Tests\Transactions\Fakes\FakeTransactionManager;
use stdClass;
use Exception;
use LuKun\Workflow\Commands\Result;
use LuKun\Workflow\Transactions\TransactionalCommandBusGateway;
use LuKun\Workflow\Transactions\AnotherCommandTransactionInProgressException;
use LuKun\Workflow\Transactions\CompleteTransactionCommandMismatchException;

class TransactionCommandBusGatewayTest extends TestCase
{
    /** @var FakeTransactionManager */
    private $transactionManager;
    /** @var TransactionalCommandBusGateway */
    private $gateway;
    /** @var bool */
    private $transactionCommitted = false;
    /** @var bool */
    private $transactionStarted = false;
    /** @var bool */
    private $transactionRolledBack = false;

    protected function setUp(): void
    {
        $this->transactionManager = new FakeTransactionManager();
        $this->gateway = new TransactionalCommandBusGateway($this->transactionManager);

        $this->transactionManager->onBeginTransaction = function () {
            $this->transactionStarted = true;
        };
        $this->transactionManager->onCommitTransaction = function () {
            $this->transactionCommitted = true;
        };
        $this->transactionManager->onRollbackTransaction = function () {
            $this->transactionRolledBack = true;
        };
    }

    public function test_commandReceived()
    {
        $command = new stdClass();

        $this->gateway->commandReceived($command);

        $this->assertTrue($this->transactionStarted);
        $this->assertFalse($this->transactionCommitted);
        $this->assertFalse($this->transactionRolledBack);
    }

    public function test_commandReceived_InvokedWhenAnotherCommandTransactionInProgress()
    {
        $command = new stdClass();

        $this->gateway->commandReceived($command);

        $this->expectException(AnotherCommandTransactionInProgressException::class);
        $this->gateway->commandReceived($command);

        $this->assertTrue($this->transactionStarted);
        $this->assertFalse($this->transactionCommitted);
        $this->assertFalse($this->transactionRolledBack);
    }

    public function test_commandCompleted_SuccessfulResult()
    {
        $command = new stdClass();
        $event = new stdClass();
        $result = Result::createSuccess($event);

        $this->gateway->commandReceived($command);
        $this->gateway->commandCompleted($command, $result);

        $this->assertTrue($this->transactionStarted);
        $this->assertTrue($this->transactionCommitted);
        $this->assertFalse($this->transactionRolledBack);
    }

    public function test_commandCompleted_FailedResult()
    {
        $command = new stdClass();
        $error = new Exception();
        $result = Result::createFailure($error);

        $this->gateway->commandReceived($command);
        $this->gateway->commandCompleted($command, $result);

        $this->assertTrue($this->transactionStarted);
        $this->assertFalse($this->transactionCommitted);
        $this->assertTrue($this->transactionRolledBack);
    }

    public function test_commandCompleted_InvokedWithDifferentCommandThanReceived()
    {
        $command1 = new stdClass();
        $command2 = new stdClass();
        $result = Result::createEmpty();

        $this->gateway->commandReceived($command1);
        $this->expectException(CompleteTransactionCommandMismatchException::class);
        $this->gateway->commandCompleted($command2, $result);

        $this->assertTrue($this->transactionStarted);
        $this->assertFalse($this->transactionCommitted);
        $this->assertFalse($this->transactionRolledBack);
    }

    public function test_commandCompleted_InvokedWithoutReceivedFirst()
    {
        $command = new stdClass();
        $result = Result::createEmpty();

        $this->expectException(CompleteTransactionCommandMismatchException::class);
        $this->gateway->commandCompleted($command, $result);

        $this->assertFalse($this->transactionStarted);
        $this->assertFalse($this->transactionCommitted);
        $this->assertFalse($this->transactionRolledBack);
    }

    public function test_commandReceived_TransparentToArguments()
    {
        $command = new stdClass();

        $returnedCommand = $this->gateway->commandReceived($command);

        $this->assertSame($command, $returnedCommand);
    }

    public function test_commandCompleted_TransparentToArguments()
    {
        $command = new stdClass();
        $result = Result::createEmpty();

        $this->gateway->commandReceived($command);
        $returnedResult = $this->gateway->commandCompleted($command, $result);

        $this->assertSame($result, $returnedResult);
    }
}
