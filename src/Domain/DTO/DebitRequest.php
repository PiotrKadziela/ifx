<?php

declare(strict_types=1);

namespace App\Domain\DTO;

use App\Domain\ValueObject\Money;

class DebitRequest
{
    public function __construct(
        public string $accountId,
        public Money $amount
    ) {}
}