<?php

namespace Padam87\AccountBundle\Tests\Resources\Entity;

use Money\Currency;
use Money\Money;
use Padam87\AccountBundle\Entity\AccountHolderInterface;
use Padam87\AccountBundle\Entity\AccountInterface;
use Padam87\AccountBundle\Entity\UserInterface;

class Account extends \Padam87\AccountBundle\Entity\Account
{
    /**
     * @var int
     *
     * @ORM\Column(type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue
     */
    protected $id;

    /**
     * @var User
     *
     * @ORM\ManyToOne(targetEntity=User::class)
     */
    protected $user;

    /**
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @return User
     */
    public function getUser()
    {
        return $this->user;
    }

    /**
     * @param User $user
     *
     * @return Account
     */
    public function setUser($user)
    {
        $this->user = $user;

        return $this;
    }

    /**
     * @param AccountHolderInterface $accountHolder
     *
     * @return $this
     */
    public function setAccountHolder(AccountHolderInterface $accountHolder)
    {
        return $this->setUser($accountHolder);
    }
}
