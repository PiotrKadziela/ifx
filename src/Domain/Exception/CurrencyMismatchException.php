<?php

declare(strict_types=1);

namespace App\Domain\Exception;

class CurrencyMismatchException extends \Exception
{
    public static function create(): self
    {
        return new self('Currency does not match.');
    }
}