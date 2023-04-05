<?php

declare(strict_types=1);

namespace App\Service\Recommendation\Model;

final class RecommendationItem
{
    public function __construct(private readonly int $id)
    {
    }

    public function getId(): int
    {
        return $this->id;
    }
}
