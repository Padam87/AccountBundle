<?php

namespace Padam87\AccountBundle\Tests\Entity;

use Mockery as m;
use Money\Currency;
use Money\Money;
use Padam87\AccountBundle\Entity\UserInterface;
use Padam87\AccountBundle\Tests\Resources\Entity\Account;
use Padam87\AccountBundle\Tests\Resources\Entity\Transaction;
use Padam87\AccountBundle\Tests\Resources\Entity\User;

class TransactionTest extends \PHPUnit_Framework_TestCase
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
        $account = new Account($user, new Currency('EUR'));

        $transaction1 = new Transaction($account, 100, Transaction::TYPE_WITHDRAWAL);
        $transaction2 = new Transaction($account, -100, Transaction::TYPE_WITHDRAWAL);

        $this->assertEquals(Money::EUR(-100), $transaction1->getAmount());
        $this->assertEquals(Money::EUR(-100), $transaction2->getAmount());
    }

    /**
     * @test
     */
    public function shouldEnforcePositiveTypes()
    {
        $user = m::mock(User::class);
        $account = new Account($user, new Currency('EUR'));

        $transaction1 = new Transaction($account, 100, Transaction::TYPE_DEPOSIT);
        $transaction2 = new Transaction($account, -100, Transaction::TYPE_DEPOSIT);

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
        $account = new Account($user, new Currency('EUR'));

        new Transaction($account, 100, Transaction::TYPE_UNKNOWN);
    }
}
