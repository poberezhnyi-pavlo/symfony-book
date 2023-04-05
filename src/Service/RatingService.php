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
    public function calcReviewRatingFroBook(int $id, int $total): float
    {
        return $total > 0 ? $this->reviewRepository->getBookTotalRatingSum($id) / $total : 0;
    }
}
