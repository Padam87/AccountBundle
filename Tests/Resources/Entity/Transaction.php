<?php

namespace Padam87\AccountBundle\Tests\Resources\Entity;

class Transaction extends \Padam87\AccountBundle\Entity\Transaction
{
    const TYPE_DEPOSIT = 1;
    const TYPE_WITHDRAWAL = 2;
    const TYPE_UNKNOWN = 3; // this type is used to test exceptions -> unmarked for a purpose

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
