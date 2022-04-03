<?php
declare(strict_types=1);

namespace Tests;

use
    Fyre\Error\Middleware\ErrorHandlerMiddleware,
    Fyre\Middleware\MiddlewareQueue,
    Fyre\Middleware\RequestHandler,
    Fyre\Server\ServerRequest,
    Tests\Mock\ExceptionMiddleware,
    PHPUnit\Framework\TestCase;

final class ErrorHandlerMiddlewareTest extends TestCase
{

    public function testException(): void
    {
        $middleware = new ErrorHandlerMiddleware([
            'register' => false
        ]);

        $queue = new MiddlewareQueue();
        $queue->add($middleware);
        $queue->add(new ExceptionMiddleware);

        $handler = new RequestHandler($queue);
        $request = new ServerRequest;

        $response = $handler->handle($request);

        $this->assertSame(
            500,
            $response->getStatusCode()
        );
    }

}
