# FyreError

**FyreError** is a free, open-source error handling library for *PHP*.


## Table Of Contents
- [Installation](#installation)
- [Methods](#methods)
- [Middleware](#middleware)
- [Exceptions](#exceptions)
    - [Http Exceptions](#http-exceptions)



## Installation

**Using Composer**

```
composer require fyre/error
```

In PHP:

```php
use Fyre\Error\ErrorHandler;
```


## Methods

**Get Exception**

Get the current *Exception*.

```php
$exception = ErrorHandler::getException();
```

**Get Renderer**

Get the error renderer.

```php
$renderer = ErrorHandler::getRenderer();
```

**Handle**

Handle an *Exception*.

```php
$response = ErrorHandler::handle($exception);
```

**Register**

Register the error handler.

- `$options` is an array containing configuration options.
    - `log` is a boolean indicating whether to log errors, and will default to *false*.
    - `level` is an integer representing the `error_reporting` level.

```php
ErrorHandler::register($options);
```

**Render**

Render an *Exception*.

```php
ErrorHandler::render($exception);
```

**Set Renderer**

Set the error renderer.

- `$renderer` is a *Closure* that accepts an *Exception* as the first argument.

```php
ErrorHandler::setRenderer($renderer);
```

The renderer should return a [*ClientResponse*](https://github.com/elusivecodes/FyreServer#client-responses) or a string.


## Middleware

```php
use Fyre\Error\Middleware\ErrorHandlerMiddleware;
```

```php
$middleware = new ErrorHandlerMiddleware();
```

**Process**

- `$request` is a [*ServerRequest*](https://github.com/elusivecodes/FyreServer#server-requests).
- `$handler` is a [*RequestHandler*](https://github.com/elusivecodes/FyreMiddleware#request-handlers).

```php
$response = $middleware->process($request, $handler);
```

This method will return a [*ClientResponse*](https://github.com/elusivecodes/FyreServer#client-responses).


## Exceptions

Custom exceptions can be created by extending the `Fyre\Error\Exceptions\Exception` class.

- `$message` is a string representing the error message.
- `$code` is a number representing the error code, and will default to *500*.
- `$previous` is an *Exception* representing the previous exception, and will default to *null*.

```php
new Exception($message, $code, $previous);
```


### Http Exceptions

**Bad Request**

400 Bad Request error.

```php
use Fyre\Error\Exceptions\BadRequestException;
```

**Unauthorized**

401 Unauthorized error.

```php
use Fyre\Error\Exceptions\UnauthorizedException;
```

**Forbidden**

403 Forbidden error.

```php
use Fyre\Error\Exceptions\Forbidden;
```

**Not Found**

404 Not Found error.

```php
use Fyre\Error\Exceptions\NotFoundException;
```

**Method Not Allowed**

405 Method Not Allowed error.

```php
use Fyre\Error\Exceptions\MethodNotAllowedException;
```

**Not Acceptable**

406 Not Acceptable error.

```php
use Fyre\Error\Exceptions\NotAcceptableException;
```

**Conflict**

409 Conflict error.

```php
use Fyre\Error\Exceptions\ConflictException;
```

**Gone**

410 Gone error.

```php
use Fyre\Error\Exceptions\GoneException;
```

**Internal Server**

500 Internal Server error.

```php
use Fyre\Error\Exceptions\InternalServerException;
```

**Not Implemented**

501 Not Implemented error.

```php
use Fyre\Error\Exceptions\NotImplementedException;
```

**Service Unavailable**

503 Service Unavailable error.

```php
use Fyre\Error\Exceptions\ServiceUnavailableException;
```