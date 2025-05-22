<?php

declare(strict_types=1);

namespace App\Domain\Entity;

use App\Domain\ValueObject\Money;
use DateTimeImmutable;

class Payment
{
    const TYPE_DEBIT = 'debit';
    const TYPE_CREDIT = 'credit';

    public function __construct(
        public readonly Money $amount,
        public readonly string $type,
        public readonly DateTimeImmutable $createdAt
    ) {}
}