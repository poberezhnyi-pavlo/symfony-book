<?php

declare(strict_types=1);

namespace App\Service\Recommendation\Model;

final class RecommendationResponse
{
    public function __construct(
        private int $id,
        private int $ts,
        private array $recommendations,
    ) {
    }

    public function getId(): int
    {
        return $this->id;
    }

    public function getTs(): int
    {
        return $this->ts;
    }

    /**
     * @return RecommendationItem[]
     */
    public function getRecommendations(): array
    {
        return $this->recommendations;
    }
}
