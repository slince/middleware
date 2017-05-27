<?php
/**
 * slince middleware library
 * @author Tao <taosikai@yeah.net>
 */
namespace Slince\Middleware;

use Interop\Http\ServerMiddleware\MiddlewareInterface;
use Psr\Http\Message\ServerRequestInterface;
use Slince\Middleware\Exception\MissingResponseException;

class MiddlewareQueue
{
    /**
     * @var \SplQueue
     */
    public $middlewares;

    public function __construct(array $middlewares = [])
    {
        $this->middlewares = new \SplQueue();
        foreach ($middlewares as $middleware) {
            $this->push($middleware);
        }
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

    public function process(ServerRequestInterface $request)
    {
        if ($this->middlewares->isEmpty()) {
            throw new MissingResponseException( 'The queue was exhausted, with no response returned');
        }
        return $this->generateDelegate()->process($request);
    }

    protected function generateDelegate()
    {
        return new Delegate($this->middlewares->dequeue(), $this->generateDelegate());
    }

    protected static function decorateCallableMiddleware(callable $middleware)
    {
        return new CallableMiddleware($middleware);
    }
}