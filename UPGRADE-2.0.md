# Changes
- `Account#user` property has been removed to allow accounts for other entities
- `UserInterface` has been renamed to `AccountHolderInterface`

# Upgrade guide:
- Remove the user class from the bundle's configuration:

```yaml
padam87_account:
    classes:
        account: AppBundle\Entity\Account
        transaction: AppBundle\Entity\Transaction
        user: AppBundle\Entity\User
    currencies: ['EUR']
    registration_listener: true # false by default
```

```yaml
padam87_account:
    classes:
        account: AppBundle\Entity\Account
        transaction: AppBundle\Entity\Transaction
    currencies: ['EUR']
    registration_listener: true # false by default
```

- Change the `UserInterface` to `AccountHolderInterface` on your user class
- Add the `user` relation to your `Account` entity
- Add a constructor to your `Account` entity

```php
    /**
     * @param User    $user
     * @param Currency $currency
     */
    public function __construct(User $user, Currency $currency)
    {
        $this->balance = new Money(0, $currency);
        $this->user = $user;
    }
```
