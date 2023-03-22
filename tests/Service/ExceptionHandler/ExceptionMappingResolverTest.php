<?php

namespace App\Tests\Service\ExceptionHandler;

use App\Service\ExceptionHandler\ExceptionMappingResolver;
use App\Tests\AbstractTestCase;

final class ExceptionMappingResolverTest extends AbstractTestCase
{
    public function testThrowsExceptionOnEmptyCode(): void
    {
        $this->expectException(\InvalidArgumentException::class);
        new ExceptionMappingResolver(['someClass' => ['hidden' => false]]);
    }

    public function testResolveReturnsNullWhenNoFound(): void
    {
        $resolver = new ExceptionMappingResolver([]);

        $this->assertNull($resolver->resolve(\InvalidArgumentException::class));
    }

    public function testResolvesClassItSelf(): void
    {
        $resolver = new ExceptionMappingResolver([\InvalidArgumentException::class => ['code' => 400]]);
        $mapping = $resolver->resolve(\InvalidArgumentException::class);

        $this->assertEquals(400, $mapping->getCode());
        $this->assertFalse($mapping->isLoggable());
        $this->assertTrue($mapping->isHidden());
    }

    public function testResolvesSubClass(): void
    {
        $resolver = new ExceptionMappingResolver([\LogicException::class => ['code' => 500]]);
        $mapping = $resolver->resolve(\InvalidArgumentException::class);

        $this->assertEquals(500, $mapping->getCode());
    }

    public function testResolvesHidden(): void
    {
        $resolver = new ExceptionMappingResolver([\LogicException::class => ['code' => 500, 'hidden' => false]]);
        $mapping = $resolver->resolve(\InvalidArgumentException::class);

        $this->assertFalse($mapping->isHidden());
    }

    public function testResolvesLoggable(): void
    {
        $resolver = new ExceptionMappingResolver([\LogicException::class => ['code' => 500, 'loggable' => true]]);
        $mapping = $resolver->resolve(\InvalidArgumentException::class);

        $this->assertTrue($mapping->isLoggable());
    }
}
