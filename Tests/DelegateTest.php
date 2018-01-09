<?php
namespace Slince\Middleware;

use PHPUnit\Framework\TestCase;
use Zend\Diactoros\Response;
use Zend\Diactoros\ServerRequestFactory;

class DelegateTest extends TestCase
{
    public function testInstance()
    {
        $delegate = new Delegate(function($request){
            $response = new Response();
            $response->getBody()->write('hello');
            return $response;
        });

        $response = $delegate->handle(ServerRequestFactory::fromGlobals());
        $this->assertEquals(200, $response->getStatusCode());
        $this->assertEquals('hello', (string)$response->getBody());
    }
}