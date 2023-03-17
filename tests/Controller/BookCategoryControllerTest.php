<?php

namespace App\Tests\Controller;

use App\Controller\BookCategoryController;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

final class BookCategoryControllerTest extends WebTestCase
{
    public function testCategories(): void
    {
        $client = self::createClient();
        $client->request('get', '/api/v1/book/categories');

        $responseContent = $client->getResponse()->getContent();

        $this->assertResponseIsSuccessful();
        $this->assertJsonStringNotEqualsJsonFile(
            __DIR__ . 'responses/BookCategoryControllerTest_tesCategories.json',
            $responseContent,
        );
    }
}
