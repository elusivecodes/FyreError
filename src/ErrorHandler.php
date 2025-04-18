<?php
declare(strict_types=1);

namespace Fyre\Error;

use Closure;
use ErrorException;
use Fyre\Config\Config;
use Fyre\Console\Console;
use Fyre\Container\Container;
use Fyre\Event\EventDispatcherTrait;
use Fyre\Event\EventManager;
use Fyre\Log\LogManager;
use Fyre\Server\ClientResponse;
use Throwable;

use function array_replace;
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
class ErrorHandler
{
    use EventDispatcherTrait;

    protected const FATAL_ERRORS = [
        E_USER_ERROR,
        E_ERROR,
        E_PARSE,
    ];

    protected static array $defaults = [
        'level' => E_ALL,
        'renderer' => null,
        'log' => true,
    ];

    protected bool $cli = true;

    protected Container $container;

    protected Throwable|null $exception = null;

    protected Console $io;

    protected int $level = E_ALL;

    protected bool $log = true;

    protected LogManager $logManager;

    protected int $originalLevel = 0;

    protected bool $registered = false;

    protected Closure|null $renderer = null;

    /**
     * New ErrorHandler constructor.
     *
     * @param Container $container The Container.
     * @param Console $io The Console.
     * @param LogManager $logManager The LogManager.
     * @param EventManager $eventManager The EventManager.
     * @param Config $config The Config.
     */
    public function __construct(Container $container, Console $io, LogManager $logManager, EventManager $eventManager, Config $config)
    {
        $this->container = $container;
        $this->io = $io;
        $this->logManager = $logManager;
        $this->eventManager = $eventManager;

        $options = array_replace(static::$defaults, $config->get('Error', []));

        $this->level = $options['level'];
        $this->renderer = $options['renderer'];
        $this->log = $options['log'];

        register_shutdown_function(function(): void {
            if (!$this->registered) {
                return;
            }

            $error = error_get_last();

            if (!is_array($error) || !in_array($error['type'], static::FATAL_ERRORS)) {
                return;
            }

            $exception = new ErrorException($error['message'], 0, $error['type'], $error['file'], $error['line']);

            $this->render($exception)->send();
        });
    }

    /**
     * Destroy the ErrorHandler.
     */
    public function __destruct()
    {
        $this->unregister();
    }

    /**
     * Disable CLI error handling.
     */
    public function disableCli(): static
    {
        $this->cli = false;

        return $this;
    }

    /**
     * Enable CLI error handling.
     */
    public function enableCli(): static
    {
        $this->cli = true;

        return $this;
    }

    /**
     * Get the current Exception.
     *
     * @return Throwable|null The current Exception.
     */
    public function getException(): Throwable|null
    {
        return $this->exception;
    }

    /**
     * Get the error renderer.
     *
     * @return Closure|null The error renderer.
     */
    public function getRenderer(): Closure|null
    {
        return $this->renderer;
    }

    /**
     * Register the error handler.
     */
    public function register(): void
    {
        if ($this->registered) {
            return;
        }

        $this->registered = true;

        $this->originalLevel = error_reporting($this->level);

        set_error_handler(function(int $type, string $message, string $file, int $line): void {
            $exception = new ErrorException($message, 0, $type, $file, $line);

            $this->render($exception)->send();
        });

        set_exception_handler(function(Throwable $exception): void {
            $this->render($exception)->send();
        });
    }

    /**
     * Render an Exception.
     *
     * @param Throwable $exception The exception.
     * @return ClientResponse The ClientResponse;
     */
    public function render(Throwable $exception): ClientResponse
    {
        $this->exception = $exception;

        if ($this->log) {
            $this->logManager->handle('error', (string) $exception);
        }

        $this->dispatchEvent('Error.beforeRender', ['exception' => $exception], false);

        if ($this->cli && PHP_SAPI === 'cli') {
            $this->io->error((string) $exception);
            exit;
        }

        try {
            if (!$this->renderer) {
                throw $exception;
            }

            $result = $this->container->call($this->renderer, ['exception' => $exception]);

            if ($result instanceof ClientResponse) {
                $response = $result;
            } else {
                $response = $this->container->build(ClientResponse::class)
                    ->setBody((string) $result);
            }
        } catch (Throwable $e) {
            $response = $this->container->build(ClientResponse::class)
                ->setBody('<pre>'.$e.'</pre>');
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
     * Set the error renderer.
     *
     * @param Closure|null $renderer The error renderer.
     * @return ErrorHandler The ErrorHandler.
     */
    public function setRenderer(Closure|null $renderer): static
    {
        $this->renderer = $renderer;

        return $this;
    }

    /**
     * Unregister the error handler.
     */
    public function unregister(): void
    {
        if (!$this->registered) {
            return;
        }

        $this->registered = false;

        error_reporting($this->originalLevel);
        restore_error_handler();
        restore_exception_handler();
    }
}
