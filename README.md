# p3-mezzio-controller

A middleware wrapper for controller-like classes for mezzio/mezzio

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

These definition works only on the condition that if the controller class exists
and the target method is public.

The target method signature can be one of the following:

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