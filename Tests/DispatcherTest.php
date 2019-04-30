<?php
namespace Slince\Middleware;

use Interop\Http\Server\RequestHandlerInterface;
use PHPUnit\Framework\TestCase;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Slince\Middleware\Exception\MissingResponseException;
use Zend\Diactoros\Response;
use Zend\Diactoros\ServerRequestFactory;

class DispatcherTest extends TestCase
{
    public function testConstructor()
    {
        $middlewareFoo = new CallableMiddleware(function (ServerRequestInterface $request, RequestHandlerInterface $next) {
            return $next->handle($request);
        });
        $queue = new Dispatcher([$middlewareFoo]);
        $this->assertCount(1, $queue->all());
    }

    public function testPush()
    {
        $middlewareFoo = new CallableMiddleware(function (ServerRequestInterface $request, RequestHandlerInterface $next) {
            return $next->handle($request);
        });
        $middlewareBar = new CallableMiddleware(function (ServerRequestInterface $request, RequestHandlerInterface $next) {
            return $next->handle($request);
        });
        $queue = new Dispatcher();
        $queue->push($middlewareFoo);
        $queue->push($middlewareBar);
        $this->assertCount(2, $queue->all());
    }


    public function testPushCallable()
    {
        $queue = new Dispatcher();
        $queue->push(function (ServerRequestInterface $request, RequestHandlerInterface $next) {
            return $next->handle($request);
        });
        $this->assertCount(1, $queue->all());
        $this->assertInstanceOf(CallableMiddleware::class, $queue->all()[0]);
    }

    public function testProcess()
    {
        $middlewareFoo = new CallableMiddleware(function (ServerRequestInterface $request, RequestHandlerInterface $next) {
            $response = new Response();
            $response->getBody()->write('foo');
            return $response;
        });
        $queue = new Dispatcher([$middlewareFoo]);
        $response = $queue->handle(ServerRequestFactory::fromGlobals());
        $this->assertInstanceOf(ResponseInterface::class, $response);
    }

    public function testInvalidResponseReturned()
    {
        $middlewareFoo = new CallableMiddleware(function (ServerRequestInterface $request, RequestHandlerInterface $next) {
            return 'foo';
        });
        $queue = new Dispatcher($middlewareFoo);
        $this->expectException(\TypeError::class);
        $queue->handle(ServerRequestFactory::fromGlobals());
    }

    public function testQueueExhausted()
    {
        $middlewareFoo = new CallableMiddleware(function (ServerRequestInterface $request, RequestHandlerInterface $next) {
            return $next->handle($request);
        });
        $queue = new Dispatcher([$middlewareFoo]);
        $this->expectException(MissingResponseException::class);
        $queue->handle(ServerRequestFactory::fromGlobals());
    }
}
