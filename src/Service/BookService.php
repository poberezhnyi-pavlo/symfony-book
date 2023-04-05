<?php

declare(strict_types=1);

namespace App\Service;

use App\Entity\Book;
use App\Entity\BookCategory;
use App\Entity\BookToBookFormat;
use App\Exception\BookCategoryNotFoundException;
use App\Mapper\BookMapper;
use App\Model\BookCategory as BookCategoryModel;
use App\Model\BookDetails;
use App\Model\BookFormat;
use App\Model\BookListItem;
use App\Model\BookListResponse;
use App\Repository\BookCategoryRepository;
use App\Repository\BookRepository;
use App\Repository\ReviewRepository;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\NonUniqueResultException;
use Doctrine\ORM\NoResultException;

class BookService
{
    public function __construct(
        private readonly BookRepository $bookRepository,
        private readonly BookCategoryRepository $bookCategoryRepository,
        private readonly ReviewRepository $reviewRepository,
        private readonly RatingService $ratingService,
    ) {
    }

    public function getBookByCategory(int $categoryId): BookListResponse
    {
        if (!$this->bookCategoryRepository->existsById($categoryId)) {
            throw new BookCategoryNotFoundException();
        }

        return new BookListResponse(array_map(
            fn (Book $book) => BookMapper::map($book, new BookListItem()),
            $this->bookRepository->findBookByCategoryId($categoryId),
        ));
    }

    /**
     * @throws NonUniqueResultException
     * @throws NoResultException
     */
    public function getBookById(int $id): BookDetails
    {
        $book = $this->bookRepository->getById($id);

        $reviews = $this->reviewRepository->countByBookId($id);

        $categories = $book->getCategories()
            ->map(
                fn (BookCategory $bookCategory) => (new BookCategoryModel(
                    $bookCategory->getId(), $bookCategory->getTitle(), $bookCategory->getSlug()
                ))
            )
        ;

        return BookMapper::map($book, new BookDetails())
            ->setRating($this->ratingService->calcReviewRatingFroBook($id, $reviews))
            ->setReviews($reviews)
            ->setFormats($this->mapFormats($book->getFormats()))
            ->setCategories($categories->toArray())
        ;
    }

    /**
     * @param Collection<BookToBookFormat> $formats
     * @return array
     */
    private function mapFormats(Collection $formats): array
    {
        return $formats
            ->map(fn (BookToBookFormat $formatJoin) => (new BookFormat())
                ->setId($formatJoin->getBookFormat()->getId())
                ->setTitle($formatJoin->getBookFormat()->getTitle())
                ->setDescription($formatJoin->getBookFormat()->getDescription())
                ->setComment($formatJoin->getBookFormat()->getComment())
                ->setPrice($formatJoin->getPrice())
                ->setDicountPercent($formatJoin->getDiscountPercent())
            )
            ->toArray();
    }
}
