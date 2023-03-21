<?php

namespace App\Tests\Service;

use App\Entity\Book;
use App\Entity\BookCategory;
use App\Exceptopn\BookCategoryNotFoundException;
use App\Model\BookListItem;
use App\Model\BookListResponse;
use App\Repository\BookCategoryRepository;
use App\Repository\BookRepository;
use App\Service\BookService;
use DateTime;
use Doctrine\Common\Collections\ArrayCollection;
use PHPUnit\Framework\TestCase;

class BookServiceTest extends TestCase
{
    public function testGetBookByCategoryNotFound(): void
    {
        $bookRepository = $this->createMock(BookRepository::class);
        $bookCategoryRepository = $this->createMock(BookCategoryRepository::class);
        $bookCategoryRepository
            ->expects($this->once())
            ->method('find')
            ->with(130)
            ->willThrowException(new BookCategoryNotFoundException())
        ;

        $this->expectException(BookCategoryNotFoundException::class);

        (new BookService($bookRepository, $bookCategoryRepository))->getBookByCategory(130);
    }

    public function testGetBookByCategory(): void
    {
        $bookRepository = $this->createMock(BookRepository::class);
        $bookRepository
            ->expects($this->once())
            ->method('findBookByCategoryId')
            ->with(130)
            ->willReturn([$this->createBookEntity()])
        ;

        $bookCategoryRepository = $this->createMock(BookCategoryRepository::class);
        $bookCategoryRepository
            ->expects($this->once())
            ->method('find')
            ->with(130)
            ->willReturn(new BookCategory())
        ;

        $service = new BookService($bookRepository, $bookCategoryRepository);
        $expected = new BookListResponse([$this->createBookItemModel()]);

        $this->assertEquals($expected, $service->getBookByCategory(130));
    }

    private function createBookEntity(): Book
    {
        return (new Book())
            ->setId(123)
            ->setTitle('Test Book')
            ->setSlug('test_book')
            ->setMeap(false)
            ->setAuthors(['author'])
            ->setImage('http://loc/image.png')
            ->setCategories(new ArrayCollection())
            ->setPublicationDate(new DateTime('2020-10-10'))
        ;
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
            ->setPublicationDate((new DateTime('2020-10-10'))->getTimestamp())
        ;
    }
}
