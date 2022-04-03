<?php
declare(strict_types=1);

namespace Fyre\Error\Exceptions;

/**
 * NotAcceptableException
 */
class NotAcceptableException extends HttpException
{

    protected const DEFAULT_CODE = 406;

    protected const DEFAULT_MESSAGE = 'Not Acceptable';

}
