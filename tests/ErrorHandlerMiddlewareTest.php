<?php
declare(strict_types=1);

namespace Tests;

use Fyre\Error\Middleware\ErrorHandlerMiddleware;
use Fyre\Middleware\MiddlewareQueue;
use Fyre\Middleware\RequestHandler;
use Fyre\Server\ServerRequest;
use Tests\Mock\ExceptionMiddleware;
use PHPUnit\Framework\TestCase;

final class ErrorHandlerMiddlewareTest extends TestCase
{

    public function testException(): void
    {
        $middleware = new ErrorHandlerMiddleware();

        $queue = new MiddlewareQueue();
        $queue->add($middleware);
        $queue->add(new ExceptionMiddleware());

        $handler = new RequestHandler($queue);
        $request = new ServerRequest();

        $response = $handler->handle($request);

        $this->assertSame(
            500,
            $response->getStatusCode()
        );
    }

}
