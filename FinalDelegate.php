<?php
/**
 * slince middleware library
 * @author Tao <taosikai@yeah.net>
 */
namespace Slince\Middleware;

use Interop\Http\ServerMiddleware\DelegateInterface;
use Psr\Http\Message\ServerRequestInterface;
use Slince\Middleware\Exception\MissingResponseException;

class FinalDelegate implements DelegateInterface
{
    /**
     * {@inheritdoc}
     */
    public function process(ServerRequestInterface $request)
    {
        throw new MissingResponseException( 'The queue was exhausted, with no response returned');
    }
}