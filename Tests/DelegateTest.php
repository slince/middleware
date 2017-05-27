<?php
namespace Slince\Middleware;

use Interop\Http\ServerMiddleware\DelegateInterface;
use PHPUnit\Framework\TestCase;
use Psr\Http\Message\ServerRequestInterface;
use Slince\Middleware\Exception\MissingResponseException;
use Zend\Diactoros\Response;
use Zend\Diactoros\ServerRequestFactory;

class DelegateTest extends TestCase
{
    public function testInstance()
    {
        $middlewareFoo = new CallableMiddleware(function(ServerRequestInterface $request, DelegateInterface $delegate){
            return $delegate->process($request);
        });

        $middlewareBar = new CallableMiddleware(function(ServerRequestInterface $request, DelegateInterface $delegate){
            $response = new Response();
            $response->getBody()->write('hello');
            return $response;
        });

        $delegate = new Delegate($middlewareFoo, new Delegate($middlewareBar, new FinalDelegate()));
        $response = $delegate->process(ServerRequestFactory::fromGlobals());
        $this->assertEquals(200, $response->getStatusCode());
        $this->assertEquals('hello', (string)$response->getBody());
    }

    public function testFinalDelegate()
    {
        $middlewareFoo = new CallableMiddleware(function(ServerRequestInterface $request, DelegateInterface $delegate){
            return $delegate->process($request);
        });

        $middlewareBar = new CallableMiddleware(function(ServerRequestInterface $request, DelegateInterface $delegate){
            return $delegate->process($request);
        });
        $delegate = new Delegate($middlewareFoo, new Delegate($middlewareBar, new FinalDelegate()));
        $this->expectException(MissingResponseException::class);;
        $delegate->process(ServerRequestFactory::fromGlobals());
    }
}