<?php

declare(strict_types=1);

namespace App\Model;

class ErrorResponse
{
    public function __construct(private readonly string $message, private readonly mixed $details = null)
    {
    }

    public function getDetails(): mixed
    {
        return $this->details;
    }

    public function getMessage(): string
    {
        return $this->message;
    }
}
