<?php
declare(strict_types=1);

namespace Tests\Mock;

use
    Fyre\Error\Exceptions\Exception,
    Fyre\Middleware\Middleware,
    Fyre\Middleware\RequestHandler,
    Fyre\Server\ClientResponse,
    Fyre\Server\ServerRequest;

/**
 * ExceptionMiddleware
 */
class ExceptionMiddleware extends Middleware
{

    /**
     * Process a ServerRequest.
     * @param ServerRequest $request The ServerRequest.
     * @param RequestHandler $handler The RequestHandler.
     * @return ClientResponse The ClientResponse.
     */
    public function process(ServerRequest $request, RequestHandler $handler): ClientResponse
    {
        throw new Exception('Error');
    }

}
