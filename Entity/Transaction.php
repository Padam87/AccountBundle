<?php

namespace Padam87\AccountBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Money\Money;

/**
 * @ORM\MappedSuperclass()
 */
abstract class Transaction implements TransactionInterface
{
    /**
     * These types will convert any amount to positive
     *
     * @var array
     */
    public static $positiveTypes = [];

    /**
     * These types will convert any amount to negative
     *
     * @var array
     */
    public static $negativeTypes = [];

    /**
     * @var Account
     *
     * @ORM\ManyToOne(targetEntity=AccountInterface::class, inversedBy="transactions")
     */
    protected $account;

    /**
     * @var Money
     *
     * @ORM\Embedded(class=Money::class)
     */
    protected $amount;

    /**
     * @var int
     *
     * @ORM\Column(type="integer")
     */
    protected $type;

    /**
     * @var string
     *
     * @ORM\Column(type="string", length=500, nullable=true)
     */
    protected $comment;

    public function __construct(Account $account, Money $amount, int $type)
    {
        $this->setAccount($account);
        $this->setType($type);
        $this->setAmount($amount);
    }

    public function isPositive(): bool
    {
        return in_array($this->getType(), static::$positiveTypes);
    }

    public function isNegative(): bool
    {
        return in_array($this->getType(), static::$negativeTypes);
    }

    public function getAccount(): AccountInterface
    {
        return $this->account;
    }

    /**
     * @return $this
     */
    public function setAccount(AccountInterface $account)
    {
        $this->account = $account;

        return $this;
    }

    public function getAmount(): Money
    {
        return $this->amount;
    }

    /**
     * @return $this
     */
    public function setAmount(Money $amount)
    {
        $amount = $amount->absolute();

        if ($this->isNegative()) {
            $amount = $amount->negative();
        } elseif (!$this->isPositive()) {
            throw new \LogicException('Every transaction type must be explicitly marked as positive, or negative');
        }

        $this->amount = $amount;

        return $this;
    }

    public function getType(): int
    {
        return $this->type;
    }

    /**
     * @return $this
     */
    public function setType($type)
    {
        if (!in_array($type, static::$positiveTypes) && !in_array($type, static::$negativeTypes)) {
            throw new \LogicException(
                sprintf(
                    'Types must be marked as negative or positive. Given type with the value of "%d" is unknown.',
                    $type
                )
            );
        }

        $this->type = $type;

        if (null !== $this->amount) {
            $this->setAmount($this->amount); // re-set the amount to ensure negative / positive amounts
        }

        return $this;
    }

    public function getComment(): ?string
    {
        return $this->comment;
    }

    /**
     * @return $this
     */
    public function setComment(?string $comment)
    {
        $this->comment = $comment;

        return $this;
    }
}
