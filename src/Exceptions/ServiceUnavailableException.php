<?php
declare(strict_types=1);

namespace Fyre\Error\Exceptions;

/**
 * ServiceUnavailableException
 */
class ServiceUnavailableException extends HttpException
{
    protected const DEFAULT_CODE = 503;

    protected const DEFAULT_MESSAGE = 'Service Unavailable';
}
