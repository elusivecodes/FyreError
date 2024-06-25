<?php
declare(strict_types=1);

namespace Fyre\Error\Exceptions;

/**
 * ConflictException
 */
class ConflictException extends HttpException
{
    protected const DEFAULT_CODE = 409;

    protected const DEFAULT_MESSAGE = 'Conflict';
}
