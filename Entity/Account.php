<?php

namespace Padam87\AccountBundle\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Money\Currency;
use Money\Money;

/**
 * @ORM\MappedSuperclass()
 */
abstract class Account implements AccountInterface
{
    /**
     * @var Money
     *
     * @ORM\Embedded(class=Money::class)
     */
    protected $balance;

    /**
     * @var Collection|TransactionInterface[]
     *
     * @ORM\OneToMany(targetEntity=TransactionInterface::class, mappedBy="account", indexBy="id")
     * @ORM\OrderBy({"createdAt" = "DESC"})
     */
    protected $transactions;

    public function __construct(Currency $currency)
    {
        $this->balance = new Money(0, $currency);
        $this->transactions = new ArrayCollection();
    }

    public function getCurrency(): Currency
    {
        return $this->balance->getCurrency();
    }

    public function getBalance(): Money
    {
        return $this->balance;
    }

    public function setBalance(Money $balance): AccountInterface
    {
        $this->balance = $balance;

        return $this;
    }

    /**
     * @return Collection|TransactionInterface[]
     */
    public function getTransactions(): Collection
    {
        return $this->transactions;
    }
}
