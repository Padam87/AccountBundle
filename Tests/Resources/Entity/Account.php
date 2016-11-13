<?php

namespace Padam87\AccountBundle\Tests\Resources\Entity;

use Padam87\AccountBundle\Entity\UserInterface;

class Account extends \Padam87\AccountBundle\Entity\Account
{
    /**
     * @return UserInterface
     */
    public function getUser()
    {
        /** @noinspection All */
        return parent::getUser();
    }
}
