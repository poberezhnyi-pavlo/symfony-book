<?php

namespace App\Tests\Controller;

use App\Tests\AbstractControllerTestCase;
use Symfony\Component\HttpFoundation\Response;

final class SubscriberControllerTest extends AbstractControllerTestCase
{
    /**
     * @throws \Exception
     */
    public function testSubscribe(): void
    {
        $content = json_encode(['email' => 'email@email.com', 'agreed' => true], JSON_THROW_ON_ERROR);
        $this->client->request('POST', '/api/v1/subscribe', [], [], [], $content);

        self::assertResponseIsSuccessful();
    }

    /**
     * @throws \Exception
     */
    public function testSubscribeNotAgreed(): void
    {
        $content = json_encode(['email' => 'email@email.com'], JSON_THROW_ON_ERROR);
        $this->client->request('POST', '/api/v1/subscribe', [], [], [], $content);

        $responseContent = json_decode($this->client->getResponse()->getContent());

        self::assertResponseStatusCodeSame(Response::HTTP_BAD_REQUEST);
        self::assertJsonDocumentMatches($responseContent, [
            '$.details.violations' => self::countOf(1),
            '$.details.violations[0].field' => 'agreed',
            '$.details.violations[0].message' => 'This value should not be blank.',
        ]);
    }
}
