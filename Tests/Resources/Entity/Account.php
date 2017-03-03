<?php

namespace Padam87\AccountBundle\Tests\Resources\Entity;

use Money\Currency;
use Money\Money;
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
     * @param User    $user
     * @param Currency $currency
     */
    public function __construct(User $user, Currency $currency)
    {
        $this->balance = new Money(0, $currency);
        $this->user = $user;
    }

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
}
