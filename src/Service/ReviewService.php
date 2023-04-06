<?php

declare(strict_types=1);

namespace App\Service;

use App\Entity\Review;
use App\Model\Review as ReviewModal;
use App\Model\ReviewPage;
use App\Repository\ReviewRepository;
use Doctrine\ORM\NonUniqueResultException;
use Doctrine\ORM\NoResultException;

class ReviewService
{
    private const PER_PAGE = 5;

    public function __construct(
        private readonly ReviewRepository $reviewRepository,
        private readonly RatingService $ratingService,
    ) {
    }

    /**
     * @throws NonUniqueResultException
     * @throws NoResultException
     */
    public function getReviewPageByBookId(int $id, int $page): ReviewPage
    {
        $offset = max($page - 1, 0) * self::PER_PAGE;
        $paginator = $this->reviewRepository->getPageByBookId($id, $offset, self::PER_PAGE);

        $items = [];

        foreach ($paginator as $item) {
            $items[] = $this->map($item);
        }

        $rating = $this->ratingService->calcReviewRatingFroBook($id);

        return (new ReviewPage())
            ->setRating($rating->getRating())
            ->setTotal($rating->getTotal())
            ->setPage($page)
            ->setPerPage(self::PER_PAGE)
            ->setPages((int) ceil($total / self::PER_PAGE))
            ->setItems($items)
        ;
    }

    private function map(Review $review): ReviewModal
    {
        return (new ReviewModal())
            ->setId($review->getId())
            ->setRating($review->getRating())
            ->setCreatedAt($review->getCreatedAt()->getTimestamp())
            ->setAuthors($review->getAuthors())
            ->setContent($review->getContent())
        ;
    }
}
