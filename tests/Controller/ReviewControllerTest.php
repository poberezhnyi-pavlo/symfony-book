<?php

namespace App\Tests\Controller;

use App\Entity\Book;
use App\Entity\Review;
use App\Tests\AbstractControllerTestCase;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Exception\ORMException;
use Doctrine\ORM\OptimisticLockException;

final class ReviewControllerTest extends AbstractControllerTestCase
{
    /**
     * @throws OptimisticLockException
     * @throws ORMException|\Exception
     */
    public function testReviews(): void
    {
        $book = $this->createBook();
        $this->createReview($book);

        $this->em->flush();

        $this->client->request('GET', '/api/v1/book/'.$book->getId().'/reviews');

        $responseContent = json_decode($this->client->getResponse()->getContent(), true, 512, JSON_THROW_ON_ERROR);

        self::assertResponseIsSuccessful();
        self::assertJsonDocumentMatchesSchema($responseContent, [
            'type' => 'object',
            'required' => ['items', 'rating', 'page', 'pages', 'perPage', 'total'],
            'properties' => [
                'rating' => ['type' => 'number'],
                'page' => ['type' => 'integer'],
                'pages' => ['type' => 'integer'],
                'perPage' => ['type' => 'integer'],
                'total' => ['type' => 'integer'],
                'items' => [
                    'type' => 'array',
                    'items' => [
                        'type' => 'object',
                        'required' => ['id', 'content', 'authors', 'rating', 'createdAt'],
                        'properties' => [
                            'id' => ['type' => 'integer'],
                            'content' => ['type' => 'string'],
                            'author' => ['type' => 'string'],
                            'rating' => ['type' => 'integer'],
                            'createdAt' => ['type' => 'integer'],
                        ],
                    ],
                ],
            ],
        ]);
    }

    /**
     * @throws ORMException
     */
    private function createBook(): Book
    {
        $book = (new Book())
            ->setTitle('Test Book')
            ->setImage('http://loc/in.png')
            ->setSlug('test-book')
            ->setMeap(false)
            ->setIsbn('12345')
            ->setDescription('test Description')
            ->setAuthors(['author'])
            ->setCategories(new ArrayCollection([]))
            ->setPublicationDate(new \DateTimeImmutable());

        $this->em->persist($book);

        return $book;
    }

    /**
     * @throws ORMException
     */
    private function createReview(Book $book): void
    {
        $this->em
            ->persist(
                (new Review())
                    ->setAuthors('tester')
                    ->setContent('test content')
                    ->setCreatedAt(new \DateTimeImmutable())
                    ->setRating(5)
                    ->setBook($book)
            );
    }
}
