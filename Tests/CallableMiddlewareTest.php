<?php
namespace Slince\Middleware\Tests;

use Interop\Http\Server\MiddlewareInterface;
use Interop\Http\Server\RequestHandlerInterface;
use PHPUnit\Framework\TestCase;
use Psr\Http\Message\ServerRequestInterface;
use Slince\Middleware\CallableMiddleware;

class CallableMiddlewareTest extends TestCase
{
    public function testInstance()
    {
        $middleware = new CallableMiddleware(function (ServerRequestInterface $request, RequestHandlerInterface $delegate) {
            $delegate->handle($request);
        });
        $this->assertInstanceOf(MiddlewareInterface::class, $middleware);
    }
}
