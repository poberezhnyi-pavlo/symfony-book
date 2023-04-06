<?php

declare(strict_types=1);

namespace App\Service;

use App\Entity\Book;
use App\Model\RecommendedBook;
use App\Model\RecommendedBookListResponse;
use App\Repository\BookRepository;
use App\Service\Recommendation\Exception\AccessDeniedException;
use App\Service\Recommendation\Exception\RequestException;
use App\Service\Recommendation\Model\RecommendationItem;
use App\Service\Recommendation\RecommendationApiService;

class RecommendationService
{
    private const MAX_DESCRIPTION = 150;

    public function __construct(
        private readonly BookRepository $bookRepository,
        private readonly RecommendationApiService $recommendationService
    ) {
    }

    private function map(Book $book): RecommendedBook
    {
        $description = $book->getDescription();
        $description = strlen($description) > self::MAX_DESCRIPTION
            ? substr($description, 0, self::MAX_DESCRIPTION - 3).'...'
            : $description
        ;

        return (new RecommendedBook())
            ->setId($book->getId())
            ->setTitle($book->getTitle())
            ->setSlug($book->getSlug())
            ->setImage($book->getImage())
            ->setShortDescription($description)
        ;
    }

    /**
     * @throws RequestException
     * @throws AccessDeniedException
     */
    public function getRecommendationById(int $id): RecommendedBookListResponse
    {
        $ids = array_map(
            fn (RecommendationItem $item) => $item->getId(),
            $this->recommendationService->getRecommendationsByBookId($id)->getRecommendations()
        );

        return new RecommendedBookListResponse(
            array_map([$this, 'map'], $this->bookRepository->findBooksByIds($ids))
        );
    }
}
