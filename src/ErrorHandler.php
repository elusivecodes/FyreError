<?php
declare(strict_types=1);

namespace Fyre\Error;

use Closure;
use Fyre\Console\Console;
use Fyre\Error\Exceptions\ErrorException;
use Fyre\Log\Log;
use Fyre\Server\ClientResponse;
use Throwable;

use function array_key_exists;
use function call_user_func;
use function error_get_last;
use function error_reporting;
use function in_array;
use function is_array;
use function register_shutdown_function;
use function set_error_handler;
use function set_exception_handler;

use const E_ERROR;
use const E_PARSE;
use const E_USER_ERROR;
use const PHP_SAPI;

/**
 * ErrorHandler
 */
abstract class ErrorHandler
{
    protected const FATAL_ERRORS = [
        E_USER_ERROR,
        E_ERROR,
        E_PARSE,
    ];

    protected static bool $cli = true;

    protected static Throwable|null $exception = null;

    protected static bool $log = false;

    protected static Closure|null $renderer = null;

    /**
     * Disable CLI output.
     */
    public static function disableCli(): void
    {
        static::$cli = false;
    }

    /**
     * Get the current Exception.
     *
     * @return Throwable|null The current Exception.
     */
    public static function getException(): Throwable|null
    {
        return static::$exception;
    }

    /**
     * Get the error renderer.
     *
     * @return Closure|null The error renderer.
     */
    public static function getRenderer(): Closure|null
    {
        return static::$renderer;
    }

    /**
     * Handle an Exception.
     *
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

        try {
            if (!static::$renderer) {
                throw $exception;
            }

            $result = call_user_func(static::$renderer, $exception);

            if ($result instanceof ClientResponse) {
                $response = $result;
            } else {
                $response = new ClientResponse();
                $response = $response->setBody((string) $result);
            }
        } catch (Throwable $e) {
            $response = new ClientResponse();
            $response = $response->setBody('<pre>'.$e.'</pre>');
        }

        try {
            $code = $exception->getCode();
            $response = $response->setStatusCode($code);
        } catch (Throwable $e) {
            $response = $response->setStatusCode(500);
        }

        return $response;
    }

    /**
     * Register the error handler.
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
     *
     * @param Throwable $exception The exception.
     */
    public static function render(Throwable $exception): void
    {
        static::handle($exception)->send();
        exit;
    }

    /**
     * Set the error renderer.
     *
     * @param Closure|null $renderer The error renderer.
     */
    public static function setRenderer(Closure|null $renderer): void
    {
        static::$renderer = $renderer;
    }
}
