<?php

namespace Padam87\AccountBundle\Tests\Entity;

use Mockery as m;
use Money\Currency;
use Money\Money;
use Padam87\AccountBundle\Entity\UserInterface;
use Padam87\AccountBundle\Tests\Resources\Entity\Account;
use Padam87\AccountBundle\Tests\Resources\Entity\Transaction;
use Padam87\AccountBundle\Tests\Resources\Entity\User;
use PHPUnit\Framework\TestCase;

class TransactionTest extends TestCase
{
    public function tearDown()
    {
        parent::tearDown();

        m::close();
    }

    /**
     * @test
     */
    public function shouldEnforceNegativeTypes()
    {
        $user = m::mock(User::class);
        $account = new Account(new Currency('EUR'));
        $account->setUser($user);

        $transaction1 = new Transaction($account, Money::EUR(100), Transaction::TYPE_WITHDRAWAL);
        $transaction2 = new Transaction($account, Money::EUR(-100), Transaction::TYPE_WITHDRAWAL);

        $this->assertEquals(Money::EUR(-100), $transaction1->getAmount());
        $this->assertEquals(Money::EUR(-100), $transaction2->getAmount());
    }

    /**
     * @test
     */
    public function shouldEnforcePositiveTypes()
    {
        $user = m::mock(User::class);
        $account = new Account(new Currency('EUR'));
        $account->setUser($user);

        $transaction1 = new Transaction($account, Money::EUR(100), Transaction::TYPE_DEPOSIT);
        $transaction2 = new Transaction($account, Money::EUR(-100), Transaction::TYPE_DEPOSIT);

        $this->assertEquals(Money::EUR(100), $transaction1->getAmount());
        $this->assertEquals(Money::EUR(100), $transaction2->getAmount());
    }

    /**
     * @test
     * @expectedException \LogicException
     */
    public function shouldForceTransactionTypeSelection()
    {
        $user = m::mock(User::class);
        $account = new Account(new Currency('EUR'));
        $account->setUser($user);

        new Transaction($account, Money::EUR(100), Transaction::TYPE_UNKNOWN);
    }
}
