<?php

namespace Padam87\AccountBundle\Tests\Resources\Entity;

use Padam87\AccountBundle\Entity\AccountHolderInterface;

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

    public function getId(): int
    {
        return $this->id;
    }

    public function getUser(): ?User
    {
        return $this->user;
    }

    public function setUser(User $user): self
    {
        $this->user = $user;

        return $this;
    }

    public function setAccountHolder(AccountHolderInterface $accountHolder): self
    {
        return $this->setUser($accountHolder);
    }
}
