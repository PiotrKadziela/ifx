<?php

declare(strict_types=1);

namespace App\Domain\Exception;

class BalanceTooLowException extends \Exception
{
    public static function create(): self
    {
        return new self('Account balance is too low.');
    }
}