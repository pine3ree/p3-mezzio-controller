<?php

/**
 * @see       https://github.com/pine3ree/p3-mezzio-controller for the canonical source repository
 * @copyright https://github.com/pine3ree/p3-mezzio-controller/blob/master/COPYRIGHT.md
 * @license   https://github.com/pine3ree/p3-mezzio-controller/blob/master/LICENSE.md New BSD License
 */

declare(strict_types=1);

namespace P3\Mezzio\Controller;

// Order imports by namespace specificity and inside namespace alphabetically
use Psr\Container\ContainerInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface;
use Mezzio\Exception\InvalidMiddlewareException;

use function is_array;
use function is_string;
use function explode;
use function strpos;

class ControllerMiddleware implements MiddlewareInterface
{
    /**
     * @var ContainerInterface
     */
    private $container;

    /**
     * @var string|object The controller FQCN or the controller instance
     */
    private $controller;

    /**
     * @var string The controller method name
     */
    private $method;

    public function __construct(
        ContainerInterface $container,
        $middleware
    ) {
        $this->container = $container;
        $this->resolve($middleware);
    }

    public function process(ServerRequestInterface $request, RequestHandlerInterface $handler): ResponseInterface
    {
        if (is_string($this->controller)) {
            $fqcn = $this->controller;
            if ($this->container->has($fqcn)) {
                $this->controller = $this->container->get($fqcn);
            } else {
                $this->controller = new $fqcn();
            }
        }

        return $this->controller->{$this->method}($request);
    }

    /**
     * Resolve the middleware definition into class/object and method
     *
     * @return void
     * @throws InvalidMiddlewareException for invalid controller/method pair
     */
    private function resolve($middleware)
    {
        if (is_string($middleware) && strpos($middleware, '::')) {
            $middleware = explode('::', $middleware);
        }

        if (!is_array($middleware) || !is_callable($middleware)) {
            throw new InvalidMiddlewareException(
                "A controller-middleware must be defined as a callable array "
                . "form [FQCN::class, 'method'] or a callable string form "
                . "'My\Fully\Qualified\ClassName::method'!"
            );
        }

        $this->controller = $middleware[0];
        $this->method     = $middleware[1];
    }
}
