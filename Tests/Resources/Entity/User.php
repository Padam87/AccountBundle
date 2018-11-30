<?php

namespace Padam87\AccountBundle\Tests\Resources\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Padam87\AccountBundle\Entity\AccountHolderInterface;
use Padam87\AccountBundle\Entity\AccountInterface;

class User implements AccountHolderInterface
{
    /**
     * @var ArrayCollection|Account[]
     *
     * @ORM\OneToMany(targetEntity=AccountInterface::class, mappedBy="user")
     */
    protected $accounts;

    public function __construct()
    {
        $this->accounts = new ArrayCollection();
    }

    public function getAccountClass(): string
    {
        return Account::class;
    }

    public function getAccount($currencyCode = 'EUR'): ?AccountInterface
    {
        foreach ($this->accounts as $account) {
            if ($account->getCurrency()->getCode() === $currencyCode) {
                return $account;
            }
        }

        return null;
    }

    public function getAccounts(): Collection
    {
        return $this->accounts;
    }

    public function addAccount(AccountInterface $account): self
    {
        $this->accounts->add($account);

        $account->setAccountHolder($this);

        return $this;
    }

    public function removeAccount(AccountInterface $account): self
    {
        $this->accounts->removeElement($account);

        $account->setAccountHolder(null);

        return $this;
    }
}
