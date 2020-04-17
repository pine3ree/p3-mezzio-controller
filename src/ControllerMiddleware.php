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
use P3\Mezzio\Controller\Exception\InvalidControllerException;

use function count;
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
     * @var callable The controller FQCN or the controller instance
     */
    private $controller;

    /**
     * @var string The controller method name
     */
    private $method;

    /**
     * @param array|string $controller A class/object-method string/array expression
     */
    public function __construct(
        ContainerInterface $container,
        $controller
    ) {
        $this->container = $container;
        $this->resolve($controller);
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
     * Resolve the middleware definition into class/object and method array
     *
     * @param array|string $controller
     * @throws InvalidControllerException for invalid controller/method combination
     */
    private function resolve($controller): void
    {
        if (is_string($controller) && 0 < strpos($controller, '::')) {
            $controller = explode('::', $controller);
        }

        if (!is_array($controller)
            || count($controller) !== 2
            || !is_callable($controller)
        ) {
            throw new InvalidControllerException(
                "A controller must be defined as a callable array form"
                . " [FQCN::class, 'method'] or a callable string form"
                . " 'My\Fully\Qualified\ClassName::method'!"
            );
        }

        $this->controller = $controller[0];
        $this->method     = $controller[1];
    }
}
