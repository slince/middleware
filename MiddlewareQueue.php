<?php
/**
 * slince middleware library
 * @author Tao <taosikai@yeah.net>
 */
namespace Slince\Middleware;

use Interop\Http\ServerMiddleware\MiddlewareInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Message\ResponseInterface;
use Slince\Middleware\Exception\MissingResponseException;

class MiddlewareQueue
{
    /**
     * @var \SplQueue
     */
    public $middlewares;

    public function __construct($middlewares = [])
    {
        $this->middlewares = new \SplQueue();
        foreach ((array)$middlewares as $middleware) {
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

    /**
     * Get all middlewares
     * @return MiddlewareInterface[]
     */
    public function all()
    {
        $middlewares = [];
        foreach ($this->middlewares as $middleware) {
            $middlewares[] = $middleware;
        }
        return $middlewares;
    }

    /**
     * Dispatch the request to the middlewares and get psr7 response
     * @param ServerRequestInterface $request
     * @throws MissingResponseException
     * @return ResponseInterface
     */
    public function process(ServerRequestInterface $request)
    {
        $response = $this->generateDelegate()->process($request);
        if (!$response instanceof ResponseInterface) {
            throw new MissingResponseException('Last middleware executed did not return a response.');
        }
        return $response;
    }

    /**
     * @return Delegate
     */
    protected function generateDelegate()
    {
        return new Delegate($this->middlewares->dequeue(), $this->middlewares->isEmpty()
            ? new FinalDelegate() : $this->generateDelegate());
    }

    protected static function decorateCallableMiddleware(callable $middleware)
    {
        return new CallableMiddleware($middleware);
    }
}