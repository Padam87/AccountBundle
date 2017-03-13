<?php

namespace Padam87\AccountBundle\Entity;

use Money\Currency;
use Money\Money;

interface AccountInterface
{
    /**
     * @param AccountHolderInterface $accountHolder
     *
     * @return $this
     */
    public function setAccountHolder(AccountHolderInterface $accountHolder);

    /**
     * @return Currency
     */
    public function getCurrency();

    /**
     * @return Money
     */
    public function getBalance();

    /**
     * @internal Should only be modified by Transactions, don't call otherwise
     *
     * @param Money $balance
     *
     * @return $this
     */
    public function setBalance(Money $balance);
}
