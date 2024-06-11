<?php
declare(strict_types=1);

namespace Tests;

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
use Fyre\Error\ErrorHandler;
use Fyre\Server\ClientResponse;
use PHPUnit\Framework\TestCase;
use Throwable;

final class ErrorHandlerTest extends TestCase
{

    public function testRenderer(): void
    {
        $ran = false;
        $renderer = function(Throwable $exception) use (&$ran): string {
            $ran = true;
            return $exception->getMessage();
        };

        ErrorHandler::setRenderer($renderer);

        $this->assertSame(
            $renderer,
            ErrorHandler::getRenderer()
        );

        $exception = new Exception('Error');
        $response = ErrorHandler::handle($exception);

        $this->assertTrue($ran);

        $this->assertSame(
            'Error',
            $response->getBody()
        );
    }

    public function testHandle(): void
    {
        $exception = new Exception('Error');
        $response = ErrorHandler::handle($exception);

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

    public function testBadRequest(): void
    {
        $response = ErrorHandler::handle(new BadRequestException());

        $this->assertSame(
            400,
            $response->getStatusCode()
        );
    }

    public function testConflict(): void
    {
        $response = ErrorHandler::handle(new ConflictException());

        $this->assertSame(
            409,
            $response->getStatusCode()
        );
    }

    public function testForbidden(): void
    {
        $response = ErrorHandler::handle(new ForbiddenException());

        $this->assertSame(
            403,
            $response->getStatusCode()
        );
    }

    public function testGone(): void
    {
        $response = ErrorHandler::handle(new GoneException());

        $this->assertSame(
            410,
            $response->getStatusCode()
        );
    }

    public function testInternalServer(): void
    {
        $response = ErrorHandler::handle(new InternalServerException());

        $this->assertSame(
            500,
            $response->getStatusCode()
        );
    }

    public function testMethodNotAllowed(): void
    {
        $response = ErrorHandler::handle(new MethodNotAllowedException());

        $this->assertSame(
            405,
            $response->getStatusCode()
        );
    }

    public function testNotAcceptable(): void
    {
        $response = ErrorHandler::handle(new NotAcceptableException());

        $this->assertSame(
            406,
            $response->getStatusCode()
        );
    }

    public function testNotFound(): void
    {
        $response = ErrorHandler::handle(new NotFoundException());

        $this->assertSame(
            404,
            $response->getStatusCode()
        );
    }

    public function testNotImplemented(): void
    {
        $response = ErrorHandler::handle(new NotImplementedException());

        $this->assertSame(
            501,
            $response->getStatusCode()
        );
    }

    public function testServiceUnavailable(): void
    {
        $response = ErrorHandler::handle(new ServiceUnavailableException());

        $this->assertSame(
            503,
            $response->getStatusCode()
        );
    }

    public function testUnauthorized(): void
    {
        $response = ErrorHandler::handle(new UnauthorizedException());

        $this->assertSame(
            401,
            $response->getStatusCode()
        );
    }

    protected function tearDown(): void
    {
        ErrorHandler::setRenderer(null);
    }

}
