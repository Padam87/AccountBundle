<?php

namespace Padam87\AccountBundle\Tests\Service;

use Doctrine\Bundle\DoctrineBundle\Registry;
use Doctrine\ORM\EntityManager;
use FOS\UserBundle\Event\FilterUserResponseEvent;
use Mockery as m;
use Money\Currency;
use Money\Exception\UnknownCurrencyException;
use Money\Money;
use Padam87\AccountBundle\EventListener\RegistrationListener;
use Padam87\AccountBundle\Service\Accountant;
use Padam87\AccountBundle\Tests\Resources\Entity\Account;
use Padam87\AccountBundle\Tests\Resources\Entity\User;
use PHPUnit\Framework\TestCase;

class AccountantTest extends TestCase
{
    public function tearDown()
    {
        parent::tearDown();

        m::close();
    }

    /**
     * @test getAccount
     */
    public function shouldReturnNullAccount()
    {
        $user = new User();

        $accountant = new Accountant();

        $this->assertNull($accountant->getAccount($user, new Currency('EUR')));
    }

    /**
     * @test getAccount
     */
    public function shouldReturnAccountIfCurrencyMatches()
    {
        $user = new User();
        $user->addAccount(new Account(new Currency('EUR')));

        $accountant = new Accountant();

        $this->assertInstanceOf(Account::class, $accountant->getAccount($user, new Currency('EUR')));
    }

    /**
     * @test getAccount
     */
    public function shouldReturnNullIfCurrencyMismatch()
    {
        $user = new User();
        $user->addAccount(new Account(new Currency('USD')));

        $accountant = new Accountant();

        $this->assertNull($accountant->getAccount($user, new Currency('EUR')));
    }

    /**
     * @test createAccount
     */
    public function shouldCreateAccount()
    {
        $user = new User();

        $accountant = new Accountant();
        $accountant->createAccount($user, new Currency('EUR'));

        $this->assertCount(1, $user->getAccounts());
        $this->assertNotNull($user->getAccount('EUR'));
    }

    /**
     * @test createAccount
     * @expectedException \LogicException
     */
    public function shouldThrowExceptionIfAlreadyExists()
    {
        $user = new User();
        $user->addAccount(new Account(new Currency('EUR')));

        $accountant = new Accountant();
        $accountant->createAccount($user, new Currency('EUR'));
    }

    /**
     * @test getOrCreateAccount
     */
    public function shouldCreateAccountIfNotExists()
    {
        $user = new User();

        $accountant = new Accountant();
        $accountant->getOrCreateAccount($user, new Currency('EUR'));

        $this->assertCount(1, $user->getAccounts());
        $this->assertNotNull($user->getAccount('EUR'));
    }

    /**
     * @test getBalance
     */
    public function shouldGetBalance()
    {
        $account = new Account(new Currency('EUR'));
        $account->setBalance(Money::EUR(1000));

        $accountant = new Accountant();
        $balance = $accountant->getBalance($account);

        $this->assertEquals(1000, $balance->getAmount());
    }

    /**
     * @test getLockedBalance
     */
    public function shouldGetLockedBalance()
    {
        $account = new Account(new Currency('EUR'));

        $accountant = new Accountant();
        $balance = $accountant->getLockedBalance($account);

        $this->assertEquals(0, $balance->getAmount());
    }

    /**
     * @test getAvailableBalance
     */
    public function shouldGetAvailableBalance()
    {
        $account = new Account(new Currency('EUR'));
        $account->setBalance(Money::EUR(1000));

        $accountant = m::mock(Accountant::class)->makePartial();
        $accountant->shouldReceive('getLockedBalance')->andReturn(Money::EUR(500));

        $balance = $accountant->getAvailableBalance($account);

        $this->assertEquals(500, $balance->getAmount());
    }
}
