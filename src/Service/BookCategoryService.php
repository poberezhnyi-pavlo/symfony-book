<?php

declare(strict_types=1);

namespace App\Service;

use App\Entity\BookCategory;
use App\Model\BookCategory as BookCategoryModal;
use App\Model\BookCategoryListResponse;
use App\Repository\BookCategoryRepository;

final class BookCategoryService
{
    public function __construct(private readonly BookCategoryRepository $bookCategoryRepository)
    {
    }

    public function getCategories(): BookCategoryListResponse
    {
        $categories = $this->bookCategoryRepository->findAllSortedByTitle();

        $items = array_map(
            static fn (BookCategory $bookCategory) => new BookCategoryModal(
                $bookCategory->getId(), $bookCategory->getTitle(), $bookCategory->getSlug(),
            ),
            $categories
        );

        return new BookCategoryListResponse($items);
    }
}
