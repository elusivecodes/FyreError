<?php
declare(strict_types=1);

namespace Fyre\Error\Exceptions;

/**
 * GoneException
 */
class GoneException extends HttpException
{
    protected const DEFAULT_CODE = 410;

    protected const DEFAULT_MESSAGE = 'Gone';
}
