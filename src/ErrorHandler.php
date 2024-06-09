<?php
declare(strict_types=1);

namespace Fyre\Error;

use Fyre\Console\Console;
use Fyre\Error\Exceptions\ErrorException;
use Fyre\Log\Log;
use Fyre\Router\Router;
use Fyre\Server\ClientResponse;
use Fyre\Server\ServerRequest;
use Throwable;

use const E_ERROR;
use const E_PARSE;
use const E_USER_ERROR;
use const PHP_SAPI;

use function array_key_exists;
use function error_get_last;
use function error_reporting;
use function in_array;
use function is_array;
use function register_shutdown_function;
use function set_error_handler;
use function set_exception_handler;

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

    protected static bool $cli = true;

    /**
     * Disable CLI output.
     */
    public static function disableCli(): void
    {
        static::$cli = false;
    }

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
     * @return ClientResponse|null The ClientResponse.
     */
    public static function handle(Throwable $exception): ClientResponse|null
    {
        static::$exception = $exception;

        if (static::$log) {
            Log::error((string) $exception);
        }

        if (static::$cli && PHP_SAPI === 'cli') {
            Console::error((string) $exception);
            exit;
        }

        $response = new ClientResponse();

        try {
            $code = $exception->getCode();    
            $response = $response->setStatusCode($code);
        } catch (Throwable $e) {
            $response = $response->setStatusCode(500);
        }

        try {
            $route = Router::getErrorRoute();

            if (!$route) {
                throw $exception;
            }

            $result = $route->process(ServerRequest::instance(), $response);

            if ($result instanceof ClientResponse) {
                $response = $result;
            } else {
                $response = $response->setBody((string) $result);
            }
        } catch (Throwable $e) {
            $response = $response->setBody('<pre>'.$e.'</pre>');
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
