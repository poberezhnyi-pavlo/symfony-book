<?php

declare(strict_types=1);

namespace App\Model;

use App\Service\Recommendation\Model\RecommendationItem;

class RecommendedBookListResponse
{
    /**
     * @var RecommendedBook[]
     */
    private array $items;

    /**
     * @param BookListItem[] $items
     */
    public function __construct(array $items)
    {
        $this->items = $items;
    }

    /**
     * @return RecommendationItem[]
     */
    public function getItems(): array
    {
        return $this->items;
    }
}
