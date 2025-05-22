<?php

declare(strict_types=1);

namespace App\Tests\Unit\Application;

use App\Application\CreditService;
use App\Domain\DTO\CreditRequest;
use App\Domain\Entity\Account;
use App\Domain\Repository\AccountRepositoryInterface;
use App\Domain\ValueObject\Money;
use PHPUnit\Framework\TestCase;
use Prophecy\PhpUnit\ProphecyTrait;
use Prophecy\Prophecy\ObjectProphecy;

class CreditServiceTest extends TestCase
{
    use ProphecyTrait;

    private const ACCOUNT_ID = 'PL123456789';
    private const AMOUNT = 12.34;
    private const CURRENCY = 'PLN';

    /**
     * @test
     */
    public function testExecuteRequest()
    {
        $amount = new Money(12.34, 'PLN');

        /** @var Account|ObjectProphecy $account */
        $account = $this->prophesize(Account::class);
        $account->addCredit($amount)->shouldBeCalled();

        /** @var AccountRepositoryInterface|ObjectProphecy $accountRepository */
        $accountRepository = $this->prophesize(AccountRepositoryInterface::class);
        $accountRepository->getByAccountId(self::ACCOUNT_ID)->willReturn($account);
        $accountRepository->save($account)->shouldBeCalled();

        (new CreditService($accountRepository->reveal()))->execute(
            new CreditRequest(self::ACCOUNT_ID, $amount)
        );
    }
}