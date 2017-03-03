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
abstract class Account
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
     * @return Currency
     */
    public function getCurrency()
    {
        return $this->balance->getCurrency();
    }

    /**
     * @return Money
     */
    public function getBalance()
    {
        return $this->balance;
    }

    /**
     * @internal Should only be modified by Transactions, don't call otherwise
     *
     * @param Money $balance
     *
     * @return $this
     */
    public function setBalance($balance)
    {
        $this->balance = $balance;

        return $this;
    }
}
