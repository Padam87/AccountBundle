<?php

namespace Padam87\AccountBundle\Service;

use Money\Currency;
use Money\Money;
use Padam87\AccountBundle\Entity\AccountHolderInterface;
use Padam87\AccountBundle\Entity\AccountInterface;

class Accountant
{
    public function getAccount(AccountHolderInterface $accountHolder, Currency $currency): ?AccountInterface
    {
        return $accountHolder->getAccount($currency->getCode());
    }

    public function createAccount(AccountHolderInterface $accountHolder, Currency $currency): AccountInterface
    {
        if ($this->getAccount($accountHolder, $currency) != null) {
            throw new \LogicException(
                sprintf(
                    'The %s account for %s already exists.',
                    $currency->getCode(),
                    get_class($accountHolder)
                )
            );
        }

        $class = $accountHolder->getAccountClass();

        /** @var AccountInterface $account */
        $account = new $class($currency);
        $account->setAccountHolder($accountHolder);

        $accountHolder->addAccount($account);

        return $account;
    }

    public function getOrCreateAccount(AccountHolderInterface $accountHolder, Currency $currency): AccountInterface
    {
        if (null === $account = $this->getAccount($accountHolder, $currency)) {
            $account = $this->createAccount($accountHolder, $currency);
        }

        return $account;
    }

    public function getBalance(AccountInterface $account): Money
    {
        return $account->getBalance();
    }

    public function getLockedBalance(AccountInterface $account): Money
    {
        return new Money(0, clone $account->getCurrency());
    }

    public function getAvailableBalance(AccountInterface $account): Money
    {
        return $this->getBalance($account)->subtract($this->getLockedBalance($account));
    }
}
