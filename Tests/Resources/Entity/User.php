<?php

namespace Padam87\AccountBundle\Tests\Resources\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Padam87\AccountBundle\Entity\AccountHolderInterface;
use Padam87\AccountBundle\Entity\AccountInterface;

class User implements AccountHolderInterface
{
    /**
     * @var ArrayCollection|Account[]
     *
     * @ORM\OneToMany(targetEntity=Account::class, mappedBy="user")
     */
    protected $accounts;

    public function __construct()
    {
        $this->accounts = new ArrayCollection();
    }

    /**
     * @return string
     */
    public function getAccountClass()
    {
        return Account::class;
    }

    /**
     * @param string $currencyCode
     *
     * @return Account|null
     */
    public function getAccount($currencyCode = 'EUR')
    {
        foreach ($this->accounts as $account) {
            if ($account->getCurrency()->getCode() === $currencyCode) {
                return $account;
            }
        }

        return null;
    }

    /**
     * {@inheritdoc}
     */
    public function getAccounts()
    {
        return $this->accounts;
    }

    /**
     * {@inheritdoc}
     */
    public function setAccounts($accounts)
    {
        $this->accounts = [];

        foreach ($accounts as $account) {
            $this->addAccount($account);
        }

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function addAccount(AccountInterface $account)
    {
        $this->accounts[] = $account;

        $account->setAccountHolder($this);

        return $this;
    }
}
