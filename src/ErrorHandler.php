<?php
declare(strict_types=1);

namespace Fyre\Error;

use
    Fyre\Error\Exceptions\ErrorException,
    Fyre\Error\Exceptions\FatalErrorException,
    Fyre\Log\Log,
    Fyre\Router\Router,
    Fyre\Server\ClientResponse,
    Fyre\Server\ServerRequest,
    Throwable;

use const
    E_ERROR,
    E_PARSE,
    E_USER_ERROR;

use function
    array_key_exists,
    error_get_last,
    error_reporting,
    in_array,
    is_array,
    register_shutdown_function,
    set_error_handler,
    set_exception_handler;

/**
 * ErrorHandler
 */
abstract class ErrorHandler
{

    protected const FATAL_ERRORS = [
        E_USER_ERROR,
        E_ERROR,
        E_PARSE
    ];

    protected static Throwable|null $exception = null;

    protected static bool $log = false;

    /**
     * Get the current Exception.
     * @return Throwable|null The current Exception.
     */
    public static function getException(): Throwable|null
    {
        return static::$exception;
    }

    /**
     * Handle an Exception.
     * @param Throwable $exception The exception.
     * @return ClientResponse The ClientResponse.
     */
    public static function handle(Throwable $exception): ClientResponse
    {
        $hasException = !!static::$exception;

        static::$exception = $exception;

        if (static::$log) {
            Log::error((string) $exception);
        }

        $response = new ClientResponse;

        try {
            $code = $exception->getCode();    
            $response->setStatusCode($code);
        } catch (Throwable $e) {
            $response->setStatusCode(500);
        }

        try {
            $route = Router::getErrorRoute();

            if (!$route) {
                throw $exception;
            }

            $response = $route->process(new ServerRequest, $response);
        } catch (Throwable $e) {
            $response->setBody('<pre>'.$e.'</pre>');
        }

        return $response;
    }

    /**
     * Register the error handler.
     * @param array $options
     */
    public static function register(array $options = []): void
    {
        if (array_key_exists('log', $options)) {
            static::$log = $options['log'];
        }

        if (array_key_exists('level', $options)) {
            error_reporting($options['level']);
        }

        register_shutdown_function(function(): void {
            $error = error_get_last();

            if (!is_array($error) || !in_array($error['type'], static::FATAL_ERRORS)) {
                return;
            }

            $exception = new ErrorException($error['message'], $error['type'], $error['file'], $error['line']);

            static::render($exception);
        });

        set_error_handler(function(int $type, string $message, string $file, int $line): void {
            $exception = new ErrorException($message, $type, $file, $line);

            static::render($exception);
        });

        set_exception_handler(function(Throwable $exception): void {
            static::render($exception);
        });
    }

    /**
     * Render an Exception.
     * @param Throwable $exception The exception.
     */
    public static function render(Throwable $exception): void
    {
        static::handle($exception)->send();
        exit;
    }

}
