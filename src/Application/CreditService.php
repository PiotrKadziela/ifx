<?php

declare(strict_types=1);

namespace App\Application;

use App\Domain\Repository\AccountRepositoryInterface;
use App\Domain\DTO\CreditRequest;

class CreditService
{
    public function __construct(
        private readonly AccountRepositoryInterface $accountRepository,
    ) {}

    public function execute(CreditRequest $request): void
    {
        $account = $this->accountRepository->getByAccountId($request->accountId);
        $account->addCredit($request->amount);
        $this->accountRepository->save($account);
    }
}