<?php

namespace App\Tests\Repository;

use App\Entity\Book;
use App\Entity\BookCategory;
use App\Repository\BookRepository;
use App\Tests\AbstractRepositoryTestCase;
use Doctrine\Common\Collections\ArrayCollection;

final class BookRepositoryTest extends AbstractRepositoryTestCase
{
    private BookRepository $bookRepository;
    protected function setUp(): void
    {
        parent::setUp();

        $this->bookRepository = $this->getRepositoryForEntity(Book::class);
    }

    public function testFindBookByCategoryId(): void
    {
        $deviceCategory = (new BookCategory())->setTitle('Devices')->setSlug('devices');
        $this->em->persist($deviceCategory);

        for ($i = 0; $i < 5; $i++) {
            $book = $this->createBook('device-'.$i, $deviceCategory);
            $this->em->persist($book);
        }

        $this->em->flush();

        $this->assertCount(5, $this->bookRepository->findBookByCategoryId($deviceCategory->getId()));
    }

    private function createBook(string $string, BookCategory $category): Book
    {
        return (new Book())
            ->setPublicationDate(new \DateTimeImmutable())
            ->setTitle($string)
            ->setAuthors(['author'])
            ->setMeap(false)
            ->setCategories(new ArrayCollection([$category]))
            ->setSlug($string)
            ->setImage("http://loc/{$string}.png")
        ;
    }
}
