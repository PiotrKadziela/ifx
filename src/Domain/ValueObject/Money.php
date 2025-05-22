<?php

declare(strict_types=1);

namespace App\Domain\ValueObject;

use App\Domain\Exception\CurrencyMismatchException;

class Money

{
    public function __construct(
        public readonly float $amount,
        public readonly string $currency
    ) {}

    public function add(Money $other): Money
    {
        $this->assertSameCurrency($other);
        return new Money($this->amount + $other->amount, $this->currency);
    }

    public function multiply(float $factor): Money
    {
        return new Money($this->amount * $factor, $this->currency);
    }

    public function isLessThan(Money $other): bool
    {
        $this->assertSameCurrency($other);
        return $this->amount < $other->amount;
    }

    public function negate(): Money
    {
        return new Money(-$this->amount, $this->currency);
    }

    private function assertSameCurrency(Money $other): void
    {
        if ($this->currency !== $other->currency) {
            throw CurrencyMismatchException::create();
        }
    }
}