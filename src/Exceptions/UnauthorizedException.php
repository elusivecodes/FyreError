<?php
declare(strict_types=1);

namespace Fyre\Error\Exceptions;

/**
 * UnauthorizedException
 */
class UnauthorizedException extends HttpException
{

    protected const DEFAULT_CODE = 401;

    protected const DEFAULT_MESSAGE = 'Unauthorized';

}
