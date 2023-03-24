<?php

declare(strict_types=1);

namespace App\Model;

use Nelmio\ApiDocBundle\Annotation\Model;
use OpenApi\Annotations as OA;

class ErrorResponse
{
    public function __construct(private readonly string $message, private readonly mixed $details = null)
    {
    }

    /**
     * @OA\Property(type="object", oneOf={
     *
     *     @OA\Schema(ref=@Model(type=ErrorDebugDetail::class)),
     *     @OA\Schema(ref=@Model(type=ErrorValidationDetails::class))
     * })
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
