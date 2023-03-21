<?php

namespace App\Tests\Controller;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class BookControllerTest extends WebTestCase
{
    public function testBooksByCategory(): void
    {
        $client = self::createClient();
        $client->request('get', '/api/v1/category/4/books');

        $responseContent = $client->getResponse()->getContent();

        self::assertResponseIsSuccessful();
        $this->assertJsonStringNotEqualsJsonFile(
            __DIR__.'/responses/BookControllerTest_testBooks.json',
            $responseContent,
        );
    }
}
