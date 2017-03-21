<?php

namespace Padam87\AccountBundle\Entity;

use Doctrine\Common\Collections\ArrayCollection;

interface AccountHolderInterface
{
    /**
     * @return string
     */
    public function getAccountClass();

    /**
     * @param $currencyCode
     *
     * @return AccountInterface|null
     */
    public function getAccount($currencyCode);

    /**
     * @return AccountInterface[]|ArrayCollection
     */
    public function getAccounts();

    /**
     * @param AccountInterface[]|ArrayCollection $accounts
     *
     * @return $this
     */
    public function setAccounts($accounts);

    /**
     * @param AccountInterface $account
     *
     * @return $this
     */
    public function addAccount(AccountInterface $account);
}
