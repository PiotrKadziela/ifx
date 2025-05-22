<?php

declare(strict_types=1);

namespace App\Domain\Exception;

class DailyPaymentsLimitExceededException extends \Exception
{
    public static function create(): self
    {
        return new self('Daily payments limit has been exceeded.');
    }
}