<?php

namespace App\Tests\ArgumentResolver;

use App\ArgumentResolver\RequestBodyArgumentResolver;
use App\Attribute\RequestBody;
use App\Exception\RequestBodyConvertException;
use App\Exception\ValidationException;
use App\Tests\AbstractTestCase;
use PHPUnit\Framework\MockObject\Exception;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\ControllerMetadata\ArgumentMetadata;
use Symfony\Component\Serializer\Encoder\JsonEncoder;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Component\Validator\ConstraintViolation;
use Symfony\Component\Validator\ConstraintViolationList;
use Symfony\Component\Validator\Validator\ValidatorInterface;

final class RequestBodyArgumentResolverTest extends AbstractTestCase
{
    private SerializerInterface $serializer;
    private ValidatorInterface $validator;

    /**
     * @throws Exception
     */
    protected function setUp(): void
    {
        parent::setUp();

        $this->serializer = $this->createMock(SerializerInterface::class);
        $this->validator = $this->createMock(ValidatorInterface::class);
    }

    public function testSupports(): void
    {
        $meta = new ArgumentMetadata(
            'some',
            null,
            false,
            false,
            null,
            false,
            [new RequestBody()]
        );

        $this->assertTrue($this->createResolver()->supports(new Request(), $meta));
    }

    public function testNorSupports(): void
    {
        $meta = new ArgumentMetadata(
            'some',
            null,
            false,
            false,
            null,
        );

        $this->assertFalse($this->createResolver()->supports(new Request(), $meta));
    }

    public function testResolveThrowsWhenDeserialize(): void
    {
        $this->expectException(RequestBodyConvertException::class);

        $request = new Request([], [], [], [], [], [], 'testing_content');
        $meta = new ArgumentMetadata(
            'some',
            \stdClass::class,
            false,
            false,
            null,
        );

        $this->serializer
            ->expects($this->once())
            ->method('deserialize')
            ->with('testing_content', \stdClass::class, JsonEncoder::FORMAT)
            ->willThrowException(new \Exception());

        $this->createResolver()->resolve($request, $meta)->next();
    }

    public function testResolveThrowsWhenValidationFails(): void
    {
        $this->expectException(ValidationException::class);

        $body = ['test' => true];
        $encodeBody = json_encode($body);

        $request = new Request([], [], [], [], [], [], $encodeBody);
        $meta = new ArgumentMetadata(
            'some',
            \stdClass::class,
            false,
            false,
            null,
            false,
            [new RequestBody()]
        );

        $this->serializer
            ->expects($this->once())
            ->method('deserialize')
            ->with($encodeBody, \stdClass::class, JsonEncoder::FORMAT)
            ->willReturn($body);

        $this->validator
            ->expects($this->once())
            ->method('validate')
            ->with($body)
            ->willReturn(new ConstraintViolationList([
                new ConstraintViolation(
                    'error',
                    null,
                    [],
                    null,
                    'something',
                    null,
                ),
            ]));

        $this->createResolver()->resolve($request, $meta)->next();
    }

    public function testResolve(): void
    {
        $body = ['test' => true];
        $encodeBody = json_encode($body);

        $request = new Request([], [], [], [], [], [], $encodeBody);
        $meta = new ArgumentMetadata(
            'some',
            \stdClass::class,
            false,
            false,
            null,
            false,
            [new RequestBody()]
        );

        $this->serializer
            ->expects($this->once())
            ->method('deserialize')
            ->with($encodeBody, \stdClass::class, JsonEncoder::FORMAT)
            ->willReturn($body);

        $this->validator
            ->expects($this->once())
            ->method('validate')
            ->with($body)
            ->willReturn(new ConstraintViolationList([]));

        $actual = $this->createResolver()->resolve($request, $meta);

        $this->assertEquals($body, $actual->current());
    }

    private function createResolver(): RequestBodyArgumentResolver
    {
        return new RequestBodyArgumentResolver($this->serializer, $this->validator);
    }
}
