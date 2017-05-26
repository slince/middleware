<?php
/**
 * slince middleware library
 * @author Tao <taosikai@yeah.net>
 */
namespace Slince\Middleware;

use Interop\Http\ServerMiddleware\DelegateInterface;
use Psr\Http\Message\ServerRequestInterface;
use Slince\Middleware\Exception\InvalidArgumentException;

class CallableMiddleware  extends Middleware
{
    /**
     * The callback
     * @var callable
     */
    protected $callable;

    public function __construct($callback)
    {
        if (!is_callable($callback)) {
            throw new InvalidArgumentException(sprintf('The callback is invalid, got type: "%s"',
                gettype($callback)
            ));
        }
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