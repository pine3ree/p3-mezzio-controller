<?php

/**
 * @see       https://github.com/pine3ree/p3-mezzio-controller for the canonical source repository
 * @copyright https://github.com/pine3ree/p3-mezzio-controller/blob/master/COPYRIGHT.md
 * @license   https://github.com/pine3ree/p3-mezzio-controller/blob/master/LICENSE.md New BSD License
 */

/**
 * @see       https://github.com/mezzio/mezzio for the canonical source repository
 * @copyright https://github.com/pine3ree/p3-mezzio-controller/blob/master/COPYRIGHT.md
 * @license   https://github.com/pine3ree/p3-mezzio-controller/blob/master/LICENSE.md New BSD License
 */

declare(strict_types=1);

namespace P3\Mezzio\Controller;

// Order imports by namespace specificity and inside namespace alphabetically
use Psr\Container\ContainerInterface;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface;
use Mezzio\Exception\InvalidMiddlewareException;
use Mezzio\MiddlewareFactory as MezzioMiddlewareFactory;
use Mezzio\MiddlewareContainer;
use P3\Mezzio\Controller\ControllerMiddleware;

use function is_array;
use function is_callable;
use function is_string;

/**
 * {@inheritDoc}
 *
 * Extends Mezzio's MiddlewareFactory allowing generic controller-like classes
 * to handle the request when provided in one of the following format:
 *
 * - [FQCN::class, 'publicMethodName']
 *
 * - "FQCN::publicMethodName"
 *
 * The class provides the following additional decorator/utility method:
 *
 * - controller() will decorate the callable controller passed to it using
 *   a ControllerMiddleware.
 */
class MiddlewareFactory extends MezzioMiddlewareFactory
{
    /**
     * @var MiddlewareContainer
     */
    private $container;

    /**
     * @var ContainerInterface
     */
    private $rootContainer;

    public function __construct(MiddlewareContainer $container, ContainerInterface $rootContainer)
    {
        parent::__construct($container);
        $this->rootContainer = $rootContainer;
    }

    /**
     * {@inheritDoc}
     * Intercepts the callable to handle the new controller case
     */
    public function prepare($middleware): MiddlewareInterface
    {
        if ($this->isController($middleware)) {
            return $this->controller($middleware);
        }

        return parent::prepare($middleware);
    }

    /**
     * Create a controller middleware based on a string/array callable middleware definition
     */
    public function controller($middleware): ControllerMiddleware
    {
        return new ControllerMiddleware($this->rootContainer, $middleware);
    }

    /**
     * Check if the callable middleware represents a class/object + method pair
     *
     * @param mixed $middleware
     */
    private function isController($middleware): bool
    {
        if (!is_callable($middleware)) {
            return false;
        }

        if (is_array($middleware)) {
            return true;
        }

        if (is_string($middleware) && strpos($middleware, '::')) {
            return true;
        }

        return false;
    }
}
