<?php
declare(strict_types=1);

namespace Fyre\Error\Middleware;

use
    Fyre\Error\ErrorHandler,
    Fyre\Middleware\Middleware,
    Fyre\Middleware\RequestHandler,
    Fyre\Server\ClientResponse,
    Fyre\Server\ServerRequest,
    Throwable;

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
