<?php
declare(strict_types=1);

namespace Tests;

use Fyre\Config\Config;
use Fyre\Container\Container;
use Fyre\Error\ErrorHandler;
use Fyre\Error\Middleware\ErrorHandlerMiddleware;
use Fyre\Event\EventManager;
use Fyre\Middleware\MiddlewareQueue;
use Fyre\Middleware\RequestHandler;
use Fyre\Server\ServerRequest;
use PHPUnit\Framework\TestCase;
use Tests\Mock\ExceptionMiddleware;

final class ErrorHandlerMiddlewareTest extends TestCase
{
    protected Container $container;

    public function testException(): void
    {
        $queue = new MiddlewareQueue();
        $queue->add(ErrorHandlerMiddleware::class);
        $queue->add(ExceptionMiddleware::class);

        $handler = $this->container->build(RequestHandler::class, ['queue' => $queue]);
        $request = $this->container->build(ServerRequest::class);

        $response = $handler->handle($request);

        $this->assertSame(
            500,
            $response->getStatusCode()
        );
    }

    protected function setUp(): void
    {
        $this->container = new Container();
        $this->container->singleton(Config::class);
        $this->container->singleton(EventManager::class);
        $this->container->singleton(ErrorHandler::class);

        $this->container->use(Config::class)->set('Error', [
            'log' => false,
        ]);

        $this->container->use(ErrorHandler::class)->disableCli();
    }

    protected function tearDown(): void
    {
        $this->container->use(ErrorHandler::class)->unregister();
    }
}
