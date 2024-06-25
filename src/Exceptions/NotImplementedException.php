<?php
declare(strict_types=1);

namespace Fyre\Error\Exceptions;

/**
 * NotImplementedException
 */
class NotImplementedException extends HttpException
{
    protected const DEFAULT_CODE = 501;

    protected const DEFAULT_MESSAGE = 'Not Implemented';
}
