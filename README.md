# p3-mezzio-controller

[![Build Status](https://travis-ci.org/pine3ree/p3-mezzio-controller.svg?branch=master)](https://travis-ci.org/pine3ree/p3-mezzio-controller)

*A middleware wrapper for controller-like classes for mezzio/mezzio*

####STATUS:
WIP!

## Installation

You can install this library using Composer (with "minimum-stability": "dev"):

```bash
$ composer require pine3ree/p3-pdo
```

## Documentation

You can now define 2 new types of route handled by generic-class controller methods:

Define a route using the callable-string format:
```php
$app->get('/home/kitchen', 'App\Controller\Home::kitchen', 'home/kitchen');
```

Define a route using the callable-array format:
```php
use App\Controller\Home;
//...
$app->get('/home/bedroom', [Home::class, 'bedroom'], 'home/bedroom');
```

Define routes using a configuration file:

```php
// config/autoload/routes.global.php

use App\Controller\Home;
use App\Middleware\BeforeControllerMiddleware;
use App\Middleware\BeforeControllerMiddleware;

return [
    'routes' => [
        'home/kitchen' => [
            'path' => '/home/kitchen',
            'middleware' => 'App\Controller\Home::kitchen',
        ],
        'home/bedroom' => [
            'path' => '/home/kitchen',
            'middleware' => [Home::class, 'bedroom'],
        ],
        'home/living-room' => [
            'path' => '/home/living-room',
            'middleware' => [
                BeforeControllerMiddleware::class,
                [Home::class, 'livingRoom'],
                AfterControllerMiddleware::class,
            ],
        ],
    ],
];

```



These definitions works only on the condition that if the controller class exists
and the target method is public.

As of now the target controller-method signature may be one of the following:

```php
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Message\ResponseInterface;

class Home
{
    public function kitchen(ServerRequestInterface $request): ResponseInterface
    {
        // return a response
    }

    public function bedroom(): ResponseInterface
    {
        // return a response
    }
}
```

The controller class may be a simple contructor-less class. In most of cases it
will have dependencies so it muste me defined in the container configuration along
with its factory.

####TODO:

- Add strategy to pass request attributes as arguments into the target controller-method
- Add strategy to allow null, string, array return values from the controller-methods
  and build appropriate response based on  the return type

