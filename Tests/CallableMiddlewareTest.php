<?php
namespace Slince\Middleware\Tests;

use Interop\Http\ServerMiddleware\DelegateInterface;
use Interop\Http\ServerMiddleware\MiddlewareInterface;
use PHPUnit\Framework\TestCase;
use Psr\Http\Message\ServerRequestInterface;
use Slince\Middleware\CallableMiddleware;

class CallableMiddlewareTest extends TestCase
{
    public function testInstance()
    {
        $middleware = new CallableMiddleware(function(ServerRequestInterface $request, DelegateInterface $delegate){
            $delegate->process($request);
        });
        $this->assertInstanceOf(MiddlewareInterface::class, $middleware);
    }
}