<?php

declare(strict_types=1);

namespace App\Service;

use App\Entity\Book;
use App\Entity\BookCategory;
use App\Entity\BookToBookFormat;
use App\Exception\BookCategoryNotFoundException;
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
    ) {
    }

    public function getBookByCategory(int $categoryId): BookListResponse
    {
        if (!$this->bookCategoryRepository->existsById($categoryId)) {
            throw new BookCategoryNotFoundException();
        }

        return new BookListResponse(array_map(
            [$this, 'map'],
            $this->bookRepository->findBookByCategoryId($categoryId),
        ));
    }

    private function map(Book $book): BookListItem
    {
        return (new BookListItem())
            ->setId($book->getId())
            ->setTitle($book->getTitle())
            ->setSlug($book->getSlug())
            ->setImage($book->getImage())
            ->setAuthors($book->getAuthors())
            ->setMeap($book->isMeap())
            ->setPublicationDate($book->getPublicationDate()->getTimestamp())
        ;
    }

    /**
     * @throws NonUniqueResultException
     * @throws NoResultException
     */
    public function getBookById(int $id): BookDetails
    {
        $book = $this->bookRepository->getById($id);

        $reviews = $this->reviewRepository->countByBookId($id);

        $rating = 0;

        if ($reviews > 0) {
            $rating = $this->reviewRepository->getBookTotalRatingSum($id) / $reviews;
        }

        $categories = $book->getCategories()
            ->map(
                fn (BookCategory $bookCategory) => (new BookCategoryModel(
                    $bookCategory->getId(), $bookCategory->getTitle(), $bookCategory->getSlug()
                ))
            )
        ;

        return (new BookDetails())
            ->setId($book->getId())
            ->setTitle($book->getTitle())
            ->setSlug($book->getSlug())
            ->setSlug($book->getSlug())
            ->setImage($book->getImage())
            ->setAuthors($book->getAuthors())
            ->setMeap($book->isMeap())
            ->setPublicationDate($book->getPublicationDate()->getTimestamp())
            ->setRating($rating)
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
