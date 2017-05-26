<?php
/**
 * ThinkFly middleware library
 * @author Tao <taosikai@yeah.net>
 */
namespace Think\Middleware;

use Interop\Http\ServerMiddleware\DelegateInterface;
use Psr\Http\Message\ServerRequestInterface;

class Delegate implements DelegateInterface
{
    /**
     * @var MiddlewareQueue
     */
    protected $middlewares;

    public function __construct(MiddlewareQueue $middlewares)
    {
        $this->middlewares = clone $middlewares;
    }

    public function process(ServerRequestInterface $request)
    {
        $this->middlewares->pop()->process($request, new Delegate($this->middlewares));
    }
}