<?php
declare(strict_types=1);

namespace Fyre\Error\Exceptions;

use RuntimeException;
use Throwable;

/**
 * Exception
 */
class Exception extends RuntimeException
{
    protected const DEFAULT_CODE = 500;

    /**
     * New Exception constructor.
     *
     * @param string $message The message.
     * @param int|null $code The error code.
     * @param Throwable|null $previous The previous exception.
     */
    public function __construct(string $message, int|null $code = null, Throwable|null $previous = null)
    {
        parent::__construct($message, $code ?? static::DEFAULT_CODE, $previous);
    }
}
