<?php
declare(strict_types=1);

namespace Fyre\Error\Exceptions;

use Throwable;

/**
 * HttpException
 */
abstract class HttpException extends Exception
{
    protected const DEFAULT_MESSAGE = 'Internal Server Error';

    /**
     * New HttpException constructor.
     *
     * @param string|null $message The message.
     * @param int|null $code The error code.
     * @param Throwable|null $previous The previous exception.
     */
    public function __construct(string|null $message = null, int|null $code = null, Throwable|null $previous = null)
    {
        parent::__construct($message ?? static::DEFAULT_MESSAGE, $code, $previous);
    }
}
