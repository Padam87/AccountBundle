<?php

namespace Padam87\AccountBundle\Entity;

use Money\Currency;
use Money\Money;
use Padam87\AccountBundle\EventListener\TransactionListener;

interface AccountInterface
{
    public function setAccountHolder(AccountHolderInterface $accountHolder);

    public function getCurrency(): Currency;

    public function getBalance(): Money;

    /**
     * @internal Should only be modified by Transactions, don't call otherwise
     * @see TransactionListener
     */
    public function setBalance(Money $balance);
}
