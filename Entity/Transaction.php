<?php

namespace Padam87\AccountBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;
use Money\Money;

/**
 * @ORM\MappedSuperclass()
 */
abstract class Transaction
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
     * @ORM\ManyToOne(targetEntity=Account::class, inversedBy="transactions")
     */
    protected $account;

    /**
     * @var int
     *
     * @ORM\Column(type="money")
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

    /**
     * @param Account $account
     * @param         $amount
     * @param         $type
     */
    public function __construct(Account $account, $amount, $type)
    {
        $this->setAccount($account);
        $this->setType($type);
        $this->setAmount($amount);
    }

    public function isPositive()
    {
        return in_array($this->getType(), self::$positiveTypes);
    }

    public function isNegative()
    {
        return in_array($this->getType(), self::$negativeTypes);
    }

    /**
     * @return Account
     */
    public function getAccount()
    {
        return $this->account;
    }

    /**
     * @param Account $account
     *
     * @return Transaction
     */
    public function setAccount($account)
    {
        $this->account = $account;

        return $this;
    }

    /**
     * @param bool $plain
     *
     * @return Money|int
     */
    public function getAmount($plain = false)
    {
        if ($plain) {
            return $this->amount;
        }

        return new Money($this->amount, $this->getAccount()->getCurrency());
    }

    /**
     * @param int $amount
     *
     * @return Transaction
     */
    public function setAmount($amount)
    {
        $amount = abs($amount);

        if (in_array($this->type, self::$negativeTypes)) {
            $amount = $amount * -1;
        }

        $this->amount = $amount;

        return $this;
    }

    /**
     * @return int
     */
    public function getType()
    {
        return $this->type;
    }

    /**
     * @param int $type
     *
     * @return Transaction
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

        $this->setAmount($this->amount); // re-set the amount to ensure negative / positive amounts

        return $this;
    }

    /**
     * @return string
     */
    public function getComment()
    {
        return $this->comment;
    }

    /**
     * @param string $comment
     *
     * @return Transaction
     */
    public function setComment($comment)
    {
        $this->comment = $comment;

        return $this;
    }
}
