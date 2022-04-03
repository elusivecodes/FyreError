<?php
declare(strict_types=1);

namespace Fyre\Error\Exceptions;

/**
 * ErrorException
 */
class ErrorException extends Exception
{

    /**
     * New ErrorException constructor.
     * @param string $message The message.
     * @param int $code The error code.
     * @param string|null $file The file.
     * @param int|null $line The line.
     * @param Throwable|null $previous The previous exception.
     */
    public function __construct(string $message = null, int $code = null, string|null $file = null, int|null $line = null, Throwable|null $previous = null)
    {
        parent::__construct($message, $code, $previous);

        if ($file) {
            $this->file = $file;
        }

        if ($line) {
            $this->line = $line;
        }
    }

}
