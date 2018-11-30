<?php

namespace Padam87\AccountBundle\Entity;

use Money\Money;

interface TransactionInterface
{
    public function getAccount(): ?AccountInterface;

    public function getAmount(): Money;

    public function getType(): int;

    public function isPositive(): bool;

    public function isNegative(): bool;
}
