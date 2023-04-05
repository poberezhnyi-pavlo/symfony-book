<?php

namespace App\Tests\Service;

use App\Entity\Book;
use App\Exception\BookCategoryNotFoundException;
use App\Model\BookListItem;
use App\Model\BookListResponse;
use App\Repository\BookCategoryRepository;
use App\Repository\BookRepository;
use App\Repository\ReviewRepository;
use App\Service\BookService;
use App\Service\RatingService;
use App\Tests\AbstractTestCase;
use Doctrine\Common\Collections\ArrayCollection;
use PHPUnit\Framework\MockObject\Exception;

final class BookServiceTest extends AbstractTestCase
{
    private ReviewRepository $reviewRepository;
    private BookRepository $bookRepository;
    private BookCategoryRepository $bookCategoryRepository;
    private RatingService $ratingService;

    /**
     * @throws Exception
     */
    protected function setUp(): void
    {
        parent::setUp();

        $this->reviewRepository = $this->createMock(ReviewRepository::class);
        $this->bookRepository = $this->createMock(BookRepository::class);
        $this->bookCategoryRepository = $this->createMock(BookCategoryRepository::class);
        $this->ratingService = $this->createMock(RatingService::class);
    }

    public function testGetBookByCategoryNotFound(): void
    {
        $this->bookCategoryRepository
            ->expects($this->once())
            ->method('existsById')
            ->with(130)
            ->willReturn(false)
        ;

        $this->expectException(BookCategoryNotFoundException::class);

        $this->createBookService()->getBookByCategory(130);
    }

    public function testGetBookByCategory(): void
    {
        $this->bookRepository
            ->expects($this->once())
            ->method('findBookByCategoryId')
            ->with(130)
            ->willReturn([$this->createBookEntity()])
        ;

        $this->bookCategoryRepository
            ->expects($this->once())
            ->method('existsById')
            ->with(130)
            ->willReturn(true)
        ;

        $service = $this->createBookService();
        $expected = new BookListResponse([$this->createBookItemModel()]);

        $this->assertEquals($expected, $service->getBookByCategory(130));
    }

    private function createBookService(): BookService
    {
        return new BookService(
            $this->bookRepository,
            $this->bookCategoryRepository,
            $this->reviewRepository,
            $this->ratingService,
        );
    }

    private function createBookEntity(): Book
    {
        $book = (new Book())
            ->setTitle('Test Book')
            ->setSlug('test_book')
            ->setMeap(false)
            ->setIsbn('12345')
            ->setDescription('set Description')
            ->setAuthors(['author'])
            ->setImage('http://loc/image.png')
            ->setCategories(new ArrayCollection())
            ->setPublicationDate(new \DateTimeImmutable('2020-10-10'))
        ;

        $this->setEntityId($book, 123);

        return $book;
    }

    private function createBookItemModel(): BookListItem
    {
        return (new BookListItem())
            ->setId(123)
            ->setTitle('Test Book')
            ->setSlug('test_book')
            ->setMeap(false)
            ->setAuthors(['author'])
            ->setImage('http://loc/image.png')
            ->setPublicationDate((new \DateTimeImmutable('2020-10-10'))->getTimestamp())
        ;
    }
}
