<?php
/**
 * slince middleware library
 * @author Tao <taosikai@yeah.net>
 */
namespace Slince\Middleware;

use Interop\Http\ServerMiddleware\DelegateInterface;
use Interop\Http\ServerMiddleware\MiddlewareInterface;
use Psr\Http\Message\ServerRequestInterface;

class Delegate implements DelegateInterface
{
    /**
     * @var MiddlewareInterface
     */
    protected $middleware;

    /**
     * @var DelegateInterface
     */
    protected $delegate;

    public function __construct(MiddlewareInterface $middleware, DelegateInterface $delegate)
    {
        $this->middleware = $middleware;
        $this->delegate = $delegate;
    }

    /**
     * {@inheritdoc}
     */
    public function process(ServerRequestInterface $request)
    {
        return $this->middleware->process($request, $this->delegate);
    }
}