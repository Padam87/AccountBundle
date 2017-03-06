<?php

namespace Padam87\AccountBundle\Entity;

use Doctrine\Common\Collections\ArrayCollection;

interface AccountHolderInterface
{
    /**
     * @return Account[]|ArrayCollection
     */
    public function getAccounts();

    /**
     * @param Account[]|ArrayCollection $accounts
     *
     * @return $this
     */
    public function setAccounts($accounts);

    /**
     * @param Account $account
     *
     * @return $this
     */
    public function addAccount($account);
}
