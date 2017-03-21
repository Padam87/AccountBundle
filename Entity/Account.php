<?php

namespace Padam87\AccountBundle\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;
use Money\Currency;
use Money\Money;
use Symfony\Component\Validator\Constraints as Assert;

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
     * @var ArrayCollection|Transaction[]
     *
     * @ORM\OneToMany(targetEntity=Transaction::class, mappedBy="account", indexBy="id")
     * @ORM\OrderBy({"createdAt" = "DESC"})
     */
    protected $transactions;

    /**
     * @param Currency $currency
     */
    public function __construct(Currency $currency)
    {
        $this->balance = new Money(0, $currency);
    }

    /**
     * {@inheritdoc}
     */
    public function getCurrency()
    {
        return $this->balance->getCurrency();
    }

    /**
     * {@inheritdoc}
     */
    public function getBalance()
    {
        return $this->balance;
    }

    /**
     * {@inheritdoc}
     */
    public function setBalance(Money $balance)
    {
        $this->balance = $balance;

        return $this;
    }

    /**
     * @return ArrayCollection|Transaction[]
     */
    public function getTransactions()
    {
        return $this->transactions;
    }
}
