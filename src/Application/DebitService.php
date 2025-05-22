<?php

declare(strict_types=1);

namespace App\Application;

use App\Domain\Repository\AccountRepositoryInterface;
use App\Domain\DTO\DebitRequest;

class DebitService
{
    public function __construct(
        private readonly AccountRepositoryInterface $accountRepository,
    ) {}

    public function execute(DebitRequest $request): void
    {
        $account = $this->accountRepository->getByAccountId($request->accountId);
        $account->addDebit($request->amount);
        $this->accountRepository->save($account);
    }
}