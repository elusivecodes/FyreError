<?php
declare(strict_types=1);

namespace Fyre\Error\Exceptions;

/**
 * BadRequestException
 */
class BadRequestException extends HttpException
{

    protected const DEFAULT_CODE = 400;

    protected const DEFAULT_MESSAGE = 'Bad Request';

}
