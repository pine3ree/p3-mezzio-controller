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
use Mezzio\MiddlewareContainer;
use P3\Mezzio\Controller\MiddlewareFactory;

class MiddlewareFactoryFactory
{
    public function __invoke(ContainerInterface $container): MiddlewareFactory
    {
        return new MiddlewareFactory(
            $container->get(MiddlewareContainer::class),
            $container
        );
    }
}
