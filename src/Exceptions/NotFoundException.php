<?php
declare(strict_types=1);

namespace Fyre\Error\Exceptions;

/**
 * NotFoundException
 */
class NotFoundException extends HttpException
{

    protected const DEFAULT_CODE = 404;

    protected const DEFAULT_MESSAGE = 'Not Found';

}
