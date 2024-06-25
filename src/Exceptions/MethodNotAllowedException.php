<?php
declare(strict_types=1);

namespace Fyre\Error\Exceptions;

/**
 * MethodNotAllowedException
 */
class MethodNotAllowedException extends HttpException
{
    protected const DEFAULT_CODE = 405;

    protected const DEFAULT_MESSAGE = 'Method Not Allowed';
}
