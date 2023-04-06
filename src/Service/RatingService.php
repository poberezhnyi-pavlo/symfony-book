<?php

declare(strict_types=1);

namespace App\Service;

use App\Repository\ReviewRepository;
use Doctrine\ORM\NonUniqueResultException;
use Doctrine\ORM\NoResultException;

class RatingService
{
    public function __construct(private readonly ReviewRepository $reviewRepository)
    {
    }

    /**
     * @throws NonUniqueResultException
     * @throws NoResultException
     */
    public function calcReviewRatingFroBook(int $id): Rating
    {
        $total = $this->reviewRepository->countByBookId($id);
        $rating = $total > 0 ? $this->reviewRepository->getBookTotalRatingSum($id) / $total : 0;

        return new Rating($total, $rating);
    }
}
