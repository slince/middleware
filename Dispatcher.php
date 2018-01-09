<?php
/*
 * This file is part of the slince/middleware package.
 *
 * (c) Slince <taosikai@yeah.net>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Slince\Middleware;

use Interop\Http\Server\MiddlewareInterface;
use Interop\Http\Server\RequestHandlerInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Message\ResponseInterface;
use Slince\Middleware\Exception\MissingResponseException;

class Dispatcher
{
    /**
     * @var \SplQueue
     */
    public $middlewares;

    public function __construct($middlewares = [])
    {
        $this->middlewares = new \SplQueue();
        foreach ((array) $middlewares as $middleware) {
            $this->push($middleware);
        }
    }

    /**
     * Add a middleware to the queue.
     *
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
     * Get all middlewares.
     *
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
     * Dispatch the request to the middlewares and get psr7 response.
     *
     * @param ServerRequestInterface $request
     *
     * @throws MissingResponseException
     *
     * @return ResponseInterface
     */
    public function process(ServerRequestInterface $request): ResponseInterface
    {
        $response = $this->resolve()->handle($request);
        if (!$response instanceof ResponseInterface) {
            throw new MissingResponseException('Last middleware executed did not return a response.');
        }

        return $response;
    }

    /**
     * @return Delegate
     */
    protected function resolve(): RequestHandlerInterface
    {
        return new Delegate(function($request){
            if (!$this->middlewares->isEmpty()) {
                $middleware = $this->middlewares->dequeue();
                $response = $middleware->process($request, $this->resolve());
                if (!$response instanceof ResponseInterface) {
                    throw new MissingResponseException(sprintf('Unexpected middleware result: %s', gettype($response)));
                }

                return $response;
            } else {
                throw new MissingResponseException( 'The queue was exhausted, with no response returned');
            }
        });
    }

    protected static function decorateCallableMiddleware(callable $middleware): CallableMiddleware
    {
        return new CallableMiddleware($middleware);
    }
}