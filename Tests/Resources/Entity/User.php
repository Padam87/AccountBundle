<?php

namespace Padam87\AccountBundle\Tests\Resources\Entity;

use Doctrine\Common\Collections\ArrayCollection;

class User
{
    /**
     * @var ArrayCollection|Account[]
     *
     * @ORM\OneToMany(targetEntity=Account::class, mappedBy="user")
     */
    protected $accounts;

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
        $this->accounts = $accounts;

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function addAccount($account)
    {
        $this->accounts[] = $account;

        $account->setUser($this);

        return $this;
    }
}
