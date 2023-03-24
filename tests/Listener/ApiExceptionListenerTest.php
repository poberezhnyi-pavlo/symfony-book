<?php

namespace App\Tests\Listener;

use App\Listener\ApiExceptionListener;
use App\Model\ErrorDebugDetail;
use App\Model\ErrorResponse;
use App\Service\ExceptionHandler\ExceptionMapping;
use App\Service\ExceptionHandler\ExceptionMappingResolver;
use App\Tests\AbstractTestCase;
use PHPUnit\Framework\MockObject\Exception;
use Psr\Log\LoggerInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Event\ExceptionEvent;
use Symfony\Component\HttpKernel\HttpKernelInterface;
use Symfony\Component\Serializer\Encoder\JsonEncoder;
use Symfony\Component\Serializer\SerializerInterface;

final class ApiExceptionListenerTest extends AbstractTestCase
{
    private ExceptionMappingResolver $resolver;
    private LoggerInterface $logger;
    private SerializerInterface $serializer;

    /**
     * @throws Exception
     */
    protected function setUp(): void
    {
        parent::setUp();

        $this->resolver = $this->createMock(ExceptionMappingResolver::class);
        $this->logger = $this->createMock(LoggerInterface::class);
        $this->serializer = $this->createMock(SerializerInterface::class);
    }

    /**
     * @throws \Exception
     */
    public function testNon500MappingWithHiddenMessage(): void
    {
        $mapping = ExceptionMapping::fromCode(Response::HTTP_NOT_FOUND);
        $responseMessage = Response::$statusTexts[$mapping->getCode()];
        $responseBody = json_encode(['error' => $responseMessage], JSON_THROW_ON_ERROR);

        $this
            ->resolver
            ->expects($this->once())
            ->method('resolve')
            ->with(\InvalidArgumentException::class)
            ->willReturn($mapping);

        $this
            ->serializer
            ->expects($this->once())
            ->method('serialize')
            ->with(new ErrorResponse($responseMessage), JsonEncoder::FORMAT)
            ->willReturn($responseBody)
        ;

        $event = $this->createEvent(new \InvalidArgumentException('test'));

        $this->runListener($event);

        $response = $event->getResponse();

        $this->assertResponse(Response::HTTP_NOT_FOUND, $responseBody, $response);
    }

    /**
     * @throws \Exception
     */
    public function testNon500MappingWithPublicMessage(): void
    {
        $mapping = new ExceptionMapping(Response::HTTP_NOT_FOUND, false, false);
        $responseMessage = 'test';
        $responseBody = json_encode(['error' => $responseMessage], JSON_THROW_ON_ERROR);

        $this
            ->resolver
            ->expects($this->once())
            ->method('resolve')
            ->with(\InvalidArgumentException::class)
            ->willReturn($mapping);

        $this
            ->serializer
            ->expects($this->once())
            ->method('serialize')
            ->with(new ErrorResponse($responseMessage), JsonEncoder::FORMAT)
            ->willReturn($responseBody)
        ;

        $event = $this->createEvent(new \InvalidArgumentException('test'));

        $this->runListener($event);

        $response = $event->getResponse();

        $this->assertResponse(Response::HTTP_NOT_FOUND, $responseBody, $response);
    }

    /**
     * @throws \Exception
     */
    public function testNon500LoggableMappingTrigger(): void
    {
        $mapping = new ExceptionMapping(Response::HTTP_NOT_FOUND, false, true);
        $responseMessage = 'test';
        $responseBody = json_encode(['error' => $responseMessage], JSON_THROW_ON_ERROR);

        $this
            ->resolver
            ->expects($this->once())
            ->method('resolve')
            ->with(\InvalidArgumentException::class)
            ->willReturn($mapping);

        $this
            ->serializer
            ->expects($this->once())
            ->method('serialize')
            ->with(new ErrorResponse($responseMessage), JsonEncoder::FORMAT)
            ->willReturn($responseBody)
        ;

        $this
            ->logger
            ->expects($this->once())
            ->method('error')
        ;

        $event = $this->createEvent(new \InvalidArgumentException('test'));

        $this->runListener($event);

        $response = $event->getResponse();

        $this->assertResponse(Response::HTTP_NOT_FOUND, $responseBody, $response);
    }

    /**
     * @throws \Exception
     */
    public function test500SsLoggable(): void
    {
        $mapping = ExceptionMapping::fromCode(Response::HTTP_GATEWAY_TIMEOUT);
        $responseMessage = Response::$statusTexts[$mapping->getCode()];
        $responseBody = json_encode(['error' => $responseMessage], JSON_THROW_ON_ERROR);

        $this
            ->resolver
            ->expects($this->once())
            ->method('resolve')
            ->with(\InvalidArgumentException::class)
            ->willReturn($mapping);

        $this
            ->serializer
            ->expects($this->once())
            ->method('serialize')
            ->with(new ErrorResponse($responseMessage), JsonEncoder::FORMAT)
            ->willReturn($responseBody)
        ;

        $this
            ->logger
            ->expects($this->once())
            ->method('error')
            ->with('error message', $this->anything())
        ;

        $event = $this->createEvent(new \InvalidArgumentException('error message'));

        $this->runListener($event);

        $response = $event->getResponse();

        $this->assertResponse(Response::HTTP_GATEWAY_TIMEOUT, $responseBody, $response);
    }

    /**
     * @throws \Exception
     */
    public function test500IsDefaultWithMappingNotFound(): void
    {
        $responseMessage = Response::$statusTexts[Response::HTTP_INTERNAL_SERVER_ERROR];
        $responseBody = json_encode(['error' => $responseMessage], JSON_THROW_ON_ERROR);

        $this
            ->resolver
            ->expects($this->once())
            ->method('resolve')
            ->with(\InvalidArgumentException::class)
            ->willReturn(null);

        $this
            ->serializer
            ->expects($this->once())
            ->method('serialize')
            ->with(new ErrorResponse($responseMessage), JsonEncoder::FORMAT)
            ->willReturn($responseBody)
        ;

        $this
            ->logger
            ->expects($this->once())
            ->method('error')
            ->with('error message', $this->anything())
        ;

        $event = $this->createEvent(new \InvalidArgumentException('error message'));

        $this->runListener($event);

        $response = $event->getResponse();

        $this->assertResponse(Response::HTTP_INTERNAL_SERVER_ERROR, $responseBody, $response);
    }

    /**
     * @throws \Exception
     */
    public function testShowTraceWhenDebug(): void
    {
        $mapping = ExceptionMapping::fromCode(Response::HTTP_NOT_FOUND);
        $responseMessage = Response::$statusTexts[$mapping->getCode()];
        $responseBody = json_encode(['error' => $responseMessage, 'trace' => 'something'], JSON_THROW_ON_ERROR);

        $this
            ->resolver
            ->expects($this->once())
            ->method('resolve')
            ->with(\InvalidArgumentException::class)
            ->willReturn($mapping);

        $this
            ->serializer
            ->expects($this->once())
            ->method('serialize')
            ->with(
                $this->callback(function (ErrorResponse $response) use ($responseMessage) {
                    /** @var ErrorDebugDetail|object $details */
                    $details = $response->getDetails();

                    return $response->getMessage() === $responseMessage
                        && $details instanceof ErrorDebugDetail
                        && !empty($details->getTrace())
                    ;
                }),
                JsonEncoder::FORMAT
            )
            ->willReturn($responseBody)
        ;

        $event = $this->createEvent(new \InvalidArgumentException('error message'));

        $this->runListener($event, true);

        $response = $event->getResponse();

        $this->assertResponse(Response::HTTP_NOT_FOUND, $responseBody, $response);
    }

    private function runListener(ExceptionEvent $event, bool $isDebug = false): void
    {
        (new ApiExceptionListener($this->resolver, $this->logger, $this->serializer, $isDebug))($event);
    }

    private function createEvent(\InvalidArgumentException $param): ExceptionEvent
    {
        return new ExceptionEvent(
            $this->createTestKernel(),
            new Request(),
            HttpKernelInterface::MAIN_REQUEST,
            $param
        );
    }

    private function createTestKernel(): HttpKernelInterface
    {
        return new class() implements HttpKernelInterface {
            public function handle(Request $request, int $type = self::MAIN_REQUEST, bool $catch = true): Response
            {
                return new Response('test');
            }
        };
    }

    private function assertResponse(int $expectedStatusCode, string $expectedBody, Response $response): void
    {
        $this->assertEquals($expectedStatusCode, $response->getStatusCode());
        $this->assertInstanceOf(JsonResponse::class, $response);
        $this->assertJsonStringEqualsJsonString($expectedBody, $response->getContent());
    }
}
