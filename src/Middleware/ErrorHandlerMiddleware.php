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

use const
    E_ALL;

use function
    array_replace_recursive;

/**
 * ErrorHandlerMiddleware
 */
class ErrorHandlerMiddleware extends Middleware
{

    protected static array $defaults = [
        'level' => E_ALL,
        'log' => true,
        'register' => true
    ];

    /**
     * New ErrorHandlerMiddleware constructor.
     * @param array $options Options for the middleware.
     */
    public function __construct(array $options = [])
    {
        $options = array_replace_recursive(static::$defaults, $options);

        if ($options['register']) {
            ErrorHandler::register($options);
        }
    }

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
