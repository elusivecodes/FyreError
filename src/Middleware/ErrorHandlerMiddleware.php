<?php
declare(strict_types=1);

namespace Fyre\Error\Middleware;

use Closure;
use Fyre\Error\ErrorHandler;
use Fyre\Middleware\Middleware;
use Fyre\Server\ClientResponse;
use Fyre\Server\ServerRequest;
use Throwable;

/**
 * ErrorHandlerMiddleware
 */
class ErrorHandlerMiddleware extends Middleware
{
    protected ErrorHandler $errorHandler;

    /**
     * New ErrorHandlerMiddleware constructor.
     *
     * @param ErrorHandler $errorHandler The ErrorHandler.
     */
    public function __construct(ErrorHandler $errorHandler)
    {
        $this->errorHandler = $errorHandler;
    }

    /**
     * Handle a ServerRequest.
     *
     * @param ServerRequest $request The ServerRequest.
     * @param Closure $next The next handler.
     * @return ClientResponse The ClientResponse.
     */
    public function handle(ServerRequest $request, Closure $next): ClientResponse
    {
        try {
            return $next($request);
        } catch (Throwable $e) {
            return $this->errorHandler->render($e);
        }
    }
}
