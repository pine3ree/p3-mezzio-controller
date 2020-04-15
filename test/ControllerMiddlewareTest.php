<?php

/**
 * @see       https://github.com/pine3ree/p3-mezzio-controller for the canonical source repository
 * @copyright https://github.com/pine3ree/p3-mezzio-controller/blob/master/COPYRIGHT.md
 * @license   https://github.com/pine3ree/p3-mezzio-controller/blob/master/LICENSE.md New BSD License
 */

declare(strict_types=1);

namespace P3Test\Mezzio\Controller;

use TypeError;
use Psr\Container\ContainerInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\RequestHandlerInterface;
use PHPUnit\Framework\TestCase;
use Prophecy\Prophecy\ObjectProphecy;
use Mezzio\Exception\InvalidMiddlewareException;
use Mezzio\MiddlewareContainer;
use P3\Mezzio\Controller\ControllerMiddleware;
use P3Test\Mezzio\Controller\TestAsset\Foo;

class ControllerMiddlewareTest extends TestCase
{
    /** @var MiddlewareContainer|ObjectProphecy */
    private $container;

    /** @var ServerRequestInterface */
    private $request;

    /** @var RequestHandlerInterface */
    private $handler;

    /** @var ResponseInterface */
    private $response;

    public function setUp()
    {
        $this->container = $this->prophesize(ContainerInterface::class);
        $this->request   = $this->prophesize(ServerRequestInterface::class);
        $this->handler   = $this->prophesize(RequestHandlerInterface::class);
        $this->response  = $this->prophesize(ResponseInterface::class);
    }

    public function buildControllerMiddleware($middleware)
    {
        return new ControllerMiddleware($this->container->reveal(), $middleware);
    }

    public function testProcessesControllerIntanceFromArrayDefinition()
    {
        $method = 'bar';

        /** @phpstan-ignore Custom.ProhibitedFunctions */
        $controller = $this->getMockBuilder(FooController::class)->setMethods([$method])->getMock();
        $controller->method($method)
            ->with($this->request->reveal())
            ->willReturn($this->response->reveal());

        $callable = [$controller, $method];

        $controllerMiddleware = $this->buildControllerMiddleware($callable);

        self::assertSame(
            $controller->{$method}($this->request->reveal()),
            $controllerMiddleware->process($this->request->reveal(), $this->handler->reveal())
        );
    }

    public function testProcessesControllerPulledFromContainerFromArrayDefinition()
    {
        self::assertInstanceOf(ServerRequestInterface::class, $this->request->reveal());
        $fqcn = Foo::class;
        $method = 'bar';
        $callable = [$fqcn, $method];

        $controller = $this->getMockBuilder($fqcn)->setMethods([$method])->getMock();
        $controller->method($method)
            ->with($this->request->reveal())
            ->willReturn($this->response->reveal());

        $this->container->has($fqcn)->willReturn(true);
        $this->container->get($fqcn)->willReturn($controller);

        $controllerMiddleware = $this->buildControllerMiddleware($callable);

        self::assertSame(
            $controller->{$method}($this->request->reveal()),
            $controllerMiddleware->process($this->request->reveal(), $this->handler->reveal())
        );
    }

    public function testProcessesControllerPulledFromContainerFromStringDefinition()
    {
        $fqcn = Foo::class;
        $method = 'bar';
        $callable = "{$fqcn}::{$method}";

        $controller = $this->getMockBuilder($fqcn)->setMethods([$method])->getMock();
        $controller->method($method)
            ->with($this->request->reveal())
            ->willReturn($this->response->reveal());

        $this->container->has($fqcn)->willReturn(true);
        $this->container->get($fqcn)->willReturn($controller);

        $controllerMiddleware = $this->buildControllerMiddleware($callable);

        self::assertSame(
            $controller->{$method}($this->request->reveal()),
            $controllerMiddleware->process($this->request->reveal(), $this->handler->reveal())
        );
    }

    public function testProcessesNewControllerIntanceFromStringDefinition()
    {
        $fqcn = Foo::class;
        $method = 'bar';
        $callable = "{$fqcn}::{$method}";

        $controller = $this->getMockBuilder($fqcn)->setMethods([$method])->getMock();
        $controller->method($method)
            ->with($this->request->reveal())
            ->willReturn($this->response->reveal());

        $this->container->has($fqcn)->willReturn(false);

        $controllerMiddleware = $this->buildControllerMiddleware($callable);

        self::assertInstanceOf(
            ResponseInterface::class,
            $controllerMiddleware->process($this->request->reveal(), $this->handler->reveal())
        );
    }

    public function testThrowsInvalidMiddlewareExceptionForUnsupportedParameterType()
    {
        $this->expectException(InvalidMiddlewareException::class);
        $controllerMiddleware = $this->buildControllerMiddleware(123);
    }

    public function testThrowsInvalidMiddlewareExceptionsForUnsupportedCallableString()
    {
        $this->expectException(InvalidMiddlewareException::class);
        $controllerMiddleware = $this->buildControllerMiddleware('strlen');
    }

    public function testThrowsInvalidMiddlewareExceptionsForNonCallableArray()
    {
        $this->expectException(InvalidMiddlewareException::class);
        $controllerMiddleware = $this->buildControllerMiddleware(['A', 2]);
    }
}
