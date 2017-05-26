<?php
/**
 * slince middleware library
 * @author Tao <taosikai@yeah.net>
 */
namespace Slince\Middleware;

use Psr\Http\Message\ServerRequestInterface;

class Next
{
    /**
     * @var MiddlewareQueue
     */
    protected $middlewares;

    public function __construct(MiddlewareQueue $middlewares)
    {
    }

    public function process(ServerRequestInterface $request)
    {
        $this->middlewares->pop()->process($request, new );
    }
}