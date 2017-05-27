<?php
namespace Slince\Middleware;

use PHPUnit\Framework\TestCase;
use Interop\Http\ServerMiddleware\DelegateInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Slince\Middleware\Exception\MissingResponseException;
use Zend\Diactoros\Response;
use Zend\Diactoros\ServerRequestFactory;

class MiddlewareQueueTest extends TestCase
{
    public function testConstructor()
    {
        $middlewareFoo = new CallableMiddleware(function(ServerRequestInterface $request, DelegateInterface $delegate){
            return $delegate->process($request);
        });
        $queue = new MiddlewareQueue([$middlewareFoo]);
        $this->assertCount(1, $queue->all());
    }

    public function testPush()
    {
        $middlewareFoo = new CallableMiddleware(function(ServerRequestInterface $request, DelegateInterface $delegate){
            return $delegate->process($request);
        });
        $middlewareBar = new CallableMiddleware(function(ServerRequestInterface $request, DelegateInterface $delegate){
            return $delegate->process($request);
        });
        $queue = new MiddlewareQueue();
        $queue->push($middlewareFoo);
        $queue->push($middlewareBar);
        $this->assertCount(2, $queue->all());
    }


    public function testPushCallable()
    {
        $queue = new MiddlewareQueue();
        $queue->push(function(ServerRequestInterface $request, DelegateInterface $delegate){
            return $delegate->process($request);
        });
        $this->assertCount(1, $queue->all());
        $this->assertInstanceOf(CallableMiddleware::class, $queue->all()[0]);
    }

    public function testProcess()
    {
        $middlewareFoo = new CallableMiddleware(function(ServerRequestInterface $request, DelegateInterface $delegate){
            $response = new Response();
            $response->getBody()->write('foo');
            return $response;
        });
        $queue = new MiddlewareQueue([$middlewareFoo]);
        $response = $queue->process(ServerRequestFactory::fromGlobals());
        $this->assertInstanceOf(ResponseInterface::class, $response);
    }

    public function testInvalidResponseReturned()
    {
        $middlewareFoo = new CallableMiddleware(function(ServerRequestInterface $request, DelegateInterface $delegate){
            return 'foo';
        });
        $queue = new MiddlewareQueue($middlewareFoo);
        $this->expectException(MissingResponseException::class);
        $queue->process(ServerRequestFactory::fromGlobals());
    }

    public function testQueueExhausted()
    {
        $middlewareFoo = new CallableMiddleware(function(ServerRequestInterface $request, DelegateInterface $delegate){
            return $delegate->process($request);
        });
        $queue = new MiddlewareQueue([$middlewareFoo]);
        $this->expectException(MissingResponseException::class);
        $queue->process(ServerRequestFactory::fromGlobals());
    }
}