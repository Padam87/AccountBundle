#Installation

### Add the dependency
```composer require padam87/account-bundle```

### Register the bundle in the kernel
```php
new Padam87\AccountBundle\Padam87AccountBundle(),
```

### Configure the bundle
```yaml
padam87_account:
    classes:
        account: App\Entity\Account
        transaction: App\Entity\Transaction
```

### Create the entities
#### Account
```php
<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Padam87\AccountBundle\Entity\Account as BaseAccount;
use Padam87\AccountBundle\Entity\AccountHolderInterface;

/**
 * @ORM\Entity()
 */
class Account extends BaseAccount
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
     * @ORM\ManyToOne(targetEntity=User::class, inversedBy="accounts")
     */
    protected $user;

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
     * @return $this
     */
    public function setUser(User $user)
    {
        $this->user = $user;

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function setAccountHolder(AccountHolderInterface $accountHolder)
    {
        if (!$accountHolder instanceof User) {
            throw new \LogicException();
        }

        return $this->setUser($accountHolder);
    }
    
    //...
}
```

#### Transaction
```php
<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Padam87\AccountBundle\Entity\Transaction as BaseTransaction;

/**
 * @ORM\Entity()
 */
class Transaction extends BaseTransaction
{
    const TYPE_DEPOSIT = 1;
    const TYPE_WITHDRAWAL = 2;

    /**
     * {@inheritdoc}
     */
    public static $positiveTypes = [
        self::TYPE_DEPOSIT
    ];

    /**
     * {@inheritdoc}
     */
    public static $negativeTypes = [
        self::TYPE_WITHDRAWAL
    ];
    
    /**
     * @var int
     *
     * @ORM\Column(type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue
     */
    protected $id;
    
    //...
}
```
`Blameable` and `Timestampable` behaviours are highly recommended.
Not supported out of the box to allow you to choose your own implementation.

#### User
```php
<?php

namespace AppBundle\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;
use FOS\UserBundle\Model\User as BaseUser;
use Padam87\AccountBundle\Entity\AccountInterface;

/**
 * @ORM\Entity
 * @ORM\Table(name="users")
 */
class User extends BaseUser
{
    /**
     * @var ArrayCollection|Account[]
     *
     * @ORM\OneToMany(targetEntity=Account::class, mappedBy="user")
     */
    protected $accounts;
    
    /**
     * @return string
     */
    public function getAccountClass()
    {
        return Account::class;
    }

    /**
     * {@inheritdoc}
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
```
