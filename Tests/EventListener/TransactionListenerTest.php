<?php

namespace Padam87\AccountBundle\Tests\EventListener;

use Doctrine\ORM\EntityManager;
use Doctrine\ORM\Event\OnFlushEventArgs;
use Doctrine\ORM\Mapping\ClassMetadata;
use Doctrine\ORM\UnitOfWork;
use Mockery as m;
use Money\Currency;
use Money\Money;
use Padam87\AccountBundle\Entity\UserInterface;
use Padam87\AccountBundle\EventListener\TransactionListener;
use Padam87\AccountBundle\Tests\Resources\Entity\Account;
use Padam87\AccountBundle\Tests\Resources\Entity\Transaction;
use Padam87\AccountBundle\Tests\Resources\Entity\User;

class TransactionListenerTest extends \PHPUnit_Framework_TestCase
{
    public function tearDown()
    {
        parent::tearDown();

        m::close();
    }

    private function getBaseMocks(Account $account, $transactions = [])
    {
        $uow = m::mock(UnitOfWork::class);
        $uow->shouldReceive('getScheduledEntityInsertions')->once()->andReturn($transactions);
        $uow->shouldReceive('getScheduledEntityUpdates')->once()->andReturn([]);
        $uow->shouldReceive('getScheduledEntityDeletions')->once()->andReturn([]);
        $uow->shouldReceive('persist')->times(count($transactions))->with($account);
        $uow->shouldReceive('computeChangeSet')->times(count($transactions));

        $em = m::mock(EntityManager::class);
        $em->shouldReceive('getUnitOfWork')->once()->andReturn($uow);
        $em->shouldReceive('getClassMetadata')->times(count($transactions))->andReturn(
            new ClassMetadata(Account::class)
        );

        $args = m::mock(OnFlushEventArgs::class);
        $args->shouldReceive('getEntityManager')->once()->andReturn($em);

        return [$uow, $em, $args];
    }

    /**
     * @test
     */
    public function shouldUpdateAccountBalance()
    {
        $user = m::mock(User::class);
        $account = new Account(new Currency('EUR'));
        $account->setUser($user);

        list($uow, $em, $args) = $this->getBaseMocks(
            $account,
            [
                new Transaction($account, 100, Transaction::TYPE_DEPOSIT),
            ]
        );

        $listener = new TransactionListener();
        $listener->onFlush($args);

        $this->assertInstanceOf(Money::class, $account->getBalance());
        $this->assertEquals(Money::EUR(100), $account->getBalance());
    }

    /**
     * @test
     */
    public function shouldBeAbleToProcessMultipleTransactions()
    {
        $user = m::mock(User::class);
        $account = new Account(new Currency('EUR'));
        $account->setUser($user);

        list($uow, $em, $args) = $this->getBaseMocks(
            $account,
            [
                new Transaction($account, 100, Transaction::TYPE_DEPOSIT),
                new Transaction($account, 250, Transaction::TYPE_DEPOSIT),
                new Transaction($account, 325, Transaction::TYPE_DEPOSIT),
                new Transaction($account, 75, Transaction::TYPE_DEPOSIT),
            ]
        );

        $listener = new TransactionListener();
        $listener->onFlush($args);

        $this->assertInstanceOf(Money::class, $account->getBalance());
        $this->assertEquals(Money::EUR(750), $account->getBalance());
    }

    /**
     * @test
     * @expectedException \LogicException
     */
    public function shouldNotAllowUpdates()
    {
        $user = m::mock(User::class);
        $account = new Account(new Currency('EUR'));
        $account->setUser($user);

        $transactions = [
            new Transaction($account, 100, Transaction::TYPE_DEPOSIT),
        ];

        $uow = m::mock(UnitOfWork::class);
        $uow->shouldReceive('getScheduledEntityInsertions')->once()->andReturn([]);
        $uow->shouldReceive('getScheduledEntityUpdates')->once()->andReturn($transactions);

        $em = m::mock(EntityManager::class);
        $em->shouldReceive('getUnitOfWork')->once()->andReturn($uow);

        $args = m::mock(OnFlushEventArgs::class);
        $args->shouldReceive('getEntityManager')->once()->andReturn($em);

        $listener = new TransactionListener();
        $listener->onFlush($args);
    }

    /**
     * @test
     * @expectedException \LogicException
     */
    public function shouldNotAllowDeletions()
    {
        $user = m::mock(User::class);
        $account = new Account(new Currency('EUR'));
        $account->setUser($user);

        $transactions = [
            new Transaction($account, 100, Transaction::TYPE_DEPOSIT),
        ];

        $uow = m::mock(UnitOfWork::class);
        $uow->shouldReceive('getScheduledEntityInsertions')->once()->andReturn([]);
        $uow->shouldReceive('getScheduledEntityUpdates')->once()->andReturn([]);
        $uow->shouldReceive('getScheduledEntityDeletions')->once()->andReturn($transactions);

        $em = m::mock(EntityManager::class);
        $em->shouldReceive('getUnitOfWork')->once()->andReturn($uow);

        $args = m::mock(OnFlushEventArgs::class);
        $args->shouldReceive('getEntityManager')->once()->andReturn($em);

        $listener = new TransactionListener();
        $listener->onFlush($args);
    }
}
