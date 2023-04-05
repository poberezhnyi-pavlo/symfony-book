<?php

namespace App\Tests\Controller;

use App\Entity\Book;
use App\Entity\BookCategory;
use App\Entity\BookFormat;
use App\Entity\BookToBookFormat;
use App\Tests\AbstractControllerTestCase;
use Doctrine\Common\Collections\ArrayCollection;

final class BookControllerTest extends AbstractControllerTestCase
{
    public function testBooksByCategory(): void
    {
        $categoryId = $this->createCategories();

        $this->client->request('get', "/api/v1/category/{$categoryId}/books");

        $responseContent = json_decode($this->client->getResponse()->getContent(), true, 512, JSON_THROW_ON_ERROR);

        self::assertResponseIsSuccessful();
        self::assertJsonDocumentMatchesSchema($responseContent, [
            'type' => 'object',
            'required' => ['items'],
            'properties' => [
                'items' => [
                    'type' => 'array',
                    'items' => [
                        'type' => 'object',
                        'required' => ['id', 'title', 'slug', 'image', 'authors', 'meap', 'publicationDate'],
                        'properties' => [
                            'title' => ['type' => 'string'],
                            'slug' => ['type' => 'string'],
                            'id' => ['type' => 'integer'],
                            'image' => ['type' => 'string'],
                            'authors' => [
                                'type' => 'array',
                                'items' => ['type' => 'string'],
                            ],
                            'meap' => ['type' => 'boolean'],
                            'publicationDate' => ['type' => 'integer'],
                        ],
                    ],
                ],
            ],
        ]);
    }

    public function testBookById(): void
    {
        $bookId = $this->createBook();

        $this->client->request('get', "/api/v1/book/{$bookId}");

        $responseContent = json_decode($this->client->getResponse()->getContent(), true, 512, JSON_THROW_ON_ERROR);

        self::assertResponseIsSuccessful();
        self::assertJsonDocumentMatchesSchema($responseContent, [
            'type' => 'object',
            'required' => ['id', 'title', 'slug', 'image', 'authors', 'meap', 'publicationDate', 'rating', 'reviews', 'categories', 'formats'],
            'properties' => [
                'items' => [
                    'type' => 'array',
                    'items' => [
                        'type' => 'object',
                        'required' => ['id', 'title', 'slug', 'image', 'authors', 'meap', 'publicationDate'],
                        'properties' => [
                            'title' => ['type' => 'string'],
                            'slug' => ['type' => 'string'],
                            'id' => ['type' => 'integer'],
                            'image' => ['type' => 'string'],
                            'authors' => [
                                'type' => 'array',
                                'items' => ['type' => 'string'],
                            ],
                            'meap' => ['type' => 'boolean'],
                            'publicationDate' => ['type' => 'integer'],
                            'rating' => ['type' => 'number'],
                            'reviews' => ['type' => 'integer'],
                            'categories' => [
                                'type' => 'array',
                                'items' => [
                                    'type' => 'object',
                                    'required' => ['id', 'slug', 'title'],
                                    'properties' => [
                                        'title' => ['type' => 'string'],
                                        'slug' => ['type' => 'string'],
                                        'id' => ['type' => 'integer'],
                                    ],
                                ],
                            ],
                        ],
                    ],
                ],
            ],
        ]);
    }

    private function createCategories(): int
    {
        $bookCategory = (new BookCategory())
            ->setTitle('Devices')
            ->setSlug('devices');

        $this->em->persist($bookCategory);

        $this->em->persist(
            (new Book())
                ->setTitle('Test Book')
                ->setImage('http://loc/in.png')
                ->setSlug('test-book')
                ->setMeap(false)
                ->setIsbn('12345')
                ->setDescription('test Description')
                ->setAuthors(['author'])
                ->setCategories(new ArrayCollection([$bookCategory]))
                ->setPublicationDate(new \DateTimeImmutable())
        );

        $this->em->flush();

        return $bookCategory->getId();
    }

    private function createBook(): int
    {
        $bookCategory = (new BookCategory())
            ->setTitle('Devices')
            ->setSlug('devices');
        $this->em->persist($bookCategory);

        $format = (new BookFormat())
            ->setTitle('format')
            ->setComment('comment')
            ->setDescription('description');
        $this->em->persist($format);

        $book = (new Book())
            ->setTitle('Test Book')
            ->setImage('http://loc/in.png')
            ->setSlug('test-book')
            ->setMeap(false)
            ->setIsbn('12345')
            ->setDescription('test Description')
            ->setAuthors(['author'])
            ->setCategories(new ArrayCollection([$bookCategory]))
            ->setPublicationDate(new \DateTimeImmutable());
        $this->em->persist($book);

        $join = (new BookToBookFormat())
            ->setPrice(23.55)
            ->setDiscountPercent(15)
            ->setBookFormat($format)
            ->setBook($book);
        $this->em->persist($join);

        $this->em->flush();

        return $book->getId();
    }
}
