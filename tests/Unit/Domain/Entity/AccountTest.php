<?php

declare(strict_types=1);

namespace App\Tests\Unit\Domain\Entity;

use App\Domain\Entity\Account;
use App\Domain\Exception\BalanceTooLowException;
use App\Domain\Exception\CurrencyMismatchException;
use App\Domain\Exception\DailyPaymentsLimitExceededException;
use App\Domain\ValueObject\Money;
use PHPUnit\Framework\TestCase;
use Prophecy\PhpUnit\ProphecyTrait;

class AccountTest extends TestCase
{
    use ProphecyTrait;

    private const CURRENCY = 'PLN';
    private const INVALID_CURRENCY = 'EUR';

    private Account $account;

    protected function setUp(): void
    {
        $this->account = new Account('PL123456789', self::CURRENCY);
    }

    /**
     * @test
     */
    public function testAddCreditIncreaseBalance(): void
    {
        $amount = new Money(100.0, self::CURRENCY);
        $this->account->addCredit($amount);

        $this->assertEquals($amount, $this->account->getBalance());
    }

    /**
     * @test
     */
    public function testAddDebitDecreasesBalance(): void
    {
        $this->account->addCredit(new Money(100.0, self::CURRENCY));
        $this->account->addDebit(new Money(20.0, self::CURRENCY));

        $this->assertEquals(new Money(79.9, self::CURRENCY), $this->account->getBalance());
    }

    /**
     * @test
     */
    public function testCannotAddDebitWhenInsufficientFunds(): void
    {
        $this->account->addCredit(new Money(20.0, self::CURRENCY));

        $this->expectException(BalanceTooLowException::class);

        $this->account->addDebit(new Money(21.0, self::CURRENCY));
    }

    /**
     * @test
     */
    public function testCannotAddCreditWhenCurrencyDoesNotMatch(): void
    {
        $this->expectException(CurrencyMismatchException::class);

        $this->account->addCredit(new Money(100.0, self::INVALID_CURRENCY));
    }

    /**
     * @test
     */
    public function testCannotAddDebitWhenCurrencyDoesNotMatch(): void
    {
        $this->account->addCredit(new Money(100.0, self::CURRENCY));

        $this->expectException(CurrencyMismatchException::class);

        $this->account->addDebit(new Money(20.0, self::INVALID_CURRENCY));
    }

    /**
     * @test
     */
    public function testCannotAddDebitWhenDailyPaymentsLimitExceeded(): void
    {
        $this->account->addCredit(new Money(100.0, self::CURRENCY));
        $this->account->addDebit(new Money(20.0, self::CURRENCY));
        $this->account->addDebit(new Money(20.0, self::CURRENCY));
        $this->account->addDebit(new Money(20.0, self::CURRENCY));

        $this->expectException(DailyPaymentsLimitExceededException::class);

        $this->account->addDebit(new Money(20.0, self::CURRENCY));
    }
}