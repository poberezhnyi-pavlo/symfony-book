<?php

namespace App\Tests\Controller;

use App\Entity\Book;
use App\Entity\BookCategory;
use App\Tests\AbstractControllerTestCase;
use DateTime;
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
                            'title' => [
                                'type' => 'string',
                            ],
                            'slug' => [
                                'type' => 'string',
                            ],
                            'id' => [
                                'type' => 'integer',
                            ],
                            'image' => [
                                'type' => 'string'
                            ],
                            'authors' => [
                                'type' => 'array',
                                'items' => [
                                    'type' => 'string',
                                ],
                            ],
                            'meap' => [
                                'type' => 'boolean'
                            ],
                            'publicationDate' => [
                                'type' => 'integer'
                            ],
                        ],
                    ]
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
                ->setAuthors(['author'])
                ->setCategories(new ArrayCollection([$bookCategory]))
                ->setPublicationDate(new DateTime())
        );

        $this->em->flush();

        return $bookCategory->getId();
    }
}
