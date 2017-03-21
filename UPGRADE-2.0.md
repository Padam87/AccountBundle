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
        #user: AppBundle\Entity\User
    currencies: ['EUR']
    registration_listener: true # false by default
```

- Change the `UserInterface` to `AccountHolderInterface` on your user class
- Add the `user` relation to your `Account` entity
- Change the instanciation of the `Account` entity
```php
$account = new Account($user, new Currency('EUR'));

// to

$account = new Account(new Currency('EUR'));
$account->setUser($user);
```
- Update the methods to comply with the new `AccountInterface`
