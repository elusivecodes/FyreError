<?php
declare(strict_types=1);

namespace Fyre\Error\Exceptions;

/**
 * ForbiddenException
 */
class ForbiddenException extends HttpException
{

    protected const DEFAULT_CODE = 403;

    protected const DEFAULT_MESSAGE = 'Forbidden';

}
