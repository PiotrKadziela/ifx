<?php

declare(strict_types=1);

namespace App\Domain\Entity;

use App\Domain\Exception\BalanceTooLowException;
use App\Domain\Exception\CurrencyMismatchException;
use App\Domain\Exception\DailyPaymentsLimitExceededException;
use App\Domain\ValueObject\Money;
use DateTimeImmutable;

class Account
{
    private array $payments = [];

    public function __construct(
        private readonly string $id,
        private readonly string $currency
    ) {}

    public function addCredit(Money $amount): void
    {
        $this->assertCurrencyMatches($amount->currency);
        $this->payments[] = new Payment($amount, Payment::TYPE_CREDIT, new DateTimeImmutable());
    }

    public function addDebit(Money $amount): void
    {
        $this->assertCurrencyMatches($amount->currency);
        $this->checkDailyLimit();

        $fee = $amount->multiply(0.005);
        $total = $amount->add($fee);

        if ($this->getBalance()->isLessThan($total)) {
            throw BalanceTooLowException::create();
        }

        $this->payments[] = new Payment($total, Payment::TYPE_DEBIT, new DateTimeImmutable());
    }

    public function getBalance(): Money
    {
        $balance = new Money(0, $this->currency);
        foreach ($this->payments as $payment) {
            if ($payment->type === Payment::TYPE_CREDIT) {
                $balance = $balance->add($payment->amount);
            }

            if ($payment->type === Payment::TYPE_DEBIT) {
                $balance = $balance->add($payment->amount->negate());
            }
        }
        return $balance;
    }

    private function assertCurrencyMatches(string $currency): void
    {
        if ($this->currency !== $currency) {
            throw CurrencyMismatchException::create();
        }
    }

    private function checkDailyLimit(): void
    {
        $today = (new DateTimeImmutable())->format('Y-m-d');
        $todayDebits = array_filter($this->payments, fn(Payment $p) =>
            $p->type === Payment::TYPE_DEBIT &&
            $p->createdAt->format('Y-m-d') === $today
        );

        if (count($todayDebits) >= 3) {
            throw DailyPaymentsLimitExceededException::create();
        }
    }

    public function getId(): string
    {
        return $this->id;
    }
}