<?php
declare(strict_types=1);

namespace Tests;

use Fyre\Config\Config;
use Fyre\Container\Container;
use Fyre\Error\ErrorHandler;
use Fyre\Error\Exceptions\BadRequestException;
use Fyre\Error\Exceptions\ConflictException;
use Fyre\Error\Exceptions\Exception;
use Fyre\Error\Exceptions\ForbiddenException;
use Fyre\Error\Exceptions\GoneException;
use Fyre\Error\Exceptions\InternalServerException;
use Fyre\Error\Exceptions\MethodNotAllowedException;
use Fyre\Error\Exceptions\NotAcceptableException;
use Fyre\Error\Exceptions\NotFoundException;
use Fyre\Error\Exceptions\NotImplementedException;
use Fyre\Error\Exceptions\ServiceUnavailableException;
use Fyre\Error\Exceptions\UnauthorizedException;
use Fyre\Event\Event;
use Fyre\Event\EventManager;
use Fyre\Server\ClientResponse;
use PHPUnit\Framework\TestCase;
use Throwable;

final class ErrorHandlerTest extends TestCase
{
    protected Container $container;

    protected ErrorHandler $errorHandler;

    public function testBadRequest(): void
    {
        $response = $this->errorHandler->render(new BadRequestException());

        $this->assertSame(
            400,
            $response->getStatusCode()
        );
    }

    public function testConflict(): void
    {
        $response = $this->errorHandler->render(new ConflictException());

        $this->assertSame(
            409,
            $response->getStatusCode()
        );
    }

    public function testEventBeforeRender(): void
    {
        $ran = false;
        $this->errorHandler->getEventManager()->on('Error.beforeRender', function(Event $event, Throwable $exception) use (&$ran): void {
            $ran = true;

            $this->assertInstanceOf(ConflictException::class, $exception);
        });

        $this->errorHandler->render(new ConflictException());

        $this->assertTrue($ran);
    }

    public function testForbidden(): void
    {
        $response = $this->errorHandler->render(new ForbiddenException());

        $this->assertSame(
            403,
            $response->getStatusCode()
        );
    }

    public function testGone(): void
    {
        $response = $this->errorHandler->render(new GoneException());

        $this->assertSame(
            410,
            $response->getStatusCode()
        );
    }

    public function testHandle(): void
    {
        $exception = new Exception('Error');
        $response = $this->errorHandler->render($exception);

        $this->assertInstanceOf(
            ClientResponse::class,
            $response
        );

        $this->assertSame(
            500,
            $response->getStatusCode()
        );

        $this->assertSame(
            '<pre>'.$exception.'</pre>',
            $response->getBody()
        );
    }

    public function testInternalServer(): void
    {
        $response = $this->errorHandler->render(new InternalServerException());

        $this->assertSame(
            500,
            $response->getStatusCode()
        );
    }

    public function testMethodNotAllowed(): void
    {
        $response = $this->errorHandler->render(new MethodNotAllowedException());

        $this->assertSame(
            405,
            $response->getStatusCode()
        );
    }

    public function testNotAcceptable(): void
    {
        $response = $this->errorHandler->render(new NotAcceptableException());

        $this->assertSame(
            406,
            $response->getStatusCode()
        );
    }

    public function testNotFound(): void
    {
        $response = $this->errorHandler->render(new NotFoundException());

        $this->assertSame(
            404,
            $response->getStatusCode()
        );
    }

    public function testNotImplemented(): void
    {
        $response = $this->errorHandler->render(new NotImplementedException());

        $this->assertSame(
            501,
            $response->getStatusCode()
        );
    }

    public function testRenderer(): void
    {
        $ran = false;
        $renderer = function(Throwable $exception) use (&$ran): string {
            $ran = true;

            return $exception->getMessage();
        };

        $this->assertSame(
            $this->errorHandler,
            $this->errorHandler->setRenderer($renderer)
        );

        $this->assertSame(
            $renderer,
            $this->errorHandler->getRenderer()
        );

        $exception = new Exception('Error');
        $response = $this->errorHandler->render($exception);

        $this->assertTrue($ran);

        $this->assertSame(
            'Error',
            $response->getBody()
        );
    }

    public function testServiceUnavailable(): void
    {
        $response = $this->errorHandler->render(new ServiceUnavailableException());

        $this->assertSame(
            503,
            $response->getStatusCode()
        );
    }

    public function testUnauthorized(): void
    {
        $response = $this->errorHandler->render(new UnauthorizedException());

        $this->assertSame(
            401,
            $response->getStatusCode()
        );
    }

    protected function setUp(): void
    {
        $this->container = new Container();
        $this->container->singleton(Config::class);
        $this->container->singleton(EventManager::class);
        $this->container->use(Config::class)->set('Error', [
            'log' => false,
        ]);

        $this->errorHandler = $this->container->use(ErrorHandler::class);
        $this->errorHandler->disableCli();
    }

    protected function tearDown(): void
    {
        $this->errorHandler->unregister();
    }
}
