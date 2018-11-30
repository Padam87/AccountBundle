<?php

namespace Padam87\AccountBundle\Entity;

use Doctrine\Common\Collections\Collection;

interface AccountHolderInterface
{
    public function getAccountClass(): string;

    public function getAccount($currencyCode): ?AccountInterface;

    /**
     * @return AccountInterface[]|Collection
     */
    public function getAccounts(): Collection;

    public function addAccount(AccountInterface $account);

    public function removeAccount(AccountInterface $account);
}
