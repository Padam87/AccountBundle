# Usage

### Guidelines
- Every transaction must have a type
- Every transaction type must be explicitly positive, or negative (use 2 types if you have a type which could be both)
- **Transactions** should be considered **immutable**.
Never modify or delete a transaction, always create a new one if you need to change the balance.
- **An account's balance should never be modified directly**.
Always add a transaction to interact with the balance.
- This bundle is still in a development phase, and it covers a highly sensitive area.
Always check your code, but in this case please **double check** mine too, and **please help improve this bundle** :)

### Usage
Add `1 EUR` to the user's balance

```php
$em = $this->getDoctrine()->getManager();
$user = $this->getUser();

if (null === $account = $user->getAccount('EUR')) {
    $account = new Account(new Currency('EUR'));
    
    $user->addAccount($account);
    
    $em->persist($account);
}

$transaction = new Transaction($account, 100, Transaction::TYPE_DEPOSIT);

$em->persist($transaction);
$em->flush();
```
