<?php
declare(strict_types=1);

namespace Tests;

use
    Fyre\Error\Exceptions\BadRequestException,
    Fyre\Error\Exceptions\ConflictException,
    Fyre\Error\Exceptions\Exception,
    Fyre\Error\Exceptions\ForbiddenException,
    Fyre\Error\Exceptions\GoneException,
    Fyre\Error\Exceptions\InternalServerException,
    Fyre\Error\Exceptions\MethodNotAllowedException,
    Fyre\Error\Exceptions\NotAcceptableException,
    Fyre\Error\Exceptions\NotFoundException,
    Fyre\Error\Exceptions\NotImplementedException,
    Fyre\Error\Exceptions\ServiceUnavailableException,
    Fyre\Error\Exceptions\UnauthorizedException,
    Fyre\Error\ErrorHandler,
    Fyre\Server\ClientResponse,
    PHPUnit\Framework\TestCase;

final class ErrorHandlerTest extends TestCase
{

    public function testHandle()
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

    public function testBadRequest()
    {
        $response = ErrorHandler::handle(new BadRequestException);

        $this->assertSame(
            400,
            $response->getStatusCode()
        );
    }

    public function testConflict()
    {
        $response = ErrorHandler::handle(new ConflictException);

        $this->assertSame(
            409,
            $response->getStatusCode()
        );
    }

    public function testForbidden()
    {
        $response = ErrorHandler::handle(new ForbiddenException);

        $this->assertSame(
            403,
            $response->getStatusCode()
        );
    }

    public function testGone()
    {
        $response = ErrorHandler::handle(new GoneException);

        $this->assertSame(
            410,
            $response->getStatusCode()
        );
    }

    public function testInternalServer()
    {
        $response = ErrorHandler::handle(new InternalServerException);

        $this->assertSame(
            500,
            $response->getStatusCode()
        );
    }

    public function testMethodNotAllowed()
    {
        $response = ErrorHandler::handle(new MethodNotAllowedException);

        $this->assertSame(
            405,
            $response->getStatusCode()
        );
    }

    public function testNotAcceptable()
    {
        $response = ErrorHandler::handle(new NotAcceptableException);

        $this->assertSame(
            406,
            $response->getStatusCode()
        );
    }

    public function testNotFound()
    {
        $response = ErrorHandler::handle(new NotFoundException);

        $this->assertSame(
            404,
            $response->getStatusCode()
        );
    }

    public function testNotImplemented()
    {
        $response = ErrorHandler::handle(new NotImplementedException);

        $this->assertSame(
            501,
            $response->getStatusCode()
        );
    }

    public function testServiceUnavailable()
    {
        $response = ErrorHandler::handle(new ServiceUnavailableException);

        $this->assertSame(
            503,
            $response->getStatusCode()
        );
    }

    public function testUnauthorized()
    {
        $response = ErrorHandler::handle(new UnauthorizedException);

        $this->assertSame(
            401,
            $response->getStatusCode()
        );
    }

}
