<?php

namespace App\Tests\Controller;

use App\Entity\BookCategory;
use App\Tests\AbstractControllerTestCase;
use Doctrine\ORM\Exception\ORMException;
use Doctrine\ORM\OptimisticLockException;

final class BookCategoryControllerTest extends AbstractControllerTestCase
{
    /**
     * @throws OptimisticLockException
     * @throws ORMException
     * @throws \JsonException
     */
    public function testCategories(): void
    {
        $this->em->persist((new BookCategory())->setTitle('Devices')->setSlug('devices'));
        $this->em->flush();

        $this->client->request('get', '/api/v1/book/categories');

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
                        'required' => ['id', 'title', 'slug'],
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
                        ]
                    ]
                ]
            ]
        ]);
    }
}
