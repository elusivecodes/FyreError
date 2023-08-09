<?php
declare(strict_types=1);

namespace Fyre\Error\Middleware;

use Fyre\Error\ErrorHandler;
use Fyre\Middleware\Middleware;
use Fyre\Middleware\RequestHandler;
use Fyre\Server\ClientResponse;
use Fyre\Server\ServerRequest;
use Throwable;

/**
 * ErrorHandlerMiddleware
 */
class ErrorHandlerMiddleware extends Middleware
{

    /**
     * Process a ServerRequest.
     * @param ServerRequest $request The ServerRequest.
     * @param RequestHandler $handler The RequestHandler.
     * @return ClientResponse The ClientResponse.
     */
    public function process(ServerRequest $request, RequestHandler $handler): ClientResponse
    {
        try {
            return $handler->handle($request);
        } catch (Throwable $e) {
            return ErrorHandler::handle($e);
        }
    }

}
