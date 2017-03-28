<?php

namespace Padam87\AccountBundle\Service;

use Money\Currency;
use Money\Money;
use Padam87\AccountBundle\Entity\AccountHolderInterface;
use Padam87\AccountBundle\Entity\AccountInterface;

class Accountant
{
    /**
     * @param AccountHolderInterface $accountHolder
     * @param Currency               $currency
     *
     * @return null|AccountInterface
     */
    public function getAccount(AccountHolderInterface $accountHolder, Currency $currency)
    {
        return $accountHolder->getAccount($currency->getCode());
    }

    /**
     * @param AccountHolderInterface $accountHolder
     * @param Currency               $currency
     *
     * @return AccountInterface
     */
    public function createAccount(AccountHolderInterface $accountHolder, Currency $currency)
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

    /**
     * @param AccountHolderInterface $accountHolder
     * @param Currency               $currency
     *
     * @return AccountInterface
     */
    public function getOrCreateAccount(AccountHolderInterface $accountHolder, Currency $currency)
    {
        if (null === $account = $this->getAccount($accountHolder, $currency)) {
            $account = $this->createAccount($accountHolder, $currency);
        }

        return $account;
    }

    /**
     * @param AccountInterface $account
     *
     * @return Money
     */
    public function getBalance(AccountInterface $account)
    {
        return $account->getBalance();
    }

    /**
     * By default, this will always return 0.
     *
     * You may override it to calculate the users locked balance.
     *
     * @param AccountInterface $account
     *
     * @return Money
     */
    public function getLockedBalance(AccountInterface $account)
    {
        return new Money(0, clone $account->getCurrency());
    }

    /**
     * @param AccountInterface $account
     *
     * @return Money
     */
    public function getAvailableBalance(AccountInterface $account)
    {
        return $this->getBalance($account)->subtract($this->getLockedBalance($account));
    }
}
