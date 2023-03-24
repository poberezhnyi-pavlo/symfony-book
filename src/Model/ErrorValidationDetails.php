<?php

declare(strict_types=1);

namespace App\Model;

class ErrorValidationDetails
{
    /**
     * @var ErrorValidationDetailsItem[]
     */
    private array $violations = [];

    public function addViolation(string $filed, string $message): void
    {
        $this->violations[] = new ErrorValidationDetailsItem($filed, $message);
    }

    /**
     * @return ErrorValidationDetailsItem[]
     */
    public function getViolations(): array
    {
        return $this->violations;
    }
}
