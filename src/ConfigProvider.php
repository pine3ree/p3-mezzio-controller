<?php

/**
 * @see       https://github.com/pine3ree/p3-mezzio-controller for the canonical source repository
 * @copyright https://github.com/pine3ree/p3-mezzio-controller/blob/master/COPYRIGHT.md
 * @license   https://github.com/pine3ree/p3-mezzio-controller/blob/master/LICENSE.md New BSD License
 */

declare(strict_types=1);

namespace P3\Mezzio\Controller;

// Order imports by namespace specificity and inside namespace alphabetically
use Mezzio\MiddlewareFactory as MezzioMiddlewareFactory;
use P3\Mezzio\Controller\MiddlewareFactory;
use P3\Mezzio\Controller\MiddlewareFactoryFactory;

/**
 * The configuration provider for the module
 *
 * @see https://docs.laminas.dev/laminas-component-installer/
 */
class ConfigProvider
{
    /**
     * Returns the configuration array
     */
    public function __invoke(): array
    {
        return [
            'dependencies' => $this->getDependencies(),
        ];
    }

    /**
     * Returns the container dependencies
     */
    public function getDependencies(): array
    {
        return [
            'aliases' => [
                MezzioMiddlewareFactory::class => MiddlewareFactory::class,
            ],
            'factories'  => [
                MiddlewareFactory::class => MiddlewareFactoryFactory::class,
            ],
        ];
    }
}
