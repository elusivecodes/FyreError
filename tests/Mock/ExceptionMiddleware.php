<?php
declare(strict_types=1);

namespace Tests\Mock;

use Closure;
use Fyre\Error\Exceptions\Exception;
use Fyre\Middleware\Middleware;
use Fyre\Server\ClientResponse;
use Fyre\Server\ServerRequest;

/**
 * ExceptionMiddleware
 */
class ExceptionMiddleware extends Middleware
{
    public function handle(ServerRequest $request, Closure $next): ClientResponse
    {
        throw new Exception('Error');
    }
}
