<?php
/**
 * ThinkFly middleware library
 * @author Tao <taosikai@yeah.net>
 */
namespace Think\Middleware;

use Interop\Http\ServerMiddleware\MiddlewareInterface;
use Psr\Http\Message\ServerRequestInterface;

class MiddlewareQueue
{
    public $middlewares;

    public function __construct(array $middleware)
    {
        $this->middlewares = new \SplQueue();
    }

    /**
     * Add a middleware to the queue
     * @param callable|MiddlewareInterface $middleware
     */
    public function push($middleware)
    {
        if (is_callable($middleware)) {
            $middleware = static::decorateCallableMiddleware($middleware);
        }
        $this->middlewares->enqueue($middleware);
    }

    /**
     * Pop a middleware from the queue
     * @return MiddlewareInterface
     */
    public function pop()
    {
        return $this->middlewares->dequeue();
    }

    public function process(ServerRequestInterface $request)
    {
        return (new Delegate($this))->process($request);
    }

    protected static function decorateCallableMiddleware(callable $middleware)
    {
        return new CallableMiddleware($middleware);
    }
}