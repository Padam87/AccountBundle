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
        account: AppBundle\Entity\Account
        transaction: AppBundle\Entity\Transaction
        user: AppBundle\Entity\User
    currencies: ['EUR']
    registration_listener: true # false by default
```
The registration listener creates accounts for users on registration. Requires the [FOSUserBundle](https://github.com/FriendsOfSymfony/FOSUserBundle).

### Create the entities
#### Account
```php
<?php

namespace AppBundle\Entity;

use AppBundle\Entity\Traits\IdTrait;
use Doctrine\ORM\Mapping as ORM;
use Padam87\AccountBundle\Entity\Account as BaseAccount;

/**
 * @ORM\Entity()
 */
class Account extends BaseAccount
{
    use IdTrait;

    /**
     * @return User
     */
    public function getUser()
    {
        /** @noinspection All */
        return parent::getUser();
    }
}
```

#### Transaction
```php
<?php

namespace AppBundle\Entity;

use AppBundle\Entity\Traits\Blameable;
use AppBundle\Entity\Traits\IdTrait;
use Doctrine\ORM\Mapping as ORM;
use Gedmo\Timestampable\Traits\TimestampableEntity;
use Padam87\AccountBundle\Entity\Transaction as BaseTransaction;

/**
 * @ORM\Entity()
 */
class Transaction extends BaseTransaction
{
    use IdTrait;
    use Blameable;
    use TimestampableEntity;

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
}
```
The `Blameable` and `Timestampable` behaviours are optional, but highly recommended. Not supported out of the box to allow you to choose your own implementation.

#### User
```php
<?php

namespace AppBundle\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;
use FOS\UserBundle\Model\User as BaseUser;
use Gedmo\Mapping\Annotation as Gedmo;
use Padam87\AccountBundle\Entity\UserInterface;

/**
 * @ORM\Entity
 * @ORM\Table(name="users")
 */
class User extends BaseUser implements UserInterface
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
```
