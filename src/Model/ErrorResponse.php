<?php

declare(strict_types=1);

namespace App\Model;

use OpenApi\Annotations as OA;

class ErrorResponse
{
    public function __construct(private readonly string $message, private readonly mixed $details = null)
    {
    }

    /**
     * @OA\Property(type="object")
     */
    public function getDetails(): mixed
    {
        return $this->details;
    }

    public function getMessage(): string
    {
        return $this->message;
    }
}
