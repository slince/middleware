<?php
/**
 * slince middleware library
 * @author Tao <taosikai@yeah.net>
 */
namespace Slince\Middleware;

use Interop\Http\ServerMiddleware\DelegateInterface;
use Interop\Http\ServerMiddleware\MiddlewareInterface;
use Psr\Http\Message\ServerRequestInterface;

class CallableMiddleware implements MiddlewareInterface
{
    /**
     * The callback
     * @var callable
     */
    protected $callable;

    public function __construct(callable $callback)
    {
        $this->callable = $callback;
    }

    /**
     * {@inheritdoc}
     */
    public function process(ServerRequestInterface $request, DelegateInterface $delegate)
    {
        return call_user_func($this->callable, $request, $delegate);
    }
}