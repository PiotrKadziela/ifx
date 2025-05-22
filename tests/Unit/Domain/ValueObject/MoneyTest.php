<?php

declare(strict_types=1);

namespace App\Tests\Unit\Domain\ValueObject;

use App\Domain\Exception\CurrencyMismatchException;
use App\Domain\ValueObject\Money;
use PHPUnit\Framework\TestCase;

class MoneyTest extends TestCase
{
    private const CURRENCY = 'PLN';

    private Money $money;

    protected function setUp(): void
    {
        $this->money = new Money(100.0, self::CURRENCY);
    }

    /**
     * @test
     */
    public function testAddMoney(): void
    {
        $this->assertEquals(
            new Money(150.0, self::CURRENCY),
            $this->money->add(
                new Money(50.0, self::CURRENCY)
            )
        );
    }

    /**
     * @test
     */
    public function testIsLessThan(): void
    {
        $this->assertFalse(
            $this->money->isLessThan(
                new Money(50.0, self::CURRENCY)
            )
        );
        
        $this->assertTrue(
            $this->money->isLessThan(
                new Money(150.0, self::CURRENCY)
            )
        );
    }

    /**
     * @test
     */
    public function testMultiplyMoney(): void
    {
        $this->assertEquals(
            new Money(250.0, self::CURRENCY), 
            $this->money->multiply(2.5)
        );
    }

    /**
     * @test
     */
    public function testNegateMoney(): void
    {
        $this->assertEquals(
            new Money(-100.0, self::CURRENCY),
            $this->money->negate()
        );
    }

    /**
     * @test
     * @dataProvider currencyMismatchDataProvider
     */
    public function testCannotAddDifferentCurrencies(string $methodName): void
    {
        $this->expectException(CurrencyMismatchException::class);

        $this->money->$methodName(new Money(50.0, 'EUR'));
    }

    public static function currencyMismatchDataProvider(): array
    {
        return [
            'add' => ['add'],
            'isLessThan' => ['isLessThan'],
        ];
    }
}