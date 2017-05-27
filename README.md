# Mddleware dispatcher

[![Build Status](https://img.shields.io/travis/slince/middleware/master.svg?style=flat-square)](https://travis-ci.org/slince/middleware)
[![Coverage Status](https://img.shields.io/codecov/c/github/slince/middleware.svg?style=flat-square)](https://codecov.io/github/slince/middleware)
[![Latest Stable Version](https://img.shields.io/packagist/v/slince/middleware.svg?style=flat-square&label=stable)](https://packagist.org/packages/slince/middleware)
[![Scrutinizer](https://img.shields.io/scrutinizer/g/slince/middleware.svg?style=flat-square)](https://scrutinizer-ci.com/g/slince/middleware/?branch=master)

The package is a PSR-15 middleware dispatcher for PSR-7 request message that help to create middlewares and dispatch.

## Installation

Install via composer

```bash
composer require slince/middleware
```

## Quick example

```php
$queue =  new Slince\Middleware\MiddlewareQueue([$middleware1, $middleware2]);

$response = $queue->process(Zend\Diactoros\ServerRequestFactory::fromGlobals());

var_dump($response instanceof Psr\Http\Message\ResponseInterface);
```

## Usage

### Add middlewares

Add PSR-15 middleware to the queue

```php
$queue = new Slince\Middleware\MiddlewareQueue([$middleware1, $middleware2]);
$queue->push($middleware);
```

Or add a callable function directly

```php
$queue->push(function(ServerRequestInterface $request, DelegateInterface $delegate){
    $delegate->process($request);
});
```

### Dispatch

```
try{
    $response = $queue->process(Zend\Diactoros\ServerRequestFactory::fromGlobals());
} catch (Slince\Middleware\Exception\MissingResponseException $exception) {
    //...
}
```
An `MissingResponseException` will be thrown if the middleware did not return a invalid response or the queue was exhausted

## License
 
The MIT license. See [MIT](https://opensource.org/licenses/MIT)